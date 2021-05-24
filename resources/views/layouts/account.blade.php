<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    @include('partials/head')
</head>

<body class="flex flex-col min-h-screen">

    @include('partials/header')

    <div id="app" class="flex-grow">
        <main class="xs:w-full w-2/3 mx-auto p-10">
            @yield('content')    
        </main>
    </div>
    
    @include('partials/footer')
    @include('partials/assets')

</body>
</html>
