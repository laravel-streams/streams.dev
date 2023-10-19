<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased bg-gray-50">

    @include('partials.topbar')

    @if (App::environment() == 'local')
    <div class="absolute top-1 left-2">
        <a href="vscode://file{{ base_path('streams/data/' . $entry->stream()->id . '/' . $entry->id . '.html') }}" class="hover:text-gray-700 text-gray-400 text-sm outline-none focus:outline-none transition duration-200 ease-in-out">Edit this page.</a>
    </div>
    @endif
    
    {!! View::parse($entry->body) !!}

    @vite(['resources/js/app.js'])
    
</body>

</html>
