@php
$areas = ['docs' => 'Streams', 'core' => 'Core', 'ui' => 'UI'];
@endphp

@if (count(request()->segments()) == 3 && request()->segment(2) == 'core')
@php
$docs = Streams::entries('docs_core')->where('enabled', true)->orderBy('sort', 'asc')->get();
$suffix = '/core';
@endphp
@elseif (count(request()->segments()) == 3 && request()->segment(2) == 'ui')
@php
$docs = Streams::entries('docs_ui')->where('enabled', true)->orderBy('sort', 'asc')->get();
$suffix = '/ui';
@endphp
@else
@php
$docs = Streams::entries('docs')->where('enabled', true)->orderBy('sort', 'asc')->get();
$suffix = '';
@endphp
@endif

<aside class="w-3/12 xl:w-2/12 hidden md:block md:pl-4 xl:pl-0 pb-12">

    <div class="o-aside">
        <div class="c-scrollbar">
            
            <p class="c-aside-menu-title">
                <a  href="/docs">Docs Home</a>
            </p>

            <ul class="c-aside-menu">
                @foreach ($stream->entries()->where('enabled', true)->orderBy('sort', 'ASC')->where('category',
                null)->get() as $page)
                <li {{ ($page->id == $entry->id) ? 'class=active' : '' }}>
                    <a href="{{$page->id}}">{{ $page->title }}</a>
                    {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
                </li>
                @endforeach
            </ul>

            @foreach ($stream->fields->category->config['options'] as $category => $label)

            @php
            $pages = $stream->entries()->where('enabled', true)->orderBy('sort', 'ASC')->where('category',
            $category)->get()
            @endphp

            @if ($pages->isNotEmpty())
            <p class="c-aside-menu-title">
                {{ $label }}
            </p>
            <ul class="c-aside-menu">
                @foreach ($pages as $page)
                <li {{ ($page->id == $entry->id) ? 'class=active' : '' }}>
                    <a href="{{$page->id}}">{{ $page->title }}</a>
                    {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
                </li>
                @endforeach
            </ul>
            @endif

            @endforeach

        </div>
    </div>

</aside>
