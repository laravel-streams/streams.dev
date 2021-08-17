@extends('layouts/default')

@section('content')
<div class="flex flex-wrap min-h-screen">
    <main class="w-full mx-auto">
        {!! View::parse($entry->body, compact('entry'))->render() !!}

        {{-- <div class="flex mt-12">
            <a href="https://github.com/laravel-streams/streams.dev/blob/master/streams/data/pages/{{$entry->id}}.md" target="_blank" class="hover:text-gray-700 text-gray-400 text-sm outline-none focus:outline-none transition duration-200 ease-in-out ml-4">Improve this page.</a>
        </div> --}}
    </main>
</div>
@endsection
