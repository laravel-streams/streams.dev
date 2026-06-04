@php
    $section = Request::segment(2);
    $packages = [
        'core' => ['label' => 'Core', 'stream' => 'core_docs'],
        'ui' => ['label' => 'UI', 'stream' => 'ui_docs'],
        'api' => ['label' => 'API', 'stream' => 'api_docs'],
        'sdk' => ['label' => 'SDK', 'stream' => 'sdk_docs'],
        'testing' => ['label' => 'Testing', 'stream' => 'testing_docs'],
        'client' => ['label' => 'Client', 'stream' => 'client_docs'],
    ];
    $isPackageSection = array_key_exists($section, $packages);
    $isHubIndex = Request::is('docs') && ! Request::segment(2);

    $newHereLinks = [
        ['title' => 'Installation', 'url' => '/docs/installation'],
        ['title' => 'Use cases', 'url' => '/docs/use-cases'],
        ['title' => 'Architecture', 'url' => '/docs/architecture'],
        ['title' => 'This project', 'url' => '/docs/this-project'],
        ['title' => 'UI quick start', 'url' => '/docs/ui/quick-start'],
    ];
@endphp

<nav class="docs-sidebar text-sm" x-data="{
    guidesOpen: localStorage.getItem('docs-nav-guides-open') === '1',
    newHereOpen: localStorage.getItem('docs-nav-newhere-open') === '1',
    toggleGuides() {
        this.guidesOpen = !this.guidesOpen;
        localStorage.setItem('docs-nav-guides-open', this.guidesOpen ? '1' : '0');
    },
    toggleNewHere() {
        this.newHereOpen = !this.newHereOpen;
        localStorage.setItem('docs-nav-newhere-open', this.newHereOpen ? '1' : '0');
    }
}" x-init="if (localStorage.getItem('docs-nav-guides-open') === null) { guidesOpen = false; }">

    <button type="button" class="docs-search-trigger" data-docs-search-open>
        <span>Search docs</span>
        <kbd>⌘K</kbd>
    </button>

    <p class="docs-nav-label">Reference</p>
    <ul class="mb-4 space-y-0.5">
        @foreach ($packages as $slug => $package)
        <li>
            <a href="/docs/{{ $slug }}/introduction"
               class="docs-nav-link {{ $section === $slug ? 'is-active' : '' }}">
                {{ $package['label'] }}
            </a>
            @if ($section === $slug)
            <ul class="docs-nav-nested mt-1 space-y-0.5 mb-2">
                @foreach (Streams::entries($package['stream'])->orderBy('sort_order', 'ASC')->get() as $page)
                <li>
                    <a href="/docs/{{ $slug }}/{{ $page->id }}"
                       class="docs-nav-link text-[0.8125rem] {{ Request::segment(3) == $page->id ? 'is-active' : '' }}">
                        {{ $page->title }}
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
    </ul>

    <div class="mb-4">
        <button type="button" class="docs-nav-label w-full text-left flex items-center justify-between cursor-pointer" @click="toggleNewHere()">
            <span>New here?</span>
            <span x-text="newHereOpen ? '−' : '+'" class="font-normal text-[var(--color-text-muted)]"></span>
        </button>
        <ul class="mt-1 space-y-0.5" x-show="newHereOpen" x-cloak>
            @foreach ($newHereLinks as $link)
            <li>
                <a href="{{ $link['url'] }}"
                   class="docs-nav-link {{ Request::is(trim($link['url'], '/')) || Request::segment(2) === basename($link['url']) ? 'is-active' : '' }}">
                    {{ $link['title'] }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    <div>
        <button type="button" class="docs-nav-label w-full text-left flex items-center justify-between cursor-pointer" @click="toggleGuides()">
            <span>Guides</span>
            <span x-text="guidesOpen ? '−' : '+'" class="font-normal text-[var(--color-text-muted)]"></span>
        </button>
        <div x-show="guidesOpen" x-cloak class="mt-1">
            <a href="/docs" class="docs-nav-link block mb-2 {{ $isHubIndex ? 'is-active' : '' }}">Overview</a>
            @foreach (Streams::entries('docs_categories')->orderBy('sort_order', 'ASC')->get() as $category)
            <details class="mb-2 group">
                <summary class="docs-nav-link cursor-pointer list-none flex items-center justify-between">
                    <span>{{ $category->name }}</span>
                </summary>
                <ul class="docs-nav-nested mt-1 space-y-0.5">
                    @foreach (Streams::docs()->where('category', $category->id)->orderBy('sort_order', 'ASC')->get() as $page)
                    <li>
                        <a href="/docs/{{ $page->id }}"
                           class="docs-nav-link text-[0.8125rem] {{ ! $isPackageSection && Request::segment(2) == $page->id ? 'is-active' : '' }}">
                            {{ $page->title }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </details>
            @endforeach
        </div>
    </div>

</nav>
