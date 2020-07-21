<!doctype html >
<html class="no-js o-theme--compacts" lang="en" >

<head>
    @include('partials/head')
</head>

<body>
    <x-icon-wheel class="c-icon--settings" x-data="settings()" x-init="init" @click="onClick"/>
    @yield('content')    
    @include('partials/assets')
    
</body>

</html>
