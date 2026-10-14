@extends('layouts.playground')

@section('title', $component['label'].' · UI Playground')
@section('description', 'Render the '.$component['label'].' Streams UI component with its catalog defaults.')

@section('heading', $component['label'])
@section('lead', 'Rendered from the Streams UI package with catalog defaults.')

@section('navigation')
@include('playground.navigation')
@endsection

@section('content')
<p><a href="{{ $component['reference'] }}">UI reference</a></p>
@if ($gap)
<div data-playground-gap="{{ $component['id'] }}" role="alert">
    <p>{{ $component['label'] }} did not render with its catalog defaults. The failure was logged as a playground gap.</p>
</div>
@else
<div data-playground-preview="{{ $component['id'] }}">
    {!! $preview !!}
</div>
@endif
@endsection
