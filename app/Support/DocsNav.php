<?php

namespace App\Support;

use JsonException;

/**
 * Site navigation for package reference. The prose lives in
 * docs/packages/{name}; this map only points at those files.
 */
class DocsNav
{
    /**
     * @return array<string, array{label: string, stream: string, groups: list<array{label: string, pages: list<string>}>}>
     */
    public static function packages(): array
    {
        try {
            $decoded = json_decode(file_get_contents(base_path('docs/nav.json')), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new JsonException('docs/nav.json is not valid JSON: '.$exception->getMessage(), 0, $exception);
        }

        return is_array($decoded) ? $decoded : [];
    }

    public static function directory(string $package): string
    {
        return base_path('docs/packages/'.$package);
    }

    /**
     * Markdown page ids in the package folder, sorted by filename.
     *
     * @return list<string>
     */
    public static function files(string $package): array
    {
        $ids = [];

        foreach (glob(static::directory($package).'/*.md') ?: [] as $file) {
            $ids[] = basename($file, '.md');
        }

        sort($ids);

        return $ids;
    }

    /**
     * Page ids named by the nav map, in map order.
     *
     * @return list<string>
     */
    public static function listed(string $package): array
    {
        $ids = [];

        foreach (static::packages()[$package]['groups'] ?? [] as $group) {
            foreach ($group['pages'] ?? [] as $id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Markdown files the nav map does not name. They stay reachable.
     *
     * @return list<string>
     */
    public static function unlisted(string $package): array
    {
        return array_values(array_diff(static::files($package), static::listed($package)));
    }
}
