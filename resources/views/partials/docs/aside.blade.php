@php
    $docs = $stream->entries()->where('enabled', true)->orderBy('sort', 'asc')->get();
@endphp

<aside class="w-5/12 text-black py-10 pl-20">

    <div>
            
        <p>
            <a href="/docs">Documentation</a>
        </p>

        <ul class="mt-10">
            @foreach ($docs->where('category', null) as $page)
            <li>
                <a href="{{$page->id}}" class="{{ ($page->id == $entry->id) ? 'text-red-500 font-bold' : 'text-gray-500' }}">{{ $page->title }}</a>
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
                    <a href="{{$page->id}}" class="{{ ($page->id == $entry->id) ? 'text-red-500 font-bold' : 'text-gray-500' }}">{{ $page->title }}</a>
                    {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
                </li>
                @endforeach
            </ul>
            @endif

        @endforeach

    </div>

</aside>
