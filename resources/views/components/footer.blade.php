<footer {{ $attributes->class(['relative isolate overflow-hidden bg-glass backdrop-blur-xl']) }}>
    <div class="st-lattice st-lattice--footer" aria-hidden="true"></div>
    <div class="mx-auto flex max-w-[var(--st-content-width)] flex-col gap-7 px-[var(--st-gutter)] py-14 text-md text-fg-muted sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2.5">
            <img src="{{ asset('img/logo.svg') }}" alt="" class="st-logo st-logo--sm opacity-70 dark:invert" width="24" height="24">
            <span>Streams · Agentic-first Laravel packages</span>
        </div>
        <ul class="flex flex-wrap items-center gap-x-6 gap-y-3">
            <li><a class="transition-colors duration-200 ease-out hover:text-fg" href="/docs">Docs</a></li>
            <li><a class="transition-colors duration-200 ease-out hover:text-fg" href="/explore/idea">Explore</a></li>
            <li><a class="transition-colors duration-200 ease-out hover:text-fg" href="/addons">Addons</a></li>
            <li><a class="transition-colors duration-200 ease-out hover:text-fg" href="https://github.com/laravel-streams" rel="noopener">GitHub</a></li>
        </ul>
    </div>
    @if (config('app.debug'))
    <div class="mx-auto max-w-[var(--st-content-width)] px-[var(--st-gutter)] pb-6 text-sm text-fg-muted">
        {{ response_time() . ' s' }}&nbsp;|&nbsp;{{ memory_usage() }}
    </div>
    @endif
</footer>
