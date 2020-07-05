@extends('layouts/default')

@section('content')

<div class="h-screen flex justify-center align-items-center bg-black">
    <div class="flex flex-col self-center">
        
        <div>
            <pre class="brand-ascii text-center text-xs mb-8">
   _____________  _______   __  _______
  / __/_  __/ _ \/ __/ _ | /  |/  / __/
 _\ \  / / / , _/ _// __ |/ /|_/ /\ \  
/___/ /_/ /_/|_/___/_/ |_/_/  /_/___/  
                                                     </pre>
                                                     
        </div>

        {{-- <p class="font-mono text-xs text-white text-center">
            {{ Arr::random([
                'A Development Cult',
                'A Development Stack',
                'A Development Methodology',
                'Idoimatic Bliss',
                'A Higher Language',
            ]) }}
        </p> --}}

        <p class="font-mono text-center">
            <a href="https://github.com/anomalylabs/streams" target="_blank" class="btn-outline-primary transition duration-300 ease-in-out focus:outline-none focus:shadow-outline border border-purple-700 hover:bg-purple-700 text-purple-700 hover:text-white font-normal py-2 px-4 rounded">GitHub</a>
            <a href="/docs/installation" class="btn-outline-primary transition duration-300 ease-in-out focus:outline-none focus:shadow-outline border border-purple-700 hover:bg-purple-700 text-purple-700 hover:text-white font-normal py-2 px-4 rounded">Docs</a>
        </p>
    </div>
</div>
    
@endsection
