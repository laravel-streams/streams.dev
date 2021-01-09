@extends('layouts/explore')

@section('content')
<div class="flex flex-wrap min-h-screen">
    <main class="xs:w-full w-2/3 mx-auto p-10">
        
        <h1 class="text-4xl sm:text-6xl lg:text-7xl leading-none font-extrabold tracking-tight text-gray-900 mt-10 sm:mt-14 sm:mb-10">
            {{ $entry->title }}
        </h1>

        <div class="text-2xl leading-10 mt-14">
            {!! Str::markdown(View::parse($entry->body, compact('entry'))) !!}
        </div>

        @if ($entry->menu)
        <div class="mt-16">
            @foreach ((array) $entry->menu as $item)
            @switch(Arr::get($item, 'type', 'btn'))
                @case('link')
                    <a href="{{ $item['href'] }}" class="px-6 py-3 mb-4 block rounded-3xl font-bold text-2xl text-black outline-none focus:outline-none hover:shadow-md transition duration-200 ease-in-out">{{ $item['text'] }}</a>
                    @break
                @case('secondary')
                    <a href="{{ $item['href'] }}" class="px-6 py-3 mb-4 block rounded-3xl font-bold text-2xl bg-gray-200 hover:bg-gray-300 text-black outline-none focus:outline-none hover:shadow-md transition duration-200 ease-in-out">{{ $item['text'] }}</a>
                    @break
                @default
                    <a href="{{ $item['href'] }}" class="px-6 py-3 mb-4 block rounded-3xl font-bold text-2xl bg-black hover:bg-gray-800 text-white outline-none focus:outline-none hover:shadow-md transition duration-200 ease-in-out">{{ $item['text'] }}</a>
            @endswitch
            @endforeach
        </div>    
        @endif

        @if ($entry->links)
        <div class="mt-16 space-x-4">
            @foreach ((array) $entry->links as $link)
            @switch(Arr::get($link, 'type', 'btn'))
                @case('link')
                    <a href="{{ $link['href'] }}" class="px-3 py-3 font-bold text-2xl text-black hover:text-gray-400 transition duration-200 ease-in-out">{{ $link['text'] }}</a>
                    @break
                @case('secondary')
                    <a href="{{ $link['href'] }}" class="px-6 py-3 rounded-3xl font-bold text-2xl bg-gray-200 hover:bg-gray-300 text-black outline-none focus:outline-none hover:shadow-md transition duration-200 ease-in-out">{{ $link['text'] }}</a>                    
                    @break
                @default
                    <a href="{{ $link['href'] }}" class="px-6 py-3 rounded-3xl font-bold text-2xl bg-black hover:bg-gray-800 text-white outline-none focus:outline-none hover:shadow-md transition duration-200 ease-in-out">{{ $link['text'] }}</a>                    
            @endswitch
            @endforeach
        </div>
        @endif

    </main>
</div>

@endsection
