<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    @stack('head')
</head>

<body class="antialiased docs-layout bg-[var(--color-page)]" x-data="{ sidebarOpen: false }">

    @include('partials.topbar')
    @include('partials.docs-search')

    <div class="docs-layout__frame mx-auto max-w-[90rem] px-4 lg:px-6">
        <div class="flex gap-0 lg:gap-8 pt-4 pb-16">

            <div
                class="docs-sidebar-backdrop fixed inset-0 z-40 bg-black/20 lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            <div
                class="docs-sidebar-panel fixed inset-y-0 left-0 z-50 w-[var(--docs-sidebar-width)] transform bg-[var(--color-surface)] border-r border-[var(--color-border)] transition-transform lg:static lg:translate-x-0 lg:z-auto lg:shrink-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                x-cloak
            >
                @include('partials.sidebar')
            </div>

            <div class="docs-layout__main flex min-w-0 flex-1 gap-8 lg:gap-10">
                <article class="docs-article min-w-0 flex-1 max-w-[42rem]">
                    @if (App::environment() == 'local' && isset($entry) && $entry->stream()->handle ?? null)
                    <div class="docs-meta mb-4">
                        <a href="vscode://file{{ base_path('streams/data/' . $entry->stream()->handle . '/' . $entry->id . '.md') }}"
                           class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)]">
                            Edit this page
                        </a>
                    </div>
                    @endif

                    @hasSection('header')
                        @yield('header')
                    @elseif (isset($entry))
                        <header class="docs-header mb-8">
                            <h1 class="docs-title">{{ $entry->title }}</h1>
                            @if ($entry->description ?? null)
                                <p class="docs-lead">{{ $entry->description }}</p>
                            @endif
                        </header>
                    @endif

                    <div class="docs-body">
                        @yield('content')
                    </div>
                </article>

                <aside class="docs-toc-rail hidden xl:block w-[var(--docs-toc-width)] shrink-0">
                    <div class="documentation__toc sticky top-[var(--docs-header-offset)] max-h-[calc(100vh-var(--docs-header-offset)-2rem)] overflow-y-auto text-sm"></div>
                </aside>
            </div>
        </div>
    </div>

    <button
        type="button"
        class="docs-menu-btn fixed bottom-6 right-6 z-30 flex h-12 w-12 items-center justify-center rounded-full border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm lg:hidden"
        @click="sidebarOpen = !sidebarOpen"
        aria-label="Toggle documentation menu"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>

</html>
