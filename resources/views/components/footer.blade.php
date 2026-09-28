<footer {{ $attributes->class(['border-t border-line']) }}>
    <div class="mx-auto flex max-w-[90rem] flex-col gap-6 px-4 py-10 text-sm text-fg-muted sm:flex-row sm:items-center sm:justify-between lg:px-6">
        <div class="flex items-center gap-2.5">
            <img src="{{ asset('img/logo.svg') }}" alt="" class="st-logo st-logo--sm opacity-70 dark:invert" width="24" height="24">
            <span>Streams · Agentic-first Laravel packages</span>
        </div>
        <ul class="flex flex-wrap items-center gap-x-5 gap-y-2">
            <li><a class="hover:text-fg" href="/docs">Docs</a></li>
            <li><a class="hover:text-fg" href="/explore/idea">Explore</a></li>
            <li><a class="hover:text-fg" href="/addons">Addons</a></li>
            <li><a class="hover:text-fg" href="https://github.com/laravel-streams" rel="noopener">GitHub</a></li>
        </ul>
    </div>
    @if (config('app.debug'))
    <div class="mx-auto max-w-[90rem] px-4 pb-4 text-sm text-fg-muted lg:px-6">
        {{ response_time() . ' s' }}&nbsp;|&nbsp;{{ memory_usage() }}
    </div>
    @endif
</footer>
