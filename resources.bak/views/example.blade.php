@extends('layouts/default')

@section('content')

<div class="">
    <div class="grid grid-cols-12 gap-4">
        <main class="o-main">
            <h1>
                {{ $entry->title }}
            </h1>
            @if ($entry->intro)
                <div class="o-intro">
                    {!! Str::markdown($entry->intro) !!}
                </div>
            @endif
            {!! Str::markdown(View::parse($entry->body, compact('entry'))) !!}
        </main>
    </div>
</div>

@endsection
