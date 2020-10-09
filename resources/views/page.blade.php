@extends('layouts/default')
@section('navigation')
@include('partials.navbar')
@endsection
@section('content')


<div class="container mx-auto">
    <div class="o-page-body w-12/12">
        <h1>
            {{ $entry->title }}
        </h1>
        @if ($entry->intro)
        <div class="o-intro">
            {!! Str::markdown($entry->intro) !!}
        </div>
        @endif
        {!! $entry->body !!}
        
    </div>
</div>
@endsection