@props([
    'variant' => 'default',
    'href' => null,
    'dot' => false,
])

@php
    $classes = 'st-pill'.($variant !== 'default' ? ' st-pill--'.$variant : '');
    $tag = $href ? 'a' : 'span';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)<span class="st-pill__dot" aria-hidden="true"></span>@endif
    {{ $slot }}
</{{ $tag }}>
