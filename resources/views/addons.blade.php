<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased bg-slate-50">

    @include('partials.topbar')
    
    <main class="container mx-auto flex mt-4">
        
        @include('partials.filters')

        <div class="flex-grow">
            {!! View::parse($entry->body) !!}
        </div>

    </main>

    @vite(['resources/js/app.js'])
    
</body>

</html>
