@php
    $siteName = config('app.name', 'Streams');
    $defaultDescription = 'Streams is an agentic-first Laravel package system for data modeling, admin UI, REST APIs, and developer tooling.';

    $pageEntry = isset($entry) && is_object($entry) ? $entry : null;
    $pageStream = $pageEntry && method_exists($pageEntry, 'stream') ? $pageEntry->stream()?->handle : null;
    $docsPackages = ['core_docs' => 'Core', 'ui_docs' => 'UI', 'api_docs' => 'API', 'sdk_docs' => 'SDK', 'testing_docs' => 'Testing', 'client_docs' => 'Client'];

    // Entry front matter is the title and description. A layout may @section('title')
    // or @section('description') when it has no entry (explore passes a short label).
    $yieldedTitle = trim($__env->yieldContent('title'));
    $yieldedDescription = trim($__env->yieldContent('description'));

    $pageTitle = $yieldedTitle !== '' ? $yieldedTitle : $pageEntry?->title;
    $pageDescription = $yieldedDescription !== '' ? $yieldedDescription : trim((string) ($pageEntry?->description ?? ''));

    if ($pageStream === 'pages' && $pageEntry?->id === 'addon' && Request::route('vendor')) {
        $addon = Streams::entries('packages')->where('composer.name', Request::route('vendor').'/'.Request::route('name'))->first();
        $pageTitle = $addon ? $addon->name.' · Addons' : $pageTitle;
        $pageDescription = $addon?->composer?->description ?? $pageDescription;
    }
    // Package pages keep a unique front matter title ("Core: Introduction"); the browser
    // title uses the short nav_title plus the package ("Introduction · Core").
    if ($pageTitle && isset($docsPackages[$pageStream])) {
        $pageTitle = ($yieldedTitle !== '' ? $yieldedTitle : ($pageEntry?->nav_title ?: $pageEntry?->title)).' · '.$docsPackages[$pageStream];
    }
    $pageTitle = $pageTitle && $pageTitle !== $siteName ? $pageTitle.' · '.$siteName : $siteName;

    $pageDescription = $pageDescription ?: $defaultDescription;
    $isDocsPage = $pageStream === 'docs' || isset($docsPackages[$pageStream]);
@endphp
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<link rel="canonical" href="{{ url()->current() }}">
@if ($isDocsPage)
<link rel="alternate" type="text/markdown" href="{{ url()->current() }}.md">
@endif

<meta name="theme-color" media="(prefers-color-scheme: light)" content="#fafafa">
<meta name="theme-color" media="(prefers-color-scheme: dark)" content="#0a0a0b">
<meta name="color-scheme" content="light dark">

<link rel="icon" type="image/png" href="/favicon.png" />
<link rel="manifest" href="/manifest.json">

{{-- Apply the saved (or system) color scheme before first paint. --}}
<script>
    (function () {
        var root = document.documentElement;
        var query = window.matchMedia('(prefers-color-scheme: dark)');
        var read = function () { try { return localStorage.getItem('st-theme') || 'system'; } catch (e) { return 'system'; } };
        var apply = function (mode) {
            var dark = mode === 'dark' || (mode !== 'light' && query.matches);
            root.classList.toggle('dark', dark);
            root.dataset.themeMode = mode;
        };
        window.StreamsTheme = {
            get: read,
            set: function (mode) {
                try { mode === 'system' ? localStorage.removeItem('st-theme') : localStorage.setItem('st-theme', mode); } catch (e) {}
                apply(mode);
            },
        };
        apply(read());
        query.addEventListener('change', function () { apply(read()); });
    })();
</script>
