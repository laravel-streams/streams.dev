@extends('layouts/default')
@section('navigation')
<div id="blurryscroll" aria-hidden="true"></div>
<div class="o-navbar @if($entry->intro) o-navbar__has-intro @endif">
    <div class="container mx-auto">
        <a href="/docs/introduction">home</a>
    </div>
</div>
@endsection
@section('content')
@if($entry->intro)
@include('partials.docs.intro')
@endif
<div class="flex flex-wrap min-h-screen mx-auto" style="max-width:1500px;">
    @include('partials.docs.aside')

    <div class="o-doc-body">

        {{-- l1N$Z2Y78wnKoU@M --}}

        @if($entry->intro)
        
        @else
        <h1>
            {{ $entry->title }}
        </h1>
        @endif
        {!! Str::markdown(View::parse($entry->body)) !!}
    </div>
    @include('partials.docs.toc')
</div>
@endsection