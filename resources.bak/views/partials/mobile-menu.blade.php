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

<div id="mobile-menu" class="o-mobile-menu px-8">

    <div class="c-scrollbar">
        <ul class="o-mobile-menu__menu">
            <li>
                <h3>
                    <a href="/docs">Docs Home</a>
                </h3>
            </li>
        </ul>


        <ul class="o-mobile-menu__menu">

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
        <h3>{{ $label }}</h3>
        <ul class="o-mobile-menu__menu">
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