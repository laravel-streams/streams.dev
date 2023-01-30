<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased bg-gray-500 p-24">

    @livewireStyles

    @livewire('form', [
        'stream' => 'docs',
    ])

    @livewireScripts

    @vite(['resources/js/app.js'])
    
</body>

</html>
