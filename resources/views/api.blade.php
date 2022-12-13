<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased">

    <script>
        // client.streams.get();
    </script>

    @vite(['resources/js/app.js'])
    
</body>

</html>
