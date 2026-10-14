<div class="flex flex-wrap items-center gap-2">
    @if ($icon = $column->getIcon())
        <x-ui::icon :icon="$icon" class="h-5 w-5" />
    @endif
    <x-ui::badge :color="$column->getColor() ?: 'gray'">{{ $column->getLabel() }}</x-ui::badge>
    @if (filled($value = $column->getValue()))
        <span>{{ $value }}</span>
    @endif
</div>
