<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use UnexpectedValueException;

class PlaygroundCatalog
{
    /** @return list<array{id: string, label: string, group: string, reference: string}> */
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

            foreach (['id', 'label', 'group', 'reference'] as $key) {
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

            $ids[$component['id']] = true;
        }

        return $components;
    }
}
