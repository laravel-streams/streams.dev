@php
    use App\Support\ExploreTree;

    $crumbs = ExploreTree::breadcrumbs($entry);
    $menu = ExploreTree::choices($entry, 'menu');
    $options = ExploreTree::choices($entry, 'options');
    $links = ExploreTree::choices($entry, 'links');
    $isRoot = count($crumbs) === 1;
    $variants = ['primary' => 'primary', 'secondary' => 'secondary', 'link' => 'link'];
@endphp

@extends('layouts.shell')

@section('title', $isRoot ? 'Explore' : ExploreTree::label($entry).' · Explore')

@push('head')
    <link rel="alternate" type="text/markdown" href="{{ url('explore/'.$entry->id.'.md') }}" title="{{ $entry->title }} (Markdown)">
    <link rel="alternate" type="application/json" href="{{ url('explore/'.$entry->id.'.json') }}" title="{{ $entry->title }} (JSON)">
    <script type="speculationrules">{"prerender":[{"where":{"href_matches":"/explore/*"},"eagerness":"moderate"}]}</script>
@endpush

@section('main')
<div class="st-ambient">
    <div class="explore-stage mx-auto max-w-5xl px-6 pt-10 pb-28 sm:pt-14">

        @unless ($isRoot)
        <nav aria-label="Explore path">
            <ol class="explore-crumbs">
                @foreach ($crumbs as $crumb)
                <li class="flex items-center gap-1">
                    @if ($loop->last)
                        <span aria-current="page">{{ $crumb['label'] }}</span>
                    @else
                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                        <svg class="h-3.5 w-3.5 text-fg-faint" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @endif
                </li>
                @endforeach
            </ol>
        </nav>
        @else
        <x-pill variant="glass" dot>Explore · {{ ExploreTree::all()->count() }} stops, about two minutes</x-pill>
        @endunless

        <h1 class="explore-title mt-10 max-w-4xl animate-rise sm:mt-14">{{ $entry->title }}</h1>

        @if (trim((string) $entry->body) !== '')
        <div class="explore-body mt-10 max-w-3xl animate-rise [animation-delay:60ms] sm:mt-12">
            {!! \App\Support\DocumentationMarkdown::toHtml($entry->body) !!}
        </div>
        @endif

        @if ($menu)
        <div class="explore-choices mt-14 grid max-w-2xl gap-3">
            @foreach ($menu as $item)
            <x-button :href="$item['href']" :variant="$variants[$item['type']]" size="xl" :block="$item['type'] !== 'link'" :target="$item['target']" :arrow="$item['type'] === 'primary'" class="{{ $item['type'] === 'link' ? 'justify-start' : '' }}">{{ $item['text'] }}</x-button>
            @endforeach
        </div>
        @endif

        @if ($options)
        <div class="explore-choices mt-14 flex flex-wrap items-center gap-3">
            @foreach ($options as $item)
            <x-button :href="$item['href']" :variant="$variants[$item['type']]" size="xl" :target="$item['target']" :arrow="$item['type'] === 'primary'">{{ $item['text'] }}</x-button>
            @endforeach
        </div>
        @endif

        @if ($links)
        <div class="mt-16 flex flex-wrap items-center gap-2 animate-fade [animation-delay:200ms]">
            <span class="mr-1 text-sm text-fg-muted">More resources</span>
            @foreach ($links as $item)
            <x-pill :href="$item['href']" :target="$item['target']">{{ $item['text'] }}</x-pill>
            @endforeach
        </div>
        @endif

        <p class="mt-24 flex flex-wrap items-center gap-2 text-xs text-fg-faint">
            <span>For agents:</span>
            <a class="underline decoration-line-strong underline-offset-2 hover:text-fg" href="{{ url('explore/'.$entry->id.'.md') }}">this node as Markdown</a>
            <span aria-hidden="true">·</span>
            <a class="underline decoration-line-strong underline-offset-2 hover:text-fg" href="{{ url('explore/'.$entry->id.'.json') }}">JSON</a>
            <span aria-hidden="true">·</span>
            <a class="underline decoration-line-strong underline-offset-2 hover:text-fg" href="{{ url('explore.json') }}">the whole tree</a>
        </p>
    </div>
</div>
@endsection
