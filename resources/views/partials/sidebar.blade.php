@php
    $section = Request::segment(2);
    $packages = \App\Support\DocsNav::packages();
    $isPackageSection = array_key_exists($section, $packages);
    $isHubIndex = Request::is('docs') && ! Request::segment(2);
    $hubPage = ! $isPackageSection ? Request::segment(2) : null;

    // Hub groups (streams/docs_categories.json) with their own sidebar section.
    $getStartedPages = Streams::docs()->where('category', 'getting-started')->orderBy('order', 'ASC')->get();
    $contributingPages = Streams::docs()->where('category', 'this-project')->orderBy('order', 'ASC')->get();
    $guideCategories = Streams::entries('docs_categories')->orderBy('order', 'ASC')->get()
        ->reject(fn ($category) => in_array($category->id, ['getting-started', 'this-project']));
    $inContributing = $hubPage && $contributingPages->contains('id', $hubPage);
@endphp

<nav class="docs-sidebar" x-data="{
    guidesOpen: localStorage.getItem('docs-nav-guides-open') === '1',
    getStartedOpen: localStorage.getItem('docs-nav-getstarted-open') !== '0',
    contributingOpen: {{ $inContributing ? 'true' : 'false' }} || localStorage.getItem('docs-nav-contributing-open') === '1',
    toggleGuides() {
        this.guidesOpen = !this.guidesOpen;
        localStorage.setItem('docs-nav-guides-open', this.guidesOpen ? '1' : '0');
    },
    toggleGetStarted() {
        this.getStartedOpen = !this.getStartedOpen;
        localStorage.setItem('docs-nav-getstarted-open', this.getStartedOpen ? '1' : '0');
    },
    toggleContributing() {
        this.contributingOpen = !this.contributingOpen;
        localStorage.setItem('docs-nav-contributing-open', this.contributingOpen ? '1' : '0');
    }
}" x-init="if (localStorage.getItem('docs-nav-guides-open') === null) { guidesOpen = false; }">

    {{-- Inline filter (resources/js/docs-filter.js): narrows this nav, the page, and "On this page" as you type. ⌘K stays the full search. --}}
    <div class="docs-filter" role="search" data-docs-filter-root>
        <svg class="docs-filter__icon" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="5.75" stroke="currentColor" stroke-width="1.5"/><path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input
            type="search"
            class="docs-filter__input"
            placeholder="Filter docs"
            aria-label="Filter the navigation and this page"
            aria-controls="docs-nav-tree"
            aria-describedby="docs-filter-status"
            autocomplete="off"
            spellcheck="false"
            enterkeyhint="go"
            data-docs-filter
        >
        <button type="button" class="docs-filter__kbd" data-docs-search-open title="Search all docs (⌘K)" aria-label="Open full search">
            <kbd>⌘K</kbd>
        </button>
    </div>
    <div class="docs-filter__meta" data-docs-filter-meta hidden>
        <p id="docs-filter-status" class="docs-filter__status" aria-live="polite" data-docs-filter-status></p>
        <button type="button" class="docs-filter__next" data-docs-filter-next hidden></button>
    </div>
    <div class="docs-filter__empty" data-docs-filter-empty hidden>
        <p>No pages match <strong data-docs-filter-query></strong>.</p>
        <button type="button" class="docs-filter__empty-action" data-docs-filter-search>Search all docs <kbd>⌘K</kbd></button>
    </div>

    <div id="docs-nav-tree" data-docs-nav>
    {{-- Get started is open by default; the choice is remembered once toggled. --}}
    <div class="mb-5" data-filter-section>
        <button type="button" class="docs-nav-label w-full text-left flex items-center justify-between cursor-pointer" @click="toggleGetStarted()">
            <span>Get started</span>
            <span x-text="getStartedOpen ? '−' : '+'" class="font-normal text-[var(--color-text-muted)]"></span>
        </button>
        <ul class="mt-1 space-y-0.5" x-show="getStartedOpen">
            @foreach ($getStartedPages as $page)
            <li>
                <a href="/docs/{{ $page->id }}"
                   class="docs-nav-link docs-nav-link--sub {{ $hubPage == $page->id ? 'is-active' : '' }}">
                    {{ $page->nav_title ?: $page->title }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    <div data-filter-section>
    <p class="docs-nav-label">Packages</p>
    <ul class="mb-5 space-y-1">
        @foreach ($packages as $slug => $package)
        @php
            $packagePages = Streams::exists($package['stream'])
                ? Streams::entries($package['stream'])->orderBy('order', 'ASC')->get()
                : collect();
            $landing = $packagePages->firstWhere('id', 'introduction') ?? $packagePages->first();
        @endphp
        @continue(! $landing)
        <li data-filter-group>
            <a href="/docs/{{ $slug }}/{{ $landing->id }}"
               class="docs-nav-link {{ $section === $slug ? 'is-active' : '' }}" data-filter-label>
                {{ $package['label'] }}
            </a>
            {{-- Every package's pages are in the markup so the filter can find them; only the current package's list is shown by default. --}}
            <ul @class(['docs-nav-nested mt-1.5 mb-3 space-y-0.5', 'docs-nav-collapsed' => $section !== $slug])>
                @php $listed = []; @endphp
                @foreach ($package['groups'] ?? [] as $group)
                <li class="docs-nav-group">
                    <p class="docs-nav-label">{{ $group['label'] }}</p>
                    <ul class="space-y-0.5">
                        @foreach ($group['pages'] ?? [] as $pageId)
                        @php
                            $page = $packagePages->firstWhere('id', $pageId);
                            $listed[] = $pageId;
                        @endphp
                        @continue(! $page)
                        <li>
                            <a href="/docs/{{ $slug }}/{{ $page->id }}"
                               class="docs-nav-link docs-nav-link--sub {{ $section === $slug && Request::segment(3) == $page->id ? 'is-active' : '' }}">
                                {{ $page->nav_title ?: $page->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endforeach
                @foreach ($packagePages as $page)
                @continue(in_array($page->id, $listed, true))
                <li>
                    <a href="/docs/{{ $slug }}/{{ $page->id }}"
                       class="docs-nav-link docs-nav-link--sub {{ $section === $slug && Request::segment(3) == $page->id ? 'is-active' : '' }}">
                        {{ $page->nav_title ?: $page->title }}
                    </a>
                </li>
                @endforeach
            </ul>
        </li>
        @endforeach
    </ul>
    </div>

    <div class="mb-5" data-filter-section>
        <button type="button" class="docs-nav-label w-full text-left flex items-center justify-between cursor-pointer" @click="toggleGuides()">
            <span>Guides</span>
            <span x-text="guidesOpen ? '−' : '+'" class="font-normal text-[var(--color-text-muted)]"></span>
        </button>
        <div x-show="guidesOpen" x-cloak class="mt-1">
            <a href="/docs" class="docs-nav-link block mb-2 {{ $isHubIndex ? 'is-active' : '' }}">Overview</a>
            @foreach ($guideCategories as $category)
            <details class="mb-2 group" data-filter-group>
                <summary class="docs-nav-link cursor-pointer list-none flex items-center justify-between">
                    <span data-filter-label>{{ $category->name }}</span>
                </summary>
                <ul class="docs-nav-nested mt-1 space-y-0.5">
                    @foreach (Streams::docs()->where('category', $category->id)->orderBy('order', 'ASC')->get() as $page)
                    <li>
                        <a href="/docs/{{ $page->id }}"
                           class="docs-nav-link docs-nav-link--sub {{ $hubPage == $page->id ? 'is-active' : '' }}">
                            {{ $page->nav_title ?: $page->title }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </details>
            @endforeach
        </div>
    </div>

    {{-- How streams.dev itself is built and run: last, because it matters least to people building Streams apps. --}}
    <div data-filter-section>
        <button type="button" class="docs-nav-label w-full text-left flex items-center justify-between cursor-pointer" @click="toggleContributing()">
            <span>Contributing</span>
            <span x-text="contributingOpen ? '−' : '+'" class="font-normal text-[var(--color-text-muted)]"></span>
        </button>
        <ul class="mt-1 space-y-0.5" x-show="contributingOpen" x-cloak>
            @foreach ($contributingPages as $page)
            <li>
                <a href="/docs/{{ $page->id }}"
                   class="docs-nav-link docs-nav-link--sub {{ $hubPage == $page->id ? 'is-active' : '' }}">
                    {{ $page->nav_title ?: $page->title }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    </div>

</nav>
