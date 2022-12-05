<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')

    @livewireStyles
</head>

<body class="antialiased">

    @livewire('form', [
        'stream' => 'users',
        'entry' => '8457398d-06a6-41fa-8f33-4bd5e5e5ff16',
    ])

    @livewireScripts
    @vite(['resources/js/app.js'])

</body>

</html>
