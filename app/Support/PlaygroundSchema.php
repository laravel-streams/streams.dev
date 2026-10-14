<?php

namespace App\Support;

use JsonException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;

class PlaygroundSchema
{
    public const SCHEMA_ID = 'https://streams.dev/schema/catalog.schema.json';

    private const MAX_BYTES = 65536;

    public static function path(): string
    {
        return public_path('schema/catalog.schema.json');
    }

    /**
     * The streams make node for a catalog component: builder class plus defaults.
     *
     * @param  array<string, mixed>  $component
     */
    public static function encodeNode(array $component): string
    {
        return json_encode(
            [
                'builder' => $component['builder'],
                'defaults' => $component['defaults'] ?? [],
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    /**
     * @return array{valid: bool, node: ?array<string, mixed>, errors: list<string>}
     */
    public static function validate(string $json): array
    {
        if (strlen($json) > self::MAX_BYTES) {
            return self::invalid(['The example config is too large to validate.']);
        }

        try {
            $decoded = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return self::invalid(['Invalid JSON: '.$exception->getMessage()]);
        }

        if (! is_object($decoded)) {
            return self::invalid(['/: The example config must be a JSON object.']);
        }

        $schema = json_decode((string) file_get_contents(self::path()));
        $validator = new Validator;
        $validator->setMaxErrors(20);
        $result = $validator->validate($decoded, $schema);

        if (! $result->isValid()) {
            $errors = [];

            foreach ((new ErrorFormatter)->format($result->error()) as $path => $messages) {
                foreach ((array) $messages as $message) {
                    if (is_string($message)) {
                        $errors[] = ($path !== '' ? $path : '/').': '.$message;
                    }
                }
            }

            return self::invalid($errors === [] ? ['/: The example config does not match catalog.schema.json.'] : $errors);
        }

        /** @var array<string, mixed> $node */
        $node = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (! isset($node['builder'], $node['defaults']) || ! is_string($node['builder']) || ! is_array($node['defaults'])) {
            return self::invalid(['/: The example config must be a streams make node.']);
        }

        $builders = array_column(PlaygroundCatalog::components(), 'builder');
        $errors = self::nodeErrors($node, $builders);

        if ($errors !== []) {
            return self::invalid($errors);
        }

        return ['valid' => true, 'node' => $node, 'errors' => []];
    }

    /**
     * @param  array<string, mixed>  $node
     * @param  list<string>  $builders
     * @return list<string>
     */
    private static function nodeErrors(array $node, array $builders): array
    {
        $errors = [];

        if (! in_array($node['builder'], $builders, true)) {
            $errors[] = '/builder: Only catalog builder classes are allowed.';
        }

        $defaults = $node['defaults'];
        $url = $defaults['url'] ?? null;

        if (is_string($url) && ! preg_match('~^(?:https?://|/(?!/)|\#)[^\x00-\x20\x7f\\\\"<>]*$~iD', $url)) {
            $errors[] = '/defaults/url: Use an HTTP(S) link, local path, or fragment.';
        }

        foreach (['components', 'actions', 'columns'] as $key) {
            if (is_array($defaults[$key] ?? null)) {
                foreach ($defaults[$key] as $child) {
                    $errors = [...$errors, ...self::nodeErrors($child, $builders)];
                }
            }
        }

        if (isset($defaults['action'])) {
            $errors = [...$errors, ...self::nodeErrors($defaults['action'], $builders)];
        }

        return $errors;
    }

    /**
     * @param  list<string>  $errors
     * @return array{valid: bool, node: null, errors: list<string>}
     */
    private static function invalid(array $errors): array
    {
        return ['valid' => false, 'node' => null, 'errors' => $errors];
    }
}
