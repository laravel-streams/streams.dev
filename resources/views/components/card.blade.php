@props([
    'href' => null,
    'title' => null,
    'eyebrow' => null,
    'glass' => false,
])

@php
    $classes = 'st-card'.($glass ? ' st-card--glass' : '');
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    @if ($eyebrow)
    <span class="st-card__eyebrow">{{ $eyebrow }}</span>
    @endif
    @if ($title)
    <span @class(['st-card__title', 'mt-1.5' => $eyebrow, 'pr-6' => $href])>{{ $title }}</span>
    @endif
    @if ($slot->isNotEmpty())
    <div @class(['st-card__body flex-1' => $title])>{{ $slot }}</div>
    @endif
    @isset($footer)
    <div class="mt-5">{{ $footer }}</div>
    @endisset
    @if ($href)
    <svg class="st-card__arrow" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M5 11L11 5m0 0H6m5 0v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    @endif
</{{ $tag }}>
