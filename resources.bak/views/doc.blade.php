@extends('layouts/default')
@section('navigation')
@include('partials.navbar')
@endsection
@section('mobile-navigation')
@include('partials/mobile-menu')
@endsection



@section('content')

<div class="fixed inline-block bottom-0 right-0 pb-2 pr-2 text-xs opacity-25">
    {{ number_format(microtime(true) - Request::server('REQUEST_TIME_FLOAT'), 2) . ' s' }}&nbsp;|&nbsp;
    @php
        $size = memory_get_usage(true);

        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        echo round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    @endphp
</div>

<div class="container mx-auto">
    <div class="flex flex-wrap min-h-screen mx-auto">
        @include('partials.docs.aside')

        <div class="o-doc-body w-full md:w-9/12 xl:w-8/12">
            @if($entry->intro)

            @include('partials.docs.intro')

            @else
            <h1>
                {{ $entry->title }}
            </h1>
            @endif
            {!! Str::markdown(View::parse($entry->body)) !!}
            @include('partials.code-bottom-anim')
        </div>
        @include('partials.docs.toc')
    </div>
</div>
@endsection
