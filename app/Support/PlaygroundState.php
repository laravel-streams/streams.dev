<?php

namespace App\Support;

use JsonException;

/**
 * Share links carry playground settings only.
 * Builder classes stay in the catalog and are never written into the URL.
 */
class PlaygroundState
{
    private const MAX_BYTES = 65536;

    private const CLASS_NAME = '/[A-Za-z_][A-Za-z0-9_]*(?:\\\\+[A-Za-z_][A-Za-z0-9_]*)+/';

    private const CODE = '/(<\?(?:php|=)?|<\s*script\b|\beval\s*\(|\{!!|@php\b|\bnamespace\s+[A-Za-z_\\\\]|\bfunction\s+[A-Za-z_(]|\bclass\s+[A-Z][A-Za-z0-9_]*|::class\b)/i';

    /** @var array<string, string>|null */
    private static ?array $builderIds = null;

    /**
     * @param  array<string, mixed>  $component
     * @return array{builder: string, defaults: array<string, mixed>}
     */
    public static function nodeForRequest(array $component, mixed $state): array
    {
        if ($state === null || $state === '') {
            return [
                'builder' => $component['builder'],
                'defaults' => $component['defaults'] ?? [],
            ];
        }

        if (! is_string($state)) {
            throw new PlaygroundStateRejected('A share link carries settings only.');
        }

        return self::decode($component['id'], $state);
    }

    /**
     * @param  array{builder: string, defaults: array<string, mixed>}  $node
     */
    public static function link(string $componentId, array $node): string
    {
        $json = self::encode($componentId, $node);
        $decoded = self::decode($componentId, $json);

        if ($decoded !== $node) {
            throw new PlaygroundStateRejected('Playground state did not round-trip.');
        }

        $url = route('playground.component', ['component' => $componentId, 'state' => $json], absolute: false);
        self::assertLink($url);

        return $url;
    }

