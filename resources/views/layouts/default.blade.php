<!doctype html>
<html lang="en">

<head>
    @include('partials/head')
</head>

<body class="o-mode">

    @section('navigation')

    @show
    @yield('content')

    @include('partials/assets')

</body>

</html>