{{--
    The one site shell. Every view extends this (directly or through layouts.docs).

    Options (pass through @extends('layouts.shell', [...])):
      chrome      bool    Show the header and footer (default true).
      menuButton  bool    Show the mobile docs-menu button in the header.
      bodyClass   string  Extra classes for <body>.
    Sections: title, description, main. Stacks: head, scripts.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    @vite(['resources/js/app.js'])
    @stack('head')
</head>

<body class="flex min-h-screen flex-col bg-page text-fg antialiased {{ $bodyClass ?? '' }}" @isset($bodyData) x-data="{{ $bodyData }}" @endisset>

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-3 focus:z-50 st-btn st-btn--primary st-btn--sm">Skip to content</a>

    @if ($chrome ?? true)
        <x-nav :menu-button="$menuButton ?? false" />
    @endif

    @include('partials.docs-search')

    <main id="main" class="flex-1">
        @yield('main')
    </main>

    @if ($chrome ?? true)
        <x-footer />
    @endif

    @stack('scripts')
</body>

</html>
