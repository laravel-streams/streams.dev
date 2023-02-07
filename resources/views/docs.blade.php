<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased bg-gray-50">

    @include('partials.topbar')
    
    <main class="container mx-auto flex mt-4">
        
        @if (Request::segment(2))
        @include('partials.sidebar')
        @endif

        <div class="pt-1">
            
            <h1 class="text-6xl font-extrabold mt-6">{{ $entry->title }}</h1>
            <p class="text-gray-500 mt-4 text-xl">{{ $entry->description }}</p>

            <div class="documentation__toc my-8"></div>

            <div class="documentation-content">
                {!! View::parse(Str::markdown($entry->body)) !!}
            </div>
        </div>

    </main>

    @vite(['resources/js/app.js'])
    
</body>

</html>
