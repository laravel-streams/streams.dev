{{-- Site header: logo, search, section links, Explore, Get Started, theme toggle. --}}
@props([
    'menuButton' => false,
])

@php
    $links = [
        ['label' => 'Docs', 'href' => '/docs', 'match' => 'docs*'],
        ['label' => 'Addons', 'href' => '/addons', 'match' => 'addons*'],
    ];
@endphp

<header {{ $attributes->class(['st-header']) }}>
    <nav class="mx-auto flex h-full max-w-[90rem] items-center gap-2 px-4 sm:gap-3 sm:px-[var(--st-gutter)]" aria-label="Primary">

        @if ($menuButton)
        <button type="button" class="st-btn st-btn--ghost st-btn--sm st-btn--icon -ml-1 lg:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle documentation menu" :aria-expanded="sidebarOpen.toString()">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3.5 6h13M3.5 10h13M3.5 14h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
        @endif

        <a href="/" class="flex shrink-0 items-center rounded-full text-fg" aria-label="Streams home">
            <img src="{{ asset('img/logo.svg') }}" alt="" class="st-logo st-logo--header dark:invert" width="44" height="44">
        </a>

        <button type="button" class="st-search-trigger ml-3 hidden flex-1 sm:flex md:max-w-xs lg:ml-5 lg:max-w-sm" data-docs-search-open>
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="5.75" stroke="currentColor" stroke-width="1.5"/><path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <span class="flex-1 truncate">Search docs…</span>
            <kbd class="st-kbd">⌘K</kbd>
        </button>

        <div class="ml-auto flex items-center gap-1 sm:gap-2">
            <button type="button" class="st-btn st-btn--ghost st-btn--sm st-btn--icon sm:hidden" data-docs-search-open aria-label="Search documentation">
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="5.75" stroke="currentColor" stroke-width="1.5"/><path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </button>

            <ul class="mr-2 hidden items-center gap-1 md:flex">
                @foreach ($links as $link)
                <li><a href="{{ $link['href'] }}" class="st-nav-link" @if (Request::is($link['match'])) aria-current="page" @endif>{{ $link['label'] }}</a></li>
                @endforeach
            </ul>

            <x-button href="/explore/idea" variant="secondary" class="hidden sm:inline-flex" :aria-current="Request::is('explore*') ? 'page' : null">Explore</x-button>

            <x-button href="/docs/installation" size="sm" class="sm:[--st-btn-height:2.75rem] sm:[--st-btn-px:1.25rem]" arrow>
                <span class="st-btn__stack"><small class="hidden sm:block">Laravel Streams</small>Get Started</span>
            </x-button>

            <x-theme-toggle />
        </div>
    </nav>
</header>