    /**
     * @param  array{builder?: mixed, defaults?: mixed}  $node
     */
    public static function encode(string $componentId, array $node): string
    {
        $component = PlaygroundCatalog::find($componentId);

        if ($component === null) {
            throw new PlaygroundStateRejected('Unknown playground component.');
        }

        $keys = array_keys($node);
        sort($keys);

        if (
            $keys !== ['builder', 'defaults']
            || $node['builder'] !== $component['builder']
            || ! is_array($node['defaults'])
        ) {
            throw new PlaygroundStateRejected('A share link carries settings only, not a builder class.');
        }

        $json = json_encode(
            self::encodeDefaults($node['defaults']),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        self::assertShareText($json);

        if (strlen($json) > self::MAX_BYTES) {
            throw new PlaygroundStateRejected('Playground state is too large.');
        }

        return $json;
    }

    /**
     * @return array{builder: string, defaults: array<string, mixed>}
     */
    public static function decode(string $componentId, string $state): array
    {
        $component = PlaygroundCatalog::find($componentId);

        if ($component === null) {
            throw new PlaygroundStateRejected('Unknown playground component.');
        }

        if (strlen($state) > self::MAX_BYTES) {
            throw new PlaygroundStateRejected('Playground state is too large.');
        }

        self::assertShareText($state);

        try {
            $settings = json_decode($state, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new PlaygroundStateRejected('Playground state must be a JSON settings object.');
        }

        if (! is_array($settings) || array_is_list($settings)) {
            throw new PlaygroundStateRejected('Playground state must be a JSON settings object.');
        }

        $node = [
            'builder' => $component['builder'],
            'defaults' => self::decodeDefaults($settings),
        ];

        $validated = PlaygroundSchema::validate(json_encode(
            $node,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        ));

        if (! $validated['valid'] || ! is_array($validated['node'])) {
            throw new PlaygroundStateRejected('Playground settings do not match the catalog schema.');
        }

        /** @var array{builder: string, defaults: array<string, mixed>} $decoded */
        $decoded = $validated['node'];

        return $decoded;
    }

    public static function assertLink(string $url): void
    {
        self::assertShareText(rawurldecode($url));
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    private static function encodeDefaults(array $defaults, int $depth = 0): array
    {
        if ($depth > 6) {
            throw new PlaygroundStateRejected('Playground settings are nested too deeply.');
        }

        $encoded = [];

        foreach ($defaults as $key => $value) {
            if (! is_string($key) || self::forbiddenKey($key)) {
                throw new PlaygroundStateRejected('A share link carries settings only, not a class name or code.');
            }

            if (self::isNodeList($key, $value)) {
                $encoded[$key] = array_map(
                    fn (mixed $node): array => self::encodeNested($node, $depth),
                    $value
                );

                continue;
            }

            if ($key === 'action') {
                $encoded[$key] = self::encodeNested($value, $depth);

                continue;
            }

            self::assertSettingValue($value);
            $encoded[$key] = $value;
        }

        return $encoded;
    }

    /**
     * @return array{component: string, defaults: array<string, mixed>}
     */
    private static function encodeNested(mixed $node, int $depth): array
    {
        $keys = is_array($node) ? array_keys($node) : [];
        sort($keys);

        if (
            $keys !== ['builder', 'defaults']
            || ! is_string($node['builder'])
            || ! is_array($node['defaults'])
        ) {
            throw new PlaygroundStateRejected('A share link cannot name a builder class.');
        }

        $id = self::builders()[$node['builder']] ?? null;

        if ($id === null) {
            throw new PlaygroundStateRejected('A share link cannot name a builder class.');
        }

        return [
            'component' => $id,
            'defaults' => self::encodeDefaults($node['defaults'], $depth + 1),
        ];
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private static function decodeDefaults(array $settings, int $depth = 0): array
    {
        if ($depth > 6) {
            throw new PlaygroundStateRejected('Playground settings are nested too deeply.');
        }

        $defaults = [];

        foreach ($settings as $key => $value) {
            if (! is_string($key) || self::forbiddenKey($key)) {
                throw new PlaygroundStateRejected('A share link carries settings only, not a class name or code.');
            }

            if (self::isNodeList($key, $value)) {
                $defaults[$key] = array_map(
                    fn (mixed $node): array => self::decodeNested($node, $depth),
                    $value
                );

                continue;
            }

            if ($key === 'action') {
                $defaults[$key] = self::decodeNested($value, $depth);

                continue;
            }

            self::assertSettingValue($value);
            $defaults[$key] = $value;
        }

        return $defaults;
    }

    /**
     * @return array{builder: string, defaults: array<string, mixed>}
     */
    private static function decodeNested(mixed $node, int $depth): array
    {
        $keys = is_array($node) ? array_keys($node) : [];
        sort($keys);

        if (
            $keys !== ['component', 'defaults']
            || ! is_string($node['component'])
            || ! is_array($node['defaults'])
            || ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $node['component'])
        ) {
            throw new PlaygroundStateRejected('A share link carries settings only, not a class name or code.');
        }

        $component = PlaygroundCatalog::find($node['component']);

        if ($component === null) {
            throw new PlaygroundStateRejected('Unknown playground component.');
        }

        return [
            'builder' => $component['builder'],
            'defaults' => self::decodeDefaults($node['defaults'], $depth + 1),
        ];
    }

    private static function isNodeList(string $key, mixed $value): bool
    {
        return in_array($key, ['components', 'actions', 'columns'], true)
            && is_array($value)
            && array_is_list($value);
    }

    private static function forbiddenKey(string $key): bool
    {
        return in_array($key, ['builder', 'class', 'code', 'component'], true);
    }

    private static function assertSettingValue(mixed $value): void
    {
        if (is_string($value)) {
            self::assertShareText($value);

            return;
        }

        if (is_int($value) || is_float($value) || is_bool($value) || $value === null) {
            return;
        }

        if (! is_array($value)) {
            throw new PlaygroundStateRejected('Playground settings must be JSON values.');
        }

        foreach ($value as $key => $item) {
            if (is_string($key)) {
                self::assertShareText($key);
            }

            self::assertSettingValue($item);
        }
    }

    public static function assertShareText(string $value): void
    {
        if (preg_match(self::CLASS_NAME, $value) || preg_match(self::CODE, $value)) {
            throw new PlaygroundStateRejected('A share link carries settings only, not a class name or code.');
        }
    }

    /**
     * @return array<string, string>
     */
    private static function builders(): array
    {
        if (self::$builderIds !== null) {
            return self::$builderIds;
        }

        $map = [];

        foreach (PlaygroundCatalog::components() as $component) {
            $builder = $component['builder'];

            if (isset($map[$builder])) {
                throw new PlaygroundStateRejected('A share link cannot name a builder class.');
            }

            $map[$builder] = $component['id'];
        }

        return self::$builderIds = $map;
    }
}
