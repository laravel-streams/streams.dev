@extends('layouts/default')

@section('content')
<style>
    .documentation__content {
        color: #cccccc;
    }
</style>

<div class="documentation__content bg-black">
    <h1>
        {{ $entry->title }}
        @if ($entry->intro)
            <small>{!! Str::markdown($entry->intro) !!}</small>
        @endif
    </h1>
    
    {!! Str::markdown($entry->body) !!}
</div>
    
@endsection
