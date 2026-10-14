<ul class="space-y-1" data-playground-nav>
    @foreach (collect($components)->groupBy('group') as $group => $items)
    <li>
        <p class="docs-nav-label">{{ $group }}</p>
        <ul class="space-y-0.5">
            @foreach ($items as $component)
            <li>
                <a
                    class="docs-nav-link docs-nav-link--sub"
                    href="{{ route('playground.component', $component['id']) }}"
                    data-playground-component="{{ $component['id'] }}"
                    @if (($active ?? null) === $component['id']) aria-current="page" @endif
                >{{ $component['label'] }}</a>
            </li>
            @endforeach
        </ul>
    </li>
    @endforeach
</ul>
