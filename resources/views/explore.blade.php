@extends('layouts/explore')

@section('content')
<div class="flex flex-wrap min-h-screen">
    <main class="xs:w-full w-2/3 mx-auto p-10">
        
        <h1 class="text-4xl sm:text-6xl lg:text-7xl leading-none font-extrabold tracking-tight text-gray-900 mt-10 mb-8 sm:mt-14 sm:mb-10">
            {{ $entry->title }}
        </h1>

        <div class="text-2xl leading-10 mt-10 mb-8">
            {!! Str::markdown(View::parse($entry->body, compact('entry'))) !!}
        </div>

        <div class="mt-10 mb-8">
            @foreach ((array) $entry->links as $link)
            <a href="{{ $link['href'] }}" target="_blank" class="px-6 py-3 rounded-3xl font-bold bg-black hover:bg-gray-800 text-white outline-none focus:outline-none hover:shadow-md transition duration-200 ease-in-out">{{ $link['text'] }}</a>
            @endforeach
        </div>

    </main>
</div>

@endsection
