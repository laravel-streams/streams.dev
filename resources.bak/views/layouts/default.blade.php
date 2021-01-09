<!doctype html>
<html lang="en">

<head>
    @include('partials/head')
</head>

<body class="o-mode">

    @section('navigation')
    @show

    @yield('content')

    
    
    @section('mobile-navigation')
    
    @show
    @include('partials/assets')
    

</body>

</html>