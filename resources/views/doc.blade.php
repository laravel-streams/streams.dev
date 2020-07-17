@extends('layouts/default')

@section('content')

<div class="container">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3">
            <aside class="o-aside sticky top-0">
    
                <ul x-data="theme()" x-init="init">
                    <li @click="onLight" class="cursor-pointer">
                        I like the lightness
                    </li>
                    <li @click="onDark" class="cursor-pointer">
                        I like the darkness
                    </li>
                </ul>
              
                <ul>
                    @foreach (Streams::entries('docs')->orderBy('sort', 'asc')->get() as $item)
                    <li>
                        <a href="/docs/{{ $item->id }}">{{ $item->title }}</a>
                    </li>
                    @endforeach
                </ul>
            </aside>
        </div>
        <div class="col-span-8 col-start-5">
            <main class="o-main">
                <h1>
                    {{ $entry->title }}
                </h1>
                @if ($entry->intro)
                    <div class="o-intro">
                        {!! Str::markdown($entry->intro) !!}
                    </div>
                @endif
                {!! Str::markdown($entry->body) !!}
            </main>
        </div>
    </div>
</div>

@endsection
