@extends('layouts/default')

@section('content')
<div class="flex flex-wrap min-h-screen">
    <aside class="w-aside p-8">
        Aside
        <ul>
            <li>
                <a href="/docs">Docs Home</a>
            </li>
        </ul>
        <?php $areas = ['docs' => 'Streams', 'core' => 'Core', 'ui' => 'UI']; ?>

                @if (count(request()->segments()) == 3 && request()->segment(2) == 'core')
                <?php $docs = Streams::entries('docs_core')->where('enabled', true)->orderBy('sort', 'asc')->get(); ?>
                <?php $suffix = '/core'; ?>
                @elseif (count(request()->segments()) == 3 && request()->segment(2) == 'ui')
                <?php $docs = Streams::entries('docs_ui')->where('enabled', true)->orderBy('sort', 'asc')->get(); ?>
                <?php $suffix = '/ui'; ?>
                @else
                <?php $docs = Streams::entries('docs')->where('enabled', true)->orderBy('sort', 'asc')->get(); ?>
                <?php $suffix = ''; ?>
                @endif

                <ul>
                    @foreach ($stream->entries()->where('enabled', true)->orderBy('sort', 'ASC')->where('category',
                    null)->get() as $page)
                    <li>
                        <a href="{{$page->id}}">{{ $page->title }}</a>
                        {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
                    </li>
                    @endforeach
                </ul>

                @foreach ($stream->fields->category->config['options'] as $category => $label)

                <?php $pages = $stream->entries()->where('enabled', true)->orderBy('sort', 'ASC')->where('category', $category)->get() ?>

                @if ($pages->isNotEmpty())
                <h4>{{ $label }}</h4>
                <ul>
                    @foreach ($pages as $page)
                    <li>
                        <a href="{{$page->id}}">{{ $page->title }}</a>
                        {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
                    </li>
                    @endforeach
                </ul>
                @endif

                @endforeach
        a
    </aside>
    <div class="o-doc-body">
        <h1>
            {{ $entry->title }}
        </h1>
        @if ($entry->intro)
        {!! Str::markdown($entry->intro) !!}
        @endif
        {!! Str::markdown(View::parse($entry->body)) !!}
    </div>
    <div>
        <div class="sticky top-0 js-toc"></div>

    </div>
</div>


@endsection