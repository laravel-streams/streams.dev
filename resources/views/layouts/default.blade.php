<!doctype html>
<html lang="en">

<head>

    @include('partials/head')
</head>

<body>

    @section('navigation')

    @show
    @yield('content')

    @include('partials/assets')

</body>

</html>