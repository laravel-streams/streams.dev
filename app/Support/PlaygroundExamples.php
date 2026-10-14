<?php

namespace App\Support;

use InvalidArgumentException;
use Streams\Core\Support\Facades\Streams;

/**
 * Curated UI Playground examples, one data file per catalog builder type.
 *
 * Builder ids come from streams-sdk resources/catalog/catalog.json (ui.builders).
 * Table, form, and action examples read the seeded people, posts, and orders streams.
 * Rendering is a read-only preview: it does not save entries or submit actions.
 */
class PlaygroundExamples
{
    public const STREAMS = ['people', 'posts', 'orders'];

    public const BOUND_TYPES = ['table', 'form', 'action'];

    /**
     * @return list<string>
     */
    public static function builderTypes(): array
    {
        $decoded = self::readJson(resource_path('playground/builder-types.json'));
        $builders = $decoded['builders'] ?? null;

        if (! is_array($builders)) {
            throw new InvalidArgumentException('Playground builder types are missing.');
        }

        return array_values(array_map(
            fn (mixed $builder): string => is_array($builder) ? (string) ($builder['id'] ?? '') : '',
            $builders
        ));
    }

    /**
     * Builder ids from the installed SDK catalog, when that package publishes one.
     *
     * @return list<string>|null
     */
    public static function installedCatalogTypes(): ?array
    {
        $path = base_path('vendor/streams/sdk/resources/catalog/catalog.json');

        if (! is_file($path)) {
            return null;
        }

        $builders = self::readJson($path)['ui']['builders'] ?? null;

        if (! is_array($builders)) {
            return null;
        }

        return array_values(array_map(
            fn (mixed $builder): string => is_array($builder) ? (string) ($builder['id'] ?? '') : '',
            $builders
        ));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        $examples = [];

        foreach (self::builderTypes() as $type) {
            $examples[$type] = self::get($type);
        }

        return $examples;
    }

    /**
     * @return array<string, mixed>
     */
    public static function get(string $type): array
    {
        $path = resource_path('playground/examples/'.$type.'.json');

        if (! is_file($path)) {
            throw new InvalidArgumentException("Playground example [{$type}] is missing.");
        }

        $example = self::readJson($path);
        self::assertExample($example, $type);

        return $example;
    }

    /**
     * @param  array<string, mixed>  $example
     */
    public static function render(array $example): string
    {
        self::assertExample($example, (string) ($example['type'] ?? ''));

        return view('playground.example', self::present($example))->render();
    }

