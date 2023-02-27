@extends('layouts/default')

@section('content')

<style>
    body {
        background: var(--ui-color-light);
    }
    input[type="search"] {
        background: var(--ui-color-white);
    }
</style>


<div class="flex">

    @include('partials.packages.aside')
    
    <div class="ls-doc__content w-3/4 flex-grow pb-16 px-16">
    
        <h1 class="text-4xl sm:text-6xl lg:text-5xl leading-none font-extrabold tracking-tight text-gray-900 mt-8 mb-4">
            {{ ucfirst(Request::segment(3)) }}
        </h1>
        
        {{-- @if ($entry->intro)
        <p class="text-xl tracking-tight mb-5 opacity-40">{{ $entry->intro }}</p>
        @endif --}}
    
        <div class="flex flex-wrap -ml-2 -mr-2">
        @foreach(Streams::packages()->where('categories', 'contains', Request::segment(3))->get() as $package)
        <div class="w-1/2 p-2 flex">
            <a href="/packages/{{ $package->name }}" class="bg-white w-full rounded-sm p-4 border-2 border-transparent hover:border-black">
                <h3 class="text-xl font-bold">{{ $package->name }}</h3>
                @foreach($package->categories as $key => $value)
                <span class="text-xs rounded-sm px-1 py-0.5 bg-black text-white">{{ $value }}</span>
                @endforeach
                <p class="text-sm text-gray-600 mt-2">{{ $package->description }}</p>
            </a>
        </div>
        @endforeach
        </div>
    
    </div>

</div>
@endsection
