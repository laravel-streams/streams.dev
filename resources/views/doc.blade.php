@extends('layouts/default')

@section('content')
<div class="flex min-h-screen">
    
    @include('partials.docs.aside')

    <div class="pb-10">
        
        <h1 class="text-4xl sm:text-6xl lg:text-7xl leading-none font-extrabold tracking-tight text-gray-900 mt-6 mb-8">
            {{ $entry->title }}
        </h1>
        
        @if ($entry->intro)
        <p class="text-2xl tracking-tight mb-10">
            {!! Str::markdown($entry->intro) !!}
        </p>
        @endif

        {!! Str::markdown(View::parse($entry->body)) !!}
    </div>
</div>
@endsection
