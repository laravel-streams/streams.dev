<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    @include('partials/head')
</head>

<body class="theme--{{ request()->segment(1) }} flex flex-col min-h-screen">

    @include('partials/header')

    <div id="app" class="flex-grow">
        @yield('content')
    </div>
    
    @include('partials/footer')
    @include('partials/assets')

</body>
</html>
