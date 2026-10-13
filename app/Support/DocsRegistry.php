<?php

namespace App\Support;

class DocsRegistry
{
    public const STATUSES = [
        'planned' => 'Planned',
        'partial' => 'Partial',
        'implemented' => 'Implemented',
    ];

    public const PACKAGES = ['core', 'ui', 'api', 'sdk', 'testing', 'client', 'site', 'all'];

    public const EDITORIAL_STATUSES = ['draft', 'review', 'ready', 'deprecated'];

    /** @return list<string> */
    public static function errors(array $frontmatter): array
    {
        $errors = [];

        foreach (['status' => array_keys(self::STATUSES), 'package' => self::PACKAGES, 'editorial_status' => self::EDITORIAL_STATUSES] as $field => $values) {
            if (! in_array($frontmatter[$field] ?? null, $values, true)) {
                $errors[] = $field.' is required and must be one of: '.implode(', ', $values).'.';
            }
        }

        if (array_key_exists('tracking_task', $frontmatter)) {
            $task = $frontmatter['tracking_task'];
            if (! ((is_int($task) && $task > 0) || (is_string($task) && preg_match('/^[1-9][0-9]*$/D', $task)))) {
                $errors[] = 'tracking_task must be a positive decimal integer Operations task id.';
            }
        } elseif (in_array($frontmatter['status'] ?? null, ['partial', 'implemented'], true)) {
            $errors[] = 'tracking_task is required unless status is planned.';
        }

        return $errors;
    }

    /** @return list<string> */
    public function files(): array
    {
        $files = glob(base_path('streams/data/docs/*.md')) ?: [];

        foreach (DocsNav::packages() as $package => $ignored) {
            $files = array_merge($files, glob(DocsNav::directory($package).'/*.md') ?: []);
        }

        sort($files);

        return $files;
    }
}
