@extends('layouts.playground')

@section('title', $component['label'].' · UI Playground')
@section('description', 'Render the '.$component['label'].' Streams UI component with its catalog defaults.')

@section('heading', $component['label'])
@section('lead', 'Read-only PHP for this component, with a live preview.')

@section('navigation')
@include('playground.navigation')
@endsection

@section('content')
<p><a href="{{ $component['reference'] }}">UI reference</a></p>
@include('playground.php-example')
<div data-playground-stage="{{ $component['id'] }}">
    @include('livewire.playground-stage', [
        'componentId' => $component['id'],
        'label' => $component['label'],
        'preview' => $preview,
        'gap' => $gap,
    ])
</div>
@endsection
