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
@endphp

<aside class="w-60 shrink-0">
    <div class="py-4 pr-6">

        <ul class="text-sm border-b border-gray-200 pb-4 mb-4 flex flex-wrap gap-x-3 gap-y-1">
            <li>
                <a href="/docs" class="{{ $section === null || $section === 'docs' ? 'font-bold text-black' : 'text-gray-600 hover:text-black' }}">Hub</a>
            </li>
            @foreach ($packages as $slug => $package)
            <li>
                <a href="/docs/{{ $slug }}/introduction" class="{{ $section === $slug ? 'font-bold text-black' : 'text-gray-600 hover:text-black' }}">{{ $package['label'] }}</a>
            </li>
            @endforeach
        </ul>

        @if ($isPackageSection)
            <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">{{ $packages[$section]['label'] }}</p>
            <ul class="flex flex-col gap-1">
                @foreach (Streams::entries($packages[$section]['stream'])->orderBy('sort_order', 'ASC')->get() as $page)
                <li class="{{ Request::segment(3) == $page->id ? 'font-bold text-black' : '' }}">
                    <a class="hover:underline text-gray-800" href="/docs/{{ $section }}/{{ $page->id }}">{{ $page->title }}</a>
                </li>
                @endforeach
            </ul>
        @else
            @foreach(Streams::entries('docs_categories')->orderBy('sort_order', 'ASC')->get() as $category)
            <div class="mt-4">
                <span class="text-sm font-bold">{{ $category->name }}</span>
                <ul class="flex flex-col mt-2 gap-1">
                    @foreach (Streams::docs()->where('category', $category->id)->orderBy('sort_order', 'ASC')->get() as $page)
                    <li class="{{ Request::segment(2) == $page->id ? 'font-bold text-black' : '' }}">
                        <a class="hover:underline text-gray-800" href="/docs/{{ $page->id }}">{{ $page->title }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        @endif

    </div>
</aside>
