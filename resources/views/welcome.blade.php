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
    </div>
</div>
    
@endsection
