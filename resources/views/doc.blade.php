@extends('layouts/default')

@section('content')
<div class="flex min-h-screen">
    
    @include('partials.docs.aside')

    <div class="flex-grow pb-10 pr-20">
        
        <h1 class="text-4xl sm:text-6xl lg:text-7xl leading-none font-extrabold tracking-tight text-gray-900 mt-8 mb-8">
            {{ $entry->title }}
        </h1>
        
        @if ($entry->intro)
        <p class="text-2xl tracking-tight mb-10">
            {!! Str::markdown($entry->intro) !!}
        </p>
        @endif

        <div class="doc-body">
            {!! Str::markdown(View::parse($entry->body)) !!}
        </div>
    </div>
</div>
@endsection
