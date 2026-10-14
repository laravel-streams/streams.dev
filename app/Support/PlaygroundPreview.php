<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;
use Streams\Ui\Builders\Actions\Action;
use Streams\Ui\Builders\Inputs\EditorInput;
use Streams\Ui\Builders\Tables\Columns\Column;
use Streams\Ui\Builders\Tables\Table;
use Streams\Ui\Builders\ViewBuilder;
use Throwable;
use UnexpectedValueException;

class PlaygroundPreview
{
    /** @var list<string> */
    private const STRING_METHODS = [
        'label', 'placeholder', 'heading', 'description', 'helpText',
        'url', 'icon', 'color', 'value', 'slugify', 'accept',
    ];

    private ?PlaygroundHost $host = null;

    /**
     * @param  array<string, mixed>  $component
     * @return array{html: ?string, gap: ?string}
     */
    public static function render(array $component): array
    {
        try {
            $defaults = $component['defaults'] ?? [];

            if (! is_array($defaults) || ! isset($component['builder']) || ! is_string($component['builder'])) {
                throw new UnexpectedValueException('Playground component is missing a builder.');
            }

            $preview = new self;
            $preview->shareErrorBag();
            $builder = $preview->build($component['builder'], $defaults);
            $html = $builder instanceof EditorInput && ! isset($defaults['view'])
                ? Livewire::mount(PlaygroundEditor::class, [
                    'name' => $defaults['name'] ?? 'content',
                    'id' => $defaults['id'] ?? $builder->getId(),
                    'value' => $defaults['value'] ?? '',
                ])
                : $builder->toHtml();

            return ['html' => $html, 'gap' => null];
        } catch (Throwable $exception) {
            $id = is_string($component['id'] ?? null) ? $component['id'] : 'unknown';

            Log::warning('Playground render gap.', [
                'component' => $id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return ['html' => null, 'gap' => $id];
        }
    }

    /**
     * @param  array<string, mixed>  $defaults
     */
    private function build(string $builder, array $defaults, int $depth = 0): ViewBuilder
    {
        if ($depth > 4) {
            throw new UnexpectedValueException('Playground defaults are nested too deeply.');
        }

        if (! class_exists($builder) || ! is_a($builder, ViewBuilder::class, true)) {
            throw new UnexpectedValueException('Unknown playground builder ['.$builder.'].');
        }

        $name = $defaults['name'] ?? $defaults['id'] ?? 'preview';

        if (! is_string($name) || $name === '') {
            throw new UnexpectedValueException('Playground builders need a name.');
        }

        $instance = is_a($builder, Table::class, true)
            ? $builder::make($this->host(), $name)
            : $builder::make($name);

        if (method_exists($instance, 'livewire')) {
            $instance->livewire($this->host());
        }

        if (isset($defaults['view']) && is_string($defaults['view'])) {
            $instance->view($defaults['view']);
        }

        foreach (self::STRING_METHODS as $method) {
            if (isset($defaults[$method]) && is_string($defaults[$method]) && method_exists($instance, $method)) {
                $instance->{$method}($defaults[$method]);
            }
        }

        if (isset($defaults['rows']) && is_int($defaults['rows']) && method_exists($instance, 'rows')) {
            $instance->rows($defaults['rows']);
        }

        if (isset($defaults['options']) && is_array($defaults['options']) && method_exists($instance, 'options')) {
            $instance->options($defaults['options']);
        }

        if (isset($defaults['components']) && is_array($defaults['components']) && method_exists($instance, 'components')) {
            $instance->components($this->nested($defaults['components'], $depth));
        }

        if (isset($defaults['actions']) && is_array($defaults['actions']) && method_exists($instance, 'actions')) {
            $instance->actions($this->nested($defaults['actions'], $depth));
        }

        if (isset($defaults['action']) && is_array($defaults['action']) && method_exists($instance, 'action')) {
            $action = $this->build($defaults['action']['builder'], $defaults['action']['defaults'] ?? [], $depth + 1);

            if (! $action instanceof Action) {
                throw new UnexpectedValueException('Playground action defaults must build an action.');
            }

            $instance->action($action);
        }

        if ($instance instanceof Table) {
            $columns = [];

            foreach ($defaults['columns'] ?? [] as $column) {
                $built = $this->build($column['builder'], $column['defaults'] ?? [], $depth + 1);

                if (! $built instanceof Column) {
                    throw new UnexpectedValueException('Playground table columns must be column builders.');
                }

                $columns[] = $built;
            }

            $instance->columns($columns);
            $instance->paginated((bool) ($defaults['paginated'] ?? false));
            $instance->setEntries($this->entries($defaults['entries'] ?? []));

            return $instance;
        }

        if (isset($defaults['columns']) && is_int($defaults['columns']) && method_exists($instance, 'columns')) {
            $instance->columns($defaults['columns']);
        }

        if (array_key_exists('paginated', $defaults) && method_exists($instance, 'paginated')) {
            $instance->paginated((bool) $defaults['paginated']);
        }

        return $instance;
    }

    /**
     * @param  list<array<string, mixed>>  $specs
     * @return list<ViewBuilder>
     */
    private function nested(array $specs, int $depth): array
    {
        return array_map(
            fn (array $spec): ViewBuilder => $this->build($spec['builder'], $spec['defaults'] ?? [], $depth + 1),
            $specs,
        );
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function entries(array $rows): Collection
    {
        if ($rows === []) {
            $rows = [['id' => '1']];
        }

        return collect($rows)->map(fn (array $row): object => (object) $row);
    }

    private function shareErrorBag(): void
    {
        if (! array_key_exists('errors', view()->getShared())) {
            view()->share('errors', new ViewErrorBag);
        }
    }

    private function host(): PlaygroundHost
    {
        return $this->host ??= new PlaygroundHost;
    }
}
