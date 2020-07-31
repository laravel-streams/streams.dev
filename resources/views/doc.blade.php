@extends('layouts/default')

@section('content')

<div class="o-doc">

    <style rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/styles/default.min.css"></style>
    
    <div class="grid grid-cols-12 gap-4 px-4">
        <div class="hidden xl:col-span-2">
            
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
                    @if (count(request()->segments()) == 3 && request()->segment(2) == 'core')
                        <?php $docs = Streams::entries('docs_streams')->orderBy('sort', 'asc')->get(); ?>
                        <?php $suffix = '/core'; ?>
                    @elseif (count(request()->segments()) == 3 && request()->segment(2) == 'ui')
                        <?php $docs = Streams::entries('docs_ui')->orderBy('sort', 'asc')->get(); ?>
                        <?php $suffix = '/ui'; ?>
                    @else
                        <?php $docs = Streams::entries('docs')->orderBy('sort', 'asc')->get(); ?>
                        <?php $suffix = ''; ?>
                    @endif
                    
                    @foreach ($docs->filter(function($item) {
                        return $item->section == null;
                    }) as $item)
                    <li>
                    <a href="/{{request()->segment(1)}}{{ $suffix }}/{{ $item->id }}" title="{{ $item->linkTitle ?? $item->title }}">
                        {{ $item->linkTitle ?? $item->title }}
                    </a>
                    </li>
                    @endforeach
                    
                    <?php $sections = Streams::make('docs')->fields->section->options; ?>
                    @foreach ($sections as $section => $name)
                    <li><small class="opacity-25 mt-3 inline-block">{{$name}}</small></li>
                    @foreach ($docs->filter(function($item) use ($section) {
                        return $item->section == $section;
                    }) as $item)
                    <li>
                    <a href="/{{request()->segment(1)}}{{ $suffix }}/{{ $item->id }}" title="{{ $item->linkTitle ?? $item->title }}">
                        {{ $item->linkTitle ?? $item->title }}
                    </a>
                    </li>
                    @endforeach
                    @endforeach

                </ul>

                <h4>Digging Deeper</h4>
                <ul>
                    <li><a href="/docs/core/introduction">Core</a></li>
                    <li><a href="/docs/ui/introduction">UI</a></li>
                    <li><a href="#docs/api/introduction" class="disabled">API</a></li>
                </ul>
                
            </aside>
        </div>
        <div class="col-span-12 xl:col-span-8 xl:col-start-4">
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
