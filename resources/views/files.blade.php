<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased">

    <div class="mt-8 ml-8">
        <ul>
            @foreach ($stream->entries()->orderBy('path', 'asc')->get() as $entry)
            <li><a href="/files/{{ $entry->id }}">{{ $entry->path }}</a></li>
            @endforeach
        </ul>
    </div>

    @vite(['resources/js/app.js'])

</body>

</html>
