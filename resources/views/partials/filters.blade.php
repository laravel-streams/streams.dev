@php
    $activeType = Request::get('type');
@endphp
<aside class="shrink-0 md:w-52" aria-label="Addon types">
    <p class="docs-nav-label">Browse</p>
    <ul class="flex flex-wrap gap-1 md:flex-col">
        <li><a class="docs-nav-link {{ $activeType ? '' : 'is-active' }}" href="/addons">All addons</a></li>
        @foreach (Streams::make('packages')->fields->get('type')->options() as $key => $type)
        <li><a class="docs-nav-link {{ $activeType === $key ? 'is-active' : '' }}" href="/addons?type={{ $key }}">{{ $type }}</a></li>
        @endforeach
    </ul>
</aside>
