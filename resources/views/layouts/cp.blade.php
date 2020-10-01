<!doctype html >
<html class="no-js o-theme--compacts" lang="en" >

<head>
    
    @include('partials/head')
</head>

<body>
    @section('navigation')
    Default
    @show
    @yield('content')    
    
    @include('partials/assets')
    
</body>

</html>
