@extends('layouts/default')
@section('navigation')
@include('partials.navbar')
@endsection
@section('content')

<div class="container mx-auto">
    <div class="flex flex-wrap min-h-screen mx-auto">
        @include('partials.docs.aside')

        <div class="o-doc-body w-8/12">



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
</div>
@endsection