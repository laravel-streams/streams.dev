@props(['status' => null])

@php
    $label = is_string($status) ? (\App\Support\DocsRegistry::STATUSES[$status] ?? null) : null;
@endphp

@if ($label)
    <span {{ $attributes->class(['st-pill'])->merge(['data-docs-status' => $status, 'aria-label' => 'Feature status: '.$label]) }}>{{ $label }}</span>
@endif
