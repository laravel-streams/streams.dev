@php
    $siteName = config('app.name', 'Streams');
    $pageTitle = trim($__env->yieldContent('title')) ?: (isset($entry) ? ($entry->title ?? null) : null);
    $fullTitle = ($pageTitle && $pageTitle !== $siteName) ? $pageTitle.' · '.$siteName : $siteName.' · Agentic-first Laravel packages';
    $description = trim($__env->yieldContent('description'))
        ?: ((isset($entry) && ($entry->description ?? null)) ? $entry->description : 'Streams is an agentic-first package system for Laravel and the TALL stack: model your domain in configuration, and people and AI agents build on it predictably.');
@endphp
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ Str::limit(strip_tags($description), 300) }}">

<meta name="theme-color" media="(prefers-color-scheme: light)" content="#fafafa">
<meta name="theme-color" media="(prefers-color-scheme: dark)" content="#0a0a0b">
<meta name="color-scheme" content="light dark">

<link rel="icon" type="image/png" href="/favicon.png">
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
