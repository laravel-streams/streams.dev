<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

@php
    $siteName = config('app.name', 'Streams');
    $defaultDescription = 'Streams is an agentic-first Laravel package system for data modeling, admin UI, REST APIs, and developer tooling.';

    $pageEntry = isset($entry) && is_object($entry) ? $entry : null;
    $pageStream = $pageEntry && method_exists($pageEntry, 'stream') ? $pageEntry->stream()?->handle : null;
    $docsPackages = ['core_docs' => 'Core', 'ui_docs' => 'UI', 'api_docs' => 'API', 'sdk_docs' => 'SDK', 'testing_docs' => 'Testing', 'client_docs' => 'Client'];

    $pageTitle = $pageEntry?->title;
    $pageDescription = trim((string) ($pageEntry?->description ?? ''));
    if ($pageStream === 'pages' && $pageEntry?->id === 'addon' && Request::route('vendor')) {
        $addon = Streams::entries('packages')->where('composer.name', Request::route('vendor').'/'.Request::route('name'))->first();
        $pageTitle = $addon ? $addon->name.' · Addons' : $pageTitle;
        $pageDescription = $addon?->composer?->description ?? $pageDescription;
    }
    if ($pageTitle && isset($docsPackages[$pageStream])) {
        $pageTitle .= ' · '.$docsPackages[$pageStream];
    }
    $pageTitle = $pageTitle && $pageTitle !== $siteName ? $pageTitle.' · '.$siteName : $siteName;

    $pageDescription = $pageDescription ?: $defaultDescription;
    $isDocsPage = $pageStream === 'docs' || isset($docsPackages[$pageStream]);
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<link rel="canonical" href="{{ url()->current() }}">
@if ($isDocsPage)
<link rel="alternate" type="text/markdown" href="{{ url()->current() }}.md">
@endif

<link rel="icon" type="image/png" href="/favicon.png" />
