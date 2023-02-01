<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased bg-gray-50">

    @include('partials.topbar')
    
    {!! View::parse($entry->body) !!}

    @vite(['resources/js/app.js'])
    
</body>

</html>
