<nav class="docs-topbar sticky top-0 z-30 border-b border-[var(--color-border)] bg-[var(--color-surface)]/95 backdrop-blur-sm">
    <div class="mx-auto flex max-w-[90rem] items-center gap-4 px-4 py-3 lg:px-6">

        <a href="/" class="flex shrink-0 items-center opacity-80 hover:opacity-100">
            <div class="h-10 w-10">
                <img src="{!! URL::asset('img/logo.svg') !!}" alt="{{ config('app.name') }}">
            </div>
            <span class="sr-only">{{ config('app.name') }}</span>
        </a>

        <button
            type="button"
            class="hidden sm:flex flex-1 max-w-md items-center gap-2 rounded-md border border-[var(--color-border)] bg-[var(--color-page)] px-3 py-2 text-sm text-[var(--color-text-muted)] hover:border-[var(--color-border-strong)]"
            data-docs-search-open
        >
            <span class="flex-1 text-left">Search documentation…</span>
            <kbd class="text-xs font-mono border border-[var(--color-border)] rounded px-1.5 py-0.5 bg-[var(--color-surface)]">⌘K</kbd>
        </button>

        <ul class="ml-auto flex items-center gap-6 text-sm">
            @foreach (Streams::pages()->where('nav_disabled', '!=', true)->orderBy('sort_order', 'ASC')->get() as $page)
            <li>
                <a class="hover:underline {{ Str::startsWith(Request::url(), URL::to($page->uri)) ? 'font-semibold text-[var(--color-text)]' : 'text-[var(--color-text-secondary)]' }}"
                   href="{{ URL::to($page->uri) }}">
                    {{ $page->title }}
                </a>
            </li>
            @endforeach
        </ul>

    </div>
</nav>
