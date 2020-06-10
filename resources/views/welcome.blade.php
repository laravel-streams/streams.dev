@extends('layouts/default')

@section('content')

<div class="h-screen flex justify-center align-items-center bg-black">
    <div style="max-width: 300px;" class="flex flex-col self-center">
        
        <div>
            {!! img('public::img/streams.png') !!}
        </div>

        {{-- <p class="font-mono text-xs text-white text-center">
            {{ Arr::random([
                'A Development Cult',
                'A Development Stack',
                'A Development Methodology',
            ]) }}
        </p> --}}

        <p class="btn-outline-primary transition duration-300 ease-in-out focus:outline-none focus:shadow-outline border border-purple-700 hover:bg-purple-700 text-purple-700 hover:text-white font-normal py-2 px-4 rounded">
            <a href="https://github.com/anomalylabs/streams" target="_blank">GitHub</a>
        </p>
    </div>
</div>
    
@endsection
