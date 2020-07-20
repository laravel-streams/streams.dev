@extends('layouts/default')

@section('content')

<div class="">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-2">
            <aside class="o-aside sticky top-0">
    
                <h4>Themes</h4>
                <ul x-data="theme()" x-init="init">
                    <li @click="onLight" class="cursor-pointer">
                        I like the lightness
                    </li>
                    <li @click="onDark" class="cursor-pointer">
                        I like the darkness
                    </li>
                </ul>
            
                <h4>Docs</h4>
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
                    @foreach ($docs as $item)
                    <li>
                    <a href="/{{request()->segment(1)}}{{ $suffix }}/{{ $item->id }}" title="{{ $item->linkTitle ?? $item->title }}">
                        {{ $item->linkTitle ?? $item->title }}
                    </a>
                    </li>
                    @endforeach
                </ul>

                <h4>Projects</h4>
                <ul>
                    <li><a href="/docs/introduction">Streams</a></li>
                    <li><a href="/docs/core/introduction">Core</a></li>
                    <li><a href="/docs/ui/introduction">UI</a></li>
                    <li><a href="#docs/api/introduction" class="disabled">API</a></li>
                </ul>
                
            </aside>
        </div>
        <div class="col-span-8 col-start-4">
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
