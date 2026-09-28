@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'arrow' => false,
    'block' => false,
    'icon' => false,
])

@php
    $classes = collect([
        'st-btn',
        'st-btn--'.$variant,
        $size !== 'md' ? 'st-btn--'.$size : null,
        $block ? 'st-btn--block' : null,
        $icon ? 'st-btn--icon' : null,
    ])->filter()->implode(' ');

    $isExternal = $href && Str::startsWith($href, ['http://', 'https://']) && ! Str::startsWith($href, url('/'));
@endphp

@if ($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes] + ($isExternal ? ['rel' => 'noopener'] : [])) }}>
    {{ $slot }}
    @if ($arrow)
    <svg class="st-btn__arrow" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3.5 8h9m0 0L8.5 4m4 4l-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    @endif
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
    @if ($arrow)
    <svg class="st-btn__arrow" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3.5 8h9m0 0L8.5 4m4 4l-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    @endif
</button>
@endif
