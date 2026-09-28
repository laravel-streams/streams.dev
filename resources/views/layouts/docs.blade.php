@extends('layouts.shell', [
    'menuButton' => true,
    'bodyData' => "{
        sidebarOpen: false,
        desktop: window.matchMedia('(min-width: 1024px)').matches,
        init() {
            const mq = window.matchMedia('(min-width: 1024px)');
            mq.addEventListener('change', () => { this.desktop = mq.matches; });
        },
    }",
])

@section('main')
<div class="mx-auto max-w-[90rem] px-4 lg:px-6">
    <div class="flex gap-0 lg:gap-8">

        <div
            class="fixed inset-x-0 bottom-0 top-[var(--st-header-height)] z-30 bg-black/20 backdrop-blur-[2px] lg:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            @keydown.escape.window="sidebarOpen = false"
            x-cloak
        ></div>

        {{-- Open state is a single class. Opposing translate utilities fight each other, so the drawer would stay off-screen. --}}
        <div
            class="docs-sidebar-panel st-glass--strong"
            :class="{ 'is-open': sidebarOpen }"
            :inert="!desktop && !sidebarOpen"
        >
            @include('partials.sidebar')
        </div>

        <div class="flex min-w-0 flex-1 gap-8 pt-10 pb-24 lg:gap-12">
            <article class="docs-article min-w-0 max-w-[46rem] flex-1">
                @hasSection('header')
                    @yield('header')
                @elseif (isset($entry))
                    <header class="mb-10">
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

            <aside class="hidden w-[var(--st-toc-width)] shrink-0 xl:block" aria-label="On this page">
                <div class="documentation__toc sticky top-[calc(var(--st-header-height)+2.5rem)] max-h-[calc(100vh-var(--st-header-height)-4rem)] overflow-y-auto"></div>
            </aside>
        </div>
    </div>
</div>
@endsection
