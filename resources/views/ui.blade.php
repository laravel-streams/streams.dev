<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>UI Test</title>
</head>
<body class="antialiased bg-gray-50">
    @include('partials.topbar')
    <main class="container mx-auto py-24 px-8">
        <h1 class="text-4xl font-bold">UI Test</h1>
        <p class="mt-4 text-gray-600">Streams UI sandbox route.</p>
    </main>
    @vite(['resources/js/app.js'])
</body>
</html>
