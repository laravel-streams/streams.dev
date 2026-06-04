<div
    id="docs-search-root"
    class="docs-search"
    data-index-url="{{ url('/search/docs.json') }}"
    hidden
    aria-hidden="true"
>
    <div
        class="docs-search-overlay fixed inset-0 z-[100] flex items-start justify-center bg-black/40 px-4 pt-[12vh]"
        data-docs-search-overlay
        role="dialog"
        aria-modal="true"
        aria-labelledby="docs-search-label"
    >
        <div class="docs-search-panel w-full max-w-xl rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] shadow-xl overflow-hidden">
            <label id="docs-search-label" class="sr-only">Search documentation</label>
            <div class="flex items-center gap-2 border-b border-[var(--color-border)] px-4 py-3">
                <svg class="h-5 w-5 shrink-0 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                </svg>
                <input
                    type="search"
                    class="docs-search-input flex-1 border-0 bg-transparent text-base text-[var(--color-text)] placeholder:text-[var(--color-text-muted)] focus:outline-none focus:ring-0"
                    placeholder="Search guides and reference…"
                    autocomplete="off"
                    spellcheck="false"
                    data-docs-search-input
                />
                <kbd class="hidden sm:inline text-xs text-[var(--color-text-muted)] border border-[var(--color-border)] rounded px-1.5 py-0.5">esc</kbd>
            </div>
            <ul class="docs-search-results max-h-[50vh] overflow-y-auto py-2" data-docs-search-results role="listbox"></ul>
            <p class="docs-search-empty hidden px-4 py-6 text-sm text-center text-[var(--color-text-muted)]" data-docs-search-empty>
                No results. Try “criteria”, “installation”, or “API routes”.
            </p>
        </div>
    </div>
</div>
