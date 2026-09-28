<div
    id="docs-search-root"
    class="docs-search"
    data-index-url="{{ url('/search/docs.json') }}"
    hidden
    aria-hidden="true"
>
    <div
        class="docs-search-overlay fixed inset-0 z-[100] flex items-start justify-center px-4 pt-[12vh]"
        data-docs-search-overlay
        role="dialog"
        aria-modal="true"
        aria-labelledby="docs-search-label"
    >
        <x-glass strong class="docs-search-panel w-full max-w-xl overflow-hidden">
            <label id="docs-search-label" for="docs-search-input" class="sr-only">Search documentation</label>
            <div class="flex items-center gap-3 border-b border-line px-5 py-4">
                <svg class="h-5 w-5 shrink-0 text-fg-muted" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="5.75" stroke="currentColor" stroke-width="1.5"/><path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input
                    id="docs-search-input"
                    type="search"
                    class="flex-1 border-0 bg-transparent text-base text-fg placeholder:text-fg-muted focus:outline-none focus:ring-0 [&::-webkit-search-cancel-button]:hidden"
                    placeholder="Search guides and reference…"
                    autocomplete="off"
                    spellcheck="false"
                    data-docs-search-input
                />
                <kbd class="st-kbd hidden sm:inline-flex">esc</kbd>
            </div>
            <ul class="max-h-[50vh] overflow-y-auto p-2" data-docs-search-results role="listbox" aria-label="Results"></ul>
            <p class="hidden px-5 py-8 text-center text-sm text-fg-muted" data-docs-search-empty>
                No results. Try “criteria”, “installation”, or “API routes”.
            </p>
        </x-glass>
    </div>
</div>
