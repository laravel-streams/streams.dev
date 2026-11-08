<?php

namespace App\Support;

use Streams\Ui\Builders\Tables\Table;

/**
 * Renders read-only PHP builder code for a catalog component. The output is
 * derived from the catalog entry and the builder classes it names: imports come
 * from the class names, and a fluent call is emitted only when the class has it.
 */
class PlaygroundPhpExample
{
    /** Fluent string methods, in the order the preview applies them. */
    private const STRING_METHODS = [
        'view', 'label', 'placeholder', 'heading', 'description', 'helpText',
        'url', 'icon', 'color', 'value', 'slugify', 'accept',
    ];

    /**
     * @param  array<string, mixed>  $component
     */
    public static function filename(array $component): string
    {
        return class_basename($component['builder']).'Example.php';
    }

    /**
     * @param  array<string, mixed>  $component
     */
    public static function render(array $component): string
    {
        $imports = [];
        $expression = self::expression($component['builder'], $component['defaults'] ?? [], 0, $imports);

        $imports = array_values(array_unique($imports));
        sort($imports);

        $uses = implode("\n", array_map(fn (string $class): string => 'use '.$class.';', $imports));

        return "<?php\n\n".$uses."\n\n$".lcfirst(class_basename($component['builder'])).' = '.$expression.";\n";
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  list<string>  $imports
     */
    private static function expression(string $builder, array $defaults, int $depth, array &$imports): string
    {
        $imports[] = $builder;
        $short = class_basename($builder);
        $name = self::string($defaults['name'] ?? $defaults['id'] ?? 'preview');
        $first = is_a($builder, Table::class, true) ? '$this, '.$name : $name;

        $calls = [];

        foreach (self::STRING_METHODS as $method) {
            if (isset($defaults[$method]) && is_string($defaults[$method]) && method_exists($builder, $method)) {
                $calls[] = $method.'('.self::string($defaults[$method]).')';
            }
        }

        if (isset($defaults['rows']) && method_exists($builder, 'rows')) {
            $calls[] = 'rows('.(int) $defaults['rows'].')';
        }

        if (isset($defaults['options']) && method_exists($builder, 'options')) {
            $calls[] = 'options('.var_export($defaults['options'], true).')';
        }

        foreach (['components', 'actions'] as $key) {
            if (isset($defaults[$key]) && method_exists($builder, $key)) {
                $calls[] = $key.'('.self::nestedList($defaults[$key], $depth + 1, $imports).')';
            }
        }

        if (isset($defaults['action']) && method_exists($builder, 'action')) {
            $calls[] = 'action('.self::expression($defaults['action']['builder'], $defaults['action']['defaults'] ?? [], $depth + 1, $imports).')';
        }

        if (is_array($defaults['columns'] ?? null) && method_exists($builder, 'columns')) {
            $calls[] = 'columns('.self::nestedList($defaults['columns'], $depth + 1, $imports).')';
        } elseif (isset($defaults['columns']) && is_int($defaults['columns']) && method_exists($builder, 'columns')) {
            $calls[] = 'columns('.$defaults['columns'].')';
        }

        if (array_key_exists('paginated', $defaults) && method_exists($builder, 'paginated')) {
            $calls[] = 'paginated('.($defaults['paginated'] ? 'true' : 'false').')';
        }

        $pad = str_repeat('    ', $depth + 1);
        $out = $short.'::make('.$first.')';

        foreach ($calls as $call) {
            $out .= "\n".$pad.'->'.$call;
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>  $specs
     * @param  list<string>  $imports
     */
    private static function nestedList(array $specs, int $depth, array &$imports): string
    {
        $pad = str_repeat('    ', $depth + 1);
        $items = array_map(
            fn (array $spec): string => $pad.self::expression($spec['builder'], $spec['defaults'] ?? [], $depth + 1, $imports).',',
            $specs,
        );

        return "[\n".implode("\n", $items)."\n".str_repeat('    ', $depth).']';
    }

    private static function string(mixed $value): string
    {
        return var_export((string) $value, true);
    }
}
