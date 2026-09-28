@props([
    'as' => 'div',
    'strong' => false,
    'flat' => false,
])

<{{ $as }} {{ $attributes->class(['st-glass', 'st-glass--strong' => $strong, 'st-glass--flat' => $flat]) }}>
    {{ $slot }}
</{{ $as }}>
