@extends('layouts.playground')

@section('title', $component['label'].' · UI Playground')
@section('description', 'Render the '.$component['label'].' Streams UI component with its catalog defaults.')

@section('heading', $component['label'])
@section('lead', 'Edit the example config to re-render this component on the page.')

@section('navigation')
@include('playground.navigation')
@endsection

@section('content')
<p><a href="{{ $component['reference'] }}">UI reference</a></p>
<livewire:playground-config
    :component-id="$component['id']"
    :preview="$preview"
    :gap="$gap"
    :key="$component['id']"
/>
<div data-playground-stage="{{ $component['id'] }}">
    @include('livewire.playground-stage', [
        'componentId' => $component['id'],
        'label' => $component['label'],
        'preview' => $preview,
        'gap' => $gap,
    ])
</div>
@endsection
