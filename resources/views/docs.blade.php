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

        <div class="pt-8">
            {!! View::parse(Str::markdown($entry->body)) !!}
        </div>

    </main>

    @vite(['resources/js/app.js'])
    
</body>

</html>
