@extends('layouts/default')
@section('navigation')
<div id="blurryscroll" aria-hidden="true"></div>
<div class="o-navbar">
    <div class="container mx-auto">
        <a href="/docs/introduction">home</a>
    </div>
</div>
@endsection
@section('content')
<div class="flex flex-wrap min-h-screen mx-auto" style="max-width:1500px;">
    @include('partials.docs.aside')

    <div class="o-doc-body">
        <h1>
            {{ $entry->title }}
        </h1>
        @if ($entry->intro)
        {!! Str::markdown($entry->intro) !!}
        @endif
        {!! Str::markdown(View::parse($entry->body)) !!}
    </div>
    @include('partials.docs.toc')
</div>
@endsection
