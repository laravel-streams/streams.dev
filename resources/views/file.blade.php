<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased">

    <pre class="mt-8 ml-8">{{ $entry->toJson(128) }}</pre>

    @vite(['resources/js/app.js'])
    
</body>

</html>
