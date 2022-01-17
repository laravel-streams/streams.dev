@php
    $stream = isset($stream) ? $stream : Streams::make('docs');
    $docs = $stream->entries()->where('enabled', true)->orderBy('sort', 'asc')->get();
@endphp

<aside class="sidebar w-1/4 text-black h-100 py-10">

    <div class="sidebar__content pl-16">

        <div>
                
            <p>
                <a href="/docs">Documentation <x-heroicon-o-home class="inline-block h-4 w-4" style="margin-bottom: 0.25rem;"/></a>
            </p>

            <p>
                <a href="#" onclick="document.getElementById('packages_model').classList.toggle('hidden'); return false;">Packages <x-heroicon-o-truck class="inline-block h-4 w-4" style="margin-bottom: 0;"/></a>
            </p>

            <ul class="mt-10">
                @foreach ($docs->where('category', null) as $page)
                <li>
                    <a href="{{ request()->path() == 'docs' ? '/docs/' : '' }}{{$page->id}}" class="{{ ($page->id == $entry->id) ? 'text-red-500 font-bold' : 'text-gray-500' }}">{{ $page->link_title ? $page->link_title : $page->title }}</a>
                    {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
                </li>
                @endforeach
            </ul>

            @foreach ($stream->fields->category->config['options'] as $category => $label)

                @php
                $pages = $docs->where('category', $category);
                @endphp

                @if ($pages->isNotEmpty())
                <p class="mt-5 font-bold uppercase">
                    {{ $label }}
                </p>
                <ul class="mt-2">
                    @foreach ($pages as $page)
                    <li>
                        <a href="{{ request()->path() == 'docs' ? '/docs/' : '' }}{{$page->id}}" class="{{ ($page->id == $entry->id) ? 'text-red-500 font-bold' : 'text-gray-500' }}">{{ $page->link_title ? $page->link_title : $page->title }}</a>
                        {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
                    </li>
                    @endforeach
                </ul>
                @endif

            @endforeach

        </div>

        <div class="flex-shrink-0 flex mt-5 px-2 space-y-1 pl-0">
            <div class="text-black dark:text-white opacity-25 text-xs">
                {{ number_format(microtime(true) - Request::server('REQUEST_TIME_FLOAT'), 2) . ' s' }}&nbsp;|&nbsp;
                @php
                $size = memory_get_usage(true);
        
                $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        
                echo round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
                @endphp
            </div>
        </div>   

    </div>

</aside>
