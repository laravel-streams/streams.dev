<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Streams\Ui\Builders\ViewBuilder;
use UnexpectedValueException;

class PlaygroundCatalog
{
    /** @var list<string> */
    private const DEFAULT_KEYS = [
        'name', 'id', 'label', 'placeholder', 'heading', 'description', 'helpText',
        'url', 'icon', 'color', 'value', 'slugify', 'accept', 'options', 'paginated',
        'columns', 'components', 'actions', 'action', 'entries', 'view', 'rows',
    ];

    /** @var list<string> */
    private const STRING_KEYS = [
        'name', 'id', 'label', 'placeholder', 'heading', 'description', 'helpText',
        'url', 'icon', 'color', 'value', 'slugify', 'accept', 'view',
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public static function components(): array
    {
        $catalog = File::json(resource_path('playground/catalog.json'), JSON_THROW_ON_ERROR);
        $components = $catalog['components'] ?? null;

        if (! is_array($components) || ! array_is_list($components) || $components === []) {
            throw new UnexpectedValueException('The playground catalog must contain a non-empty component list.');
        }

        $ids = [];

        foreach ($components as $component) {
            if (! is_array($component)) {
                throw new UnexpectedValueException('Invalid playground catalog component.');
            }

            foreach (['id', 'label', 'group', 'reference', 'builder'] as $key) {
                if (! isset($component[$key]) || ! is_string($component[$key]) || trim($component[$key]) === '') {
                    throw new UnexpectedValueException('Missing playground component '.$key.'.');
                }
            }

            if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $component['id']) || isset($ids[$component['id']])) {
                throw new UnexpectedValueException('Playground component IDs must be unique slugs.');
            }

            if (! str_starts_with($component['reference'], '/docs/ui/')) {
                throw new UnexpectedValueException('Playground references must point to the UI documentation.');
            }

            self::assertBuilder($component['builder']);

            $defaults = $component['defaults'] ?? [];

            if (! is_array($defaults)) {
                throw new UnexpectedValueException('Playground defaults for '.$component['id'].' must be an object.');
            }

            self::assertDefaults($defaults, $component['id']);

            $ids[$component['id']] = true;
        }

        return $components;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $id): ?array
    {
        foreach (self::components() as $component) {
            if ($component['id'] === $id) {
                return $component;
            }
        }

        return null;
    }

    private static function assertBuilder(string $builder): void
    {
        if (! preg_match('/^Streams\\\\Ui\\\\Builders\\\\[A-Za-z\\\\]+$/D', $builder)) {
            throw new UnexpectedValueException('Playground builders must be Streams UI builder classes.');
        }

        if (! class_exists($builder) || ! is_a($builder, ViewBuilder::class, true) || $builder === ViewBuilder::class) {
            throw new UnexpectedValueException('Unknown playground builder ['.$builder.'].');
        }
    }

    /**
     * @param  array<string, mixed>  $defaults
     */
    private static function assertDefaults(array $defaults, string $id): void
    {
        foreach ($defaults as $key => $value) {
            if (! in_array($key, self::DEFAULT_KEYS, true)) {
                throw new UnexpectedValueException('Unknown playground default ['.$key.'] for '.$id.'.');
            }
        }

        foreach (self::STRING_KEYS as $key) {
            if (array_key_exists($key, $defaults) && ! is_string($defaults[$key])) {
                throw new UnexpectedValueException('Playground default ['.$key.'] for '.$id.' must be a string.');
            }
        }

        if (isset($defaults['view']) && ! preg_match('/^playground\.[A-Za-z0-9_.-]+$/D', $defaults['view'])) {
            throw new UnexpectedValueException('Playground default views must use the playground view namespace.');
        }

        if (array_key_exists('paginated', $defaults) && ! is_bool($defaults['paginated'])) {
            throw new UnexpectedValueException('Playground default [paginated] for '.$id.' must be a boolean.');
        }

        if (array_key_exists('rows', $defaults) && ! is_int($defaults['rows'])) {
            throw new UnexpectedValueException('Playground default [rows] for '.$id.' must be an integer.');
        }

        if (array_key_exists('options', $defaults)) {
            if (! is_array($defaults['options'])) {
                throw new UnexpectedValueException('Playground default [options] for '.$id.' must be an object.');
            }

            foreach ($defaults['options'] as $option) {
                if (! is_string($option)) {
                    throw new UnexpectedValueException('Playground options for '.$id.' must be strings.');
                }
            }
        }

        if (array_key_exists('columns', $defaults) && ! is_int($defaults['columns']) && ! is_array($defaults['columns'])) {
            throw new UnexpectedValueException('Playground default [columns] for '.$id.' must be an integer or a list.');
        }

        if (is_array($defaults['columns'] ?? null)) {
            foreach ($defaults['columns'] as $column) {
                self::assertNested($column, $id);
            }
        }

        foreach (['components', 'actions'] as $key) {
            if (! array_key_exists($key, $defaults)) {
                continue;
            }

            if (! is_array($defaults[$key]) || ! array_is_list($defaults[$key])) {
                throw new UnexpectedValueException('Playground default ['.$key.'] for '.$id.' must be a list.');
            }

            foreach ($defaults[$key] as $nested) {
                self::assertNested($nested, $id);
            }
        }

        if (array_key_exists('action', $defaults)) {
            self::assertNested($defaults['action'], $id);
        }

        if (! array_key_exists('entries', $defaults)) {
            return;
        }

        if (! is_array($defaults['entries']) || ! array_is_list($defaults['entries'])) {
            throw new UnexpectedValueException('Playground default [entries] for '.$id.' must be a list.');
        }

        foreach ($defaults['entries'] as $entry) {
            if (! is_array($entry)) {
                throw new UnexpectedValueException('Playground table entries for '.$id.' must be objects.');
            }

            foreach ($entry as $value) {
                if (! is_scalar($value) && $value !== null) {
                    throw new UnexpectedValueException('Playground table entry values for '.$id.' must be scalar.');
                }
            }
        }
    }

    private static function assertNested(mixed $spec, string $id): void
    {
        if (! is_array($spec) || ! isset($spec['builder'], $spec['defaults']) || ! is_string($spec['builder']) || ! is_array($spec['defaults'])) {
            throw new UnexpectedValueException('Invalid nested playground builder for '.$id.'.');
        }

        self::assertBuilder($spec['builder']);
        self::assertDefaults($spec['defaults'], $id);
    }
}
