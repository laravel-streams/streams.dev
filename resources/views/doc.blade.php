@extends('layouts/default')

@section('content')

<div class="grid grid-cols-12 gap-4">
    <div class="col-span-3">
        <aside class="o-aside sticky top-0">
            <p>
                I am a sticky sidebar
            </p>
            <ul x-data="theme()">
                <li @click="onLight">
                    I like the lightness
                </li>
                <li @click="onDark">
                    I like the darkness
                </li>
            </ul>
        </aside>
    </div>
    <div class="col-span-9">
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





@endsection