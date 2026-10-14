@extends('layouts.playground')

@section('title', 'UI Playground')
@section('description', 'Explore the Laravel Streams UI component catalog in the playground.')

@section('navigation')
@include('playground.navigation')
@endsection

@section('content')
<p>Choose a component to render it from the Streams UI package with its catalog defaults.</p>
<p>{{ count($components) }} components are in the catalog. Each page keeps a link to the UI reference.</p>
@endsection
