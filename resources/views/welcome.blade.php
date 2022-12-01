<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')

    @php
    $support = [
    'content',
    'app',
    'api',
    'product',
    'developers',
    'team',
    'research',
    'prototyping',
    'making',
    ];
    @endphp
</head>

<body class="pb-24">

    <section class="pt-24">
        <div class="logo">
            {!! Assets::contents('public::img/logo.svg') !!}
        </div>
    </section>

    <section class="container mx-auto max-w-7xl text-center pt-24 px-8">
        <div class="text-7xl font-bold uppercase">The Laravel platform for your {{ $support[array_rand($support)] }}
        </div>
    </section>

    <section class="container mx-auto max-w-7xl text-center pt-24 px-8">
        <p class="text-xl">Laravel Streams provides an optimized foundation and workflow for <span
                class="font-bold">Laravel development</span>.</p>
    </section>

    <section class="container mx-auto max-w-3xl text-center pt-24 px-8">
        <pre class="bg-black text-white p-4 rounded-sm"><code>composer create-project streams/streams:1.0.x-dev

cd streams

php artisan serve</code>
</pre>
    </section>

    <section class="container mx-auto max-w-7xl text-center pt-24 px-8">
        <a href="/docs" class="relative px-6 py-2 group">
            <span
                class="absolute inset-0 w-full h-full transition duration-300 ease-out transform translate-x-1 translate-y-1 bg-black group-hover:-translate-x-0 group-hover:-translate-y-0"></span>
            <span class="absolute inset-0 w-full h-full bg-white border-2 border-black"></span>
            <span class="relative text-black">Documentation</span>
        </a>
    </section>

</body>

</html>
