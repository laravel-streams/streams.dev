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
    
    <div class="ls-doc w-3/4 flex-grow pb-16 px-16">
        
        <h1 class="text-4xl sm:text-6xl lg:text-5xl leading-none font-extrabold tracking-tight text-gray-900 mt-8 mb-4">
            {{ $package->name }}
        </h1>
        
        @if ($package->description)
        <p class="text-xl tracking-tight mb-4 mt-4 opacity-40">{{ $package->description }}</p>
        @endif

        @foreach($package->categories as $category)
        <a href="/packages/category/{{ $category }}" class="text-sm rounded-sm px-1 py-0.5 bg-black text-white">{{ $category }}</a>
        @endforeach
    
        <ul class="mt-4">
            @if ($package->docs)
            <li><a href="{{ $package->docs }}" target="_blank">Documentation 🔗</a></li>
            @endif
            @if ($package->repository)
            <li><a href="{{ $package->repository }}" target="_blank">Repository 👀</a></li>
            @endif
        </ul>

        <h4 class="mt-4 mb-2 text-lg font-bold">Installation</h4>

        <input type="text" class="px-2 rounded-sm w-72" readonly value="composer require {{ $package->name }}">

        @if ($package->readme)
        <div class="doc-body mt-8">
            {!! $package->decorate('readme')->markdown() !!}
        </div>
        @endif
    
    </div>
    </div>
@endsection
