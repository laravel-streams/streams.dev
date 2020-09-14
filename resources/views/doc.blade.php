@extends('layouts/default')

@section('content')

<div class="o-doc">
    <div class="c-versioning">
        Alpha Version
        <div class="js-toc"></div>
    </div>
    <style rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/styles/default.min.css"></style>

    <div class="flex flex-wrap">
        <div class="xl:w-2/12">

            <aside class="o-aside sticky top-0">

                {{-- <ul class="flex text-sm">
                    <li class="cursor-pointer mr-2">
                        Lightness
                    </li>
                    <li>|</li>
                    <li class="cursor-pointer ml-2">
                        Darkness
                    </li>
                </ul>

                <br> --}}

                <ul>
                    <li>
                        <a href="/docs">Docs Home</a>
                    </li>
                </ul>

                <br>

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

            </aside>
        </div>
        <div class="lg:w-8/12 xl:w-9/12 lg:ml-auto">
            <main class="o-main">
                <h1>
                    {{ $entry->title }}
                </h1>
                @if ($entry->intro)
                <div class="o-intro">
                    {!! Str::markdown($entry->intro) !!}
                </div>
                @endif
                <div class="js-toc-content">
                    {!! Str::markdown($entry->body) !!}
                </div>
            </main>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/anchor-js/anchor.min.js"></script>

<script>
    anchors.options = {
        placement: 'right',
        visible: 'hover',
        truncate: 64,
        icon: '#'
    };

    anchors.add('.js-toc-content h2, .js-toc-content h3, .js-toc-content h4');
 </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tocbot/4.11.1/tocbot.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tocbot/4.11.1/tocbot.css">

<script>

    tocbot.init({
        // Where to render the table of contents.
        tocSelector: '.js-toc',
        // Where to grab the headings to build the table of contents.
        contentSelector: '.js-toc-content',
        // Which headings to grab inside of the contentSelector element.
        headingSelector: 'h1, h2, h3',
        // For headings inside relative or absolute positioned containers within content.
        hasInnerContainers: false,
        // How many heading levels should not be collapsed.
        // For example, number 6 will show everything since
        // there are only 6 heading levels and number 0 will collapse them all.
        // The sections that are hidden will open
        // and close as you scroll to headings within them.
        collapseDepth: 1,
    });
</script>

@endsection
