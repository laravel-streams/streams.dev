@extends('layouts.shell')

@section('main')
<div class="mx-auto max-w-[var(--st-content-width)] px-[var(--st-gutter)]">
    <div class="flex flex-col gap-8 py-12 lg:flex-row lg:gap-12 lg:py-16">
        <aside class="st-glass lg:w-[var(--st-sidebar-width)] lg:shrink-0" aria-label="Component catalog">
            <nav class="p-6" aria-label="Playground components">
                <p class="docs-nav-label">Components</p>
                @yield('navigation')
            </nav>
        </aside>
        <article class="docs-article min-w-0 flex-1">
            <header class="mb-12">
                <h1 class="docs-title">UI Playground</h1>
                <p class="docs-lead">Explore the Laravel Streams UI component catalog.</p>
            </header>
            <div class="docs-body">
                @yield('content')
            </div>
        </article>
    </div>
</div>
@endsection