    /**
     * @return array<string, mixed>
     */
    private static function readJson(string $path): array
    {
        $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            throw new InvalidArgumentException("Expected a JSON object in [{$path}].");
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $example
     */
    private static function assertExample(array $example, string $type): void
    {
        foreach (['id', 'type', 'label'] as $key) {
            if (! is_string($example[$key] ?? null) || $example[$key] === '') {
                throw new InvalidArgumentException("Playground example [{$type}] is missing [{$key}].");
            }
        }

        if ($type !== '' && $example['type'] !== $type) {
            throw new InvalidArgumentException("Playground example [{$example['id']}] has type [{$example['type']}], expected [{$type}].");
        }

        if (! is_array($example['config'] ?? null)) {
            throw new InvalidArgumentException("Playground example [{$example['id']}] is missing config.");
        }

        $stream = $example['stream'] ?? null;

        if ($stream !== null && ! in_array($stream, self::STREAMS, true)) {
            throw new InvalidArgumentException("Playground example [{$example['id']}] uses unknown stream [{$stream}].");
        }

        if (in_array($example['type'], self::BOUND_TYPES, true) && ! is_string($stream)) {
            throw new InvalidArgumentException("Playground example [{$example['id']}] must name a seeded stream.");
        }
    }

    /**
     * @param  array<string, mixed>  $example
     * @return array<string, mixed>
     */
    private static function present(array $example): array
    {
        $base = [
            'type' => $example['type'],
            'label' => $example['label'],
            'description' => (string) ($example['description'] ?? ''),
            'stream' => $example['stream'] ?? null,
            'mode' => 'summary',
            'columns' => [],
            'rows' => [],
            'fields' => [],
            'action' => [
                'label' => '',
                'name' => '',
                'confirm' => false,
                'entry' => '',
            ],
            'items' => [],
            'details' => [],
        ];

        $type = $example['type'];

        if (in_array($type, ['table', 'column'], true)) {
            return self::presentTable($base, $example);
        }

        if ($type === 'form' || $type === 'field' || str_starts_with($type, 'input-')) {
            return self::presentForm($base, $example);
        }

        if ($type === 'action') {
            return self::presentAction($base, $example);
        }

        if (in_array($type, ['list', 'calendar', 'navigation', 'menu', 'panel', 'repeater', 'resource'], true)) {
            return self::presentItems($base, $example);
        }

        return self::presentSummary($base, $example);
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $example
     * @return array<string, mixed>
     */
    private static function presentTable(array $base, array $example): array
    {
        $config = $example['config'];
        $columns = $example['type'] === 'column'
            ? [[
                'handle' => (string) ($config['handle'] ?? ''),
                'label' => (string) ($config['label'] ?? $example['label']),
            ]]
            : array_map(fn (array $column): array => [
                'handle' => (string) ($column['handle'] ?? ''),
                'label' => (string) ($column['label'] ?? $column['handle'] ?? ''),
            ], $config['columns'] ?? []);

        $rows = [];

        foreach (self::rows((string) $example['stream']) as $record) {
            $cells = [];

            foreach ($columns as $column) {
                $cells[$column['handle']] = self::display($record[$column['handle']] ?? '');
            }

            $rows[] = [
                'id' => (string) ($record['id'] ?? ''),
                'cells' => $cells,
            ];
        }

        $base['mode'] = 'table';
        $base['columns'] = $columns;
        $base['rows'] = $rows;

        return $base;
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $example
     * @return array<string, mixed>
     */
    private static function presentForm(array $base, array $example): array
    {
        $entry = isset($example['entry'], $example['stream'])
            ? self::entry((string) $example['stream'], (string) $example['entry'])
            : null;

        $definitions = $example['type'] === 'form'
            ? ($example['config']['fields'] ?? [])
            : [array_merge($example['config'], ['input' => $example['config']['input'] ?? $example['type']])];

        $base['mode'] = 'form';
        $base['fields'] = array_map(
            fn (array $definition): array => self::field($definition, $entry),
            $definitions
        );

        return $base;
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $example
     * @return array<string, mixed>
     */
    private static function presentAction(array $base, array $example): array
    {
        $entry = self::entry((string) $example['stream'], (string) ($example['entry'] ?? ''));
        $config = $example['config'];

        $base['mode'] = 'action';
        $base['action'] = [
            'label' => (string) ($config['label'] ?? $example['label']),
            'name' => (string) ($config['handle'] ?? $config['name'] ?? 'action'),
            'confirm' => (bool) ($config['confirm'] ?? false),
            'entry' => self::display($entry['number'] ?? $entry['title'] ?? $entry['name'] ?? $entry['id'] ?? ''),
        ];

        return $base;
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $example
     * @return array<string, mixed>
     */
    private static function presentItems(array $base, array $example): array
    {
        $items = [];
        $config = $example['config'];
        $type = $example['type'];

        if ($type === 'list') {
            foreach (self::rows((string) $example['stream']) as $record) {
                $items[] = [
                    'label' => self::display($record['name'] ?? $record['id'] ?? ''),
                    'meta' => self::display($record['email'] ?? ''),
                ];
            }
        } elseif ($type === 'calendar') {
            $dateField = (string) ($config['date_field'] ?? 'placed_at');

            foreach (self::rows((string) $example['stream']) as $record) {
                $items[] = [
                    'label' => self::display($record['number'] ?? $record['title'] ?? $record['id'] ?? ''),
                    'meta' => self::display($record[$dateField] ?? ''),
                ];
            }
        } elseif ($type === 'resource') {
            $items[] = ['label' => 'Table', 'meta' => (string) ($config['table'] ?? '')];
            $items[] = ['label' => 'Form', 'meta' => (string) ($config['form'] ?? '')];
            $items[] = ['label' => 'Records', 'meta' => (string) count(self::rows((string) $example['stream']))];
        } elseif ($type === 'repeater') {
            foreach ($config['rows'] ?? [] as $row) {
                $items[] = [
                    'label' => (string) ($row['label'] ?? ''),
                    'meta' => self::display($row['meta'] ?? ''),
                ];
            }
        } else {
            foreach ($config['items'] ?? [] as $item) {
                $items[] = [
                    'label' => (string) ($item['label'] ?? ''),
                    'meta' => (string) ($item['meta'] ?? ''),
                ];
            }
        }

        $base['mode'] = 'items';
        $base['items'] = $items;

        return $base;
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $example
     * @return array<string, mixed>
     */
    private static function presentSummary(array $base, array $example): array
    {
        $details = [];

        foreach ($example['config'] as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $details[] = [
                    'label' => (string) $key,
                    'value' => self::display($value),
                ];
            }
        }

        $base['mode'] = 'summary';
        $base['details'] = $details;

        return $base;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>|null  $entry
     * @return array<string, mixed>
     */
    private static function field(array $definition, ?array $entry): array
    {
        $input = (string) ($definition['input'] ?? 'text');
        $handle = (string) ($definition['handle'] ?? 'value');
        $spec = self::controlSpec($input);
        $value = $definition['value'] ?? null;

        if ($entry !== null && array_key_exists($handle, $entry) && ! array_key_exists('value', $definition)) {
            $value = $entry[$handle];
        }

        if ($spec['html_type'] === 'password') {
            $value = '';
        }

        $value = self::normalizeValue($spec, $value);

        if (isset($definition['options_from'])) {
            $options = self::optionsFrom(
                (string) $definition['options_from'],
                (string) ($definition['option_label'] ?? 'name')
            );
        } else {
            $options = [];

            foreach ($definition['options'] ?? [] as $optionValue => $optionLabel) {
                $options[] = [
                    'value' => (string) $optionValue,
                    'label' => (string) $optionLabel,
                ];
            }
        }

        return [
            'handle' => $handle,
            'label' => (string) ($definition['label'] ?? $handle),
            'kind' => $spec['kind'],
            'html_type' => $spec['html_type'],
            'value' => $value,
            'options' => $options,
            'required' => (bool) ($definition['required'] ?? false),
            'multiple' => $spec['multiple'],
            'checked' => $spec['kind'] === 'checkbox' && filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'switch' => $spec['switch'],
            'min' => $definition['min'] ?? $spec['min'],
            'max' => $definition['max'] ?? $spec['max'],
            'step' => $definition['step'] ?? $spec['step'],
            'accept' => $definition['accept'] ?? $spec['accept'],
        ];
    }

    /**
     * @return array{kind: string, html_type: string, multiple: bool, switch: bool, min: ?string, max: ?string, step: ?string, accept: ?string}
     */
    private static function controlSpec(string $input): array
    {
        $name = str_starts_with($input, 'input-') ? substr($input, strlen('input-')) : $input;
        $spec = [
            'kind' => 'input',
            'html_type' => 'text',
            'multiple' => false,
            'switch' => false,
            'min' => null,
            'max' => null,
            'step' => null,
            'accept' => null,
        ];

        return match ($name) {
            'textarea' => array_merge($spec, ['kind' => 'textarea']),
            'select' => array_merge($spec, ['kind' => 'select']),
            'multiselect' => array_merge($spec, ['kind' => 'select', 'multiple' => true]),
            'radio' => array_merge($spec, ['kind' => 'radio']),
            'checkbox' => array_merge($spec, ['kind' => 'checkbox', 'html_type' => 'checkbox']),
            'toggle' => array_merge($spec, ['kind' => 'checkbox', 'html_type' => 'checkbox', 'switch' => true]),
            'file' => array_merge($spec, ['kind' => 'file', 'html_type' => 'file']),
            'image' => array_merge($spec, ['kind' => 'file', 'html_type' => 'file', 'accept' => 'image/*']),
            'email' => array_merge($spec, ['html_type' => 'email']),
            'url' => array_merge($spec, ['html_type' => 'url']),
            'password' => array_merge($spec, ['html_type' => 'password']),
            'number' => array_merge($spec, ['html_type' => 'number']),
            'integer' => array_merge($spec, ['html_type' => 'number', 'step' => '1']),
            'decimal' => array_merge($spec, ['html_type' => 'number', 'step' => '0.01']),
            'date' => array_merge($spec, ['html_type' => 'date']),
            'datetime', 'datetime-local' => array_merge($spec, ['html_type' => 'datetime-local']),
            'time' => array_merge($spec, ['html_type' => 'time']),
            'range' => array_merge($spec, ['html_type' => 'range']),
            'color' => array_merge($spec, ['html_type' => 'color']),
            default => $spec,
        };
    }

    /**
     * @param  array{kind: string, html_type: string, multiple: bool, switch: bool, min: ?string, max: ?string, step: ?string, accept: ?string}  $spec
     */
    private static function normalizeValue(array $spec, mixed $value): mixed
    {
        if ($spec['multiple']) {
            if ($value === null || $value === '') {
                return [];
            }

            return is_array($value) ? array_map(strval(...), $value) : [(string) $value];
        }

        if ($spec['html_type'] === 'datetime-local' && is_string($value) && $value !== '') {
            return substr(str_replace(' ', 'T', $value), 0, 16);
        }

        if ($spec['html_type'] === 'time' && is_string($value) && preg_match('/(\d{2}:\d{2})/', $value, $matches)) {
            return $matches[1];
        }

        if ($spec['kind'] === 'checkbox') {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value === null) {
            return '';
        }

        if (is_array($value)) {
            return self::display($value);
        }

        return (string) $value;
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private static function optionsFrom(string $stream, string $labelKey): array
    {
        if (! in_array($stream, self::STREAMS, true)) {
            throw new InvalidArgumentException("Playground options cannot be loaded from [{$stream}].");
        }

        $options = [];

        foreach (self::rows($stream) as $record) {
            $options[] = [
                'value' => (string) ($record['id'] ?? ''),
                'label' => self::display($record[$labelKey] ?? $record['id'] ?? ''),
            ];
        }

        return $options;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function rows(string $stream): array
    {
        $rows = [];

        foreach (Streams::entries($stream)->get() as $entry) {
            $rows[] = self::entryArray($entry);
        }

        return $rows;
    }

    /**
     * @return array<string, mixed>
     */
    private static function entry(string $stream, string $id): array
    {
        $entry = Streams::entries($stream)->find($id);

        if (! $entry) {
            throw new InvalidArgumentException("Entry [{$id}] was not found on stream [{$stream}].");
        }

        return self::entryArray($entry);
    }

    /**
     * @return array<string, mixed>
     */
    private static function entryArray(mixed $entry): array
    {
        if (is_array($entry)) {
            return $entry;
        }

        if (is_object($entry) && method_exists($entry, 'toArray')) {
            $data = $entry->toArray();

            if (! is_array($data)) {
                throw new InvalidArgumentException('Playground entry could not be read.');
            }

            if (! array_key_exists('id', $data) && isset($entry->id)) {
                $data['id'] = $entry->id;
            }

            return $data;
        }

        if (is_object($entry)) {
            return get_object_vars($entry);
        }

        throw new InvalidArgumentException('Playground entry could not be read.');
    }

    private static function display(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value === null) {
            return '';
        }

        if (is_array($value)) {
            return implode(', ', array_map(fn (mixed $item): string => self::display($item), $value));
        }

        return (string) $value;
    }
}
