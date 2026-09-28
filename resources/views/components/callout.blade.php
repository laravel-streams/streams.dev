@props([
    'type' => 'note',
    'title' => null,
])

<div role="note" {{ $attributes->class(['st-callout', 'st-callout--'.$type]) }}>
    @switch($type)
        @case('warning')
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 7v4m0 3h.01M8.6 3.2L2.3 14.5A1.6 1.6 0 003.7 17h12.6a1.6 1.6 0 001.4-2.5L11.4 3.2a1.6 1.6 0 00-2.8 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @break
        @case('tip')
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 2.5v1.5m5.3.7l-1 1M17.5 10H16M4 10H2.5m3.2-4.3l-1-1M7.5 15h5m-4 2.5h3M10 6a4 4 0 00-2.5 7.1V15h5v-1.9A4 4 0 0010 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @break
        @default
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 9v4.5M10 6.5h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
    @endswitch
    <div class="min-w-0">
        @if ($title)<strong class="st-callout__title">{{ $title }}</strong>@endif
        {{ $slot }}
    </div>
</div>
