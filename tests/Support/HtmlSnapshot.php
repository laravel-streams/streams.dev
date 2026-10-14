<?php

namespace Tests\Support;

use PHPUnit\Framework\Assert;

/**
 * Compares rendered playground HTML to a stored snapshot.
 * Set UPDATE_SNAPSHOTS=1 to rewrite tests/snapshots.
 */
final class HtmlSnapshot
{
    public static function assertMatches(string $name, string $html): void
    {
        $normalized = self::normalize($html);
        $path = dirname(__DIR__).'/snapshots/'.$name.'.html';

        if (getenv('UPDATE_SNAPSHOTS') === '1') {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            file_put_contents($path, $normalized);
        }

        Assert::assertFileExists($path, 'Missing snapshot for '.$name.'. Set UPDATE_SNAPSHOTS=1 to create it.');
        Assert::assertSame(file_get_contents($path), $normalized, $name);
    }

    public static function normalize(string $html): string
    {
        $html = str_replace("\r\n", "\n", $html);

        $html = preg_replace('/\s+wire:(?:snapshot|effects|id|key|initial-data|ignore)="[^"]*"/', '', $html) ?? $html;
        $html = preg_replace('/<!--\s*Livewire Component.*?-->/s', '', $html) ?? $html;
        $html = preg_replace('/name="csrf-token" content="[^"]*"/', 'name="csrf-token" content=""', $html) ?? $html;
        $html = preg_replace('/name="_token" value="[^"]*"/', 'name="_token" value=""', $html) ?? $html;
        $html = preg_replace('/value="[^"]*"(\s+name="_token")/', 'value=""$1', $html) ?? $html;

        return rtrim($html)."\n";
    }
}
