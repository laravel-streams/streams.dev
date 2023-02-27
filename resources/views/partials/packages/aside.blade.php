@php
    
@endphp

<aside class="sidebar w-1/4 text-black h-100 py-8">

    <div class="sidebar__content pl-16">

        <div>
                
            <p>
                <a href="/docs" class="{{ Request::segment(1) == 'docs' ? "italic font-bold" : "" }} hover:underline">Documentation <x-heroicon-o-home class="inline-block h-4 w-4" style="margin-bottom: 0.25rem;"/></a>
            </p>

            <p>
                <a href="/packages" class="{{ Request::segment(1) == 'packages' ? "italic font-bold" : "" }} hover:underline">Packages <x-heroicon-o-truck class="inline-block h-4 w-4" style="margin-bottom: 0;"/></a>
            </p>

            <h2 class="mt-8 font-bold uppercase">
                Categories
            </h2>

            <ul class="mt-2">
                @foreach (Streams::make('packages')->fields->categories->options() as $key => $value)    
                <li>
                    <a href="/packages/category/{{ $key }}" class="text-gray-500 hover:underline">{{ $value }}</a>
                </li>
                @endforeach
            </ul>

        </div>

        <div class="flex-shrink-0 flex mt-5 px-2 space-y-1 pl-0">
            <div class="text-black dark:text-gray-600 opacity-25 text-xs">
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
