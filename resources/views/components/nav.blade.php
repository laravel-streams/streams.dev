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
    <nav class="mx-auto flex h-full max-w-[90rem] items-center gap-2 px-3 sm:gap-3 sm:px-4 lg:px-6" aria-label="Primary">

        @if ($menuButton)
        <button type="button" class="st-btn st-btn--ghost st-btn--sm st-btn--icon -ml-1 lg:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle documentation menu" :aria-expanded="sidebarOpen.toString()">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3.5 6h13M3.5 10h13M3.5 14h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
        @endif

        <a href="/" class="flex shrink-0 items-center gap-2.5 rounded-full pr-1 text-fg">
            <img src="{{ asset('img/logo.svg') }}" alt="" class="st-logo dark:invert" width="32" height="32">
            <span class="hidden text-[0.9375rem] font-semibold tracking-tight min-[30rem]:inline">Streams</span>
        </a>

        <button type="button" class="st-search-trigger ml-2 hidden flex-1 sm:flex md:max-w-xs lg:max-w-sm" data-docs-search-open>
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="5.75" stroke="currentColor" stroke-width="1.5"/><path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <span class="flex-1 truncate">Search docs…</span>
            <kbd class="st-kbd">⌘K</kbd>
        </button>

        <div class="ml-auto flex items-center gap-1 sm:gap-1.5">
            <button type="button" class="st-btn st-btn--ghost st-btn--sm st-btn--icon sm:hidden" data-docs-search-open aria-label="Search documentation">
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="5.75" stroke="currentColor" stroke-width="1.5"/><path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </button>

            <ul class="mr-1 hidden items-center gap-0.5 md:flex">
                @foreach ($links as $link)
                <li><a href="{{ $link['href'] }}" class="st-nav-link" @if (Request::is($link['match'])) aria-current="page" @endif>{{ $link['label'] }}</a></li>
                @endforeach
            </ul>

            <x-button href="/explore/idea" variant="secondary" size="sm" class="hidden sm:inline-flex" :aria-current="Request::is('explore*') ? 'page' : null">Explore</x-button>

            <x-button href="/docs/installation" size="sm" arrow>
                <span class="st-btn__stack"><small class="hidden sm:block">Laravel Streams</small>Get Started</span>
            </x-button>

            <x-theme-toggle />
        </div>
    </nav>
</header>
