@extends('layouts/default')

@section('content')

<div class="o-doc">
    <div class="c-versioning">
        Alpha Version
    </div>
    <style rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/styles/default.min.css"></style>
    
    <div class="flex flex-wrap">
        <div class="xl:w-2/12">
            
            <aside class="o-aside sticky top-0">
                
                <ul class="flex text-sm">
                    <li class="cursor-pointer mr-2">
                        Lightness
                    </li>
                    <li>|</li>
                    <li class="cursor-pointer ml-2">
                        Darkness
                    </li>
                </ul>

                <br>

                <ul>
                    <li>
                        <a href="/docs">Docs Home</a>
                    </li>
                </ul>

                <br>

                <?php $areas = ['docs' => 'Streams', 'core' => 'Core', 'ui' => 'UI']; ?>
            
                @if (count(request()->segments()) == 3 && request()->segment(2) == 'core')
                    <?php $docs = Streams::entries('docs_core')->orderBy('sort', 'asc')->get(); ?>
                    <?php $suffix = '/core'; ?>
                @elseif (count(request()->segments()) == 3 && request()->segment(2) == 'ui')
                    <?php $docs = Streams::entries('docs_ui')->orderBy('sort', 'asc')->get(); ?>
                    <?php $suffix = '/ui'; ?>
                @else
                    <?php $docs = Streams::entries('docs')->orderBy('sort', 'asc')->get(); ?>
                    <?php $suffix = ''; ?>
                @endif
                
                <ul>                    
                    @foreach ($stream->entries()->orderBy('sort', 'ASC')->where('category', null)->get() as $page)
                    <li>
                        <a href="{{$page->id}}">{{ $page->title }}</a>
                        {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
                    </li>
                    @endforeach
                </ul>

                @foreach ($stream->fields->category->config['options'] as $category => $label)

                <?php $pages = $stream->entries()->orderBy('sort', 'ASC')->where('category', $category)->get() ?>

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
                {!! Str::markdown($entry->body) !!}
            </main>
        </div>
    </div>
</div>

@endsection
