@extends('layouts/default')
@section('navigation')
@include('partials.navbar')
@endsection
@section('content')

<div class="flex flex-wrap min-h-screen mx-auto" style="max-width:1500px;">
    @include('partials.docs.aside')

    <div class="o-doc-body">



        @if($entry->intro)

        @include('partials.docs.intro')

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