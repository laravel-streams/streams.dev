@extends('layouts/default')

@section('content')
<div class="flex flex-wrap min-h-screen">
    <main class="w-main p-8">
        <h1>
            {{ $entry->title }}
            @if ($entry->intro)
            <small>{!! Str::markdown($entry->intro) !!}</small>
            @endif
        </h1>

        {!! Str::markdown(View::parse($entry->body, compact('entry'))) !!}
    </main>
</div>

@endsection
