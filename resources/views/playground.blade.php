@extends('layouts.playground')

@section('title', 'UI Playground')
@section('description', 'Explore the Laravel Streams UI component catalog in the playground.')

@section('navigation')
<ul class="space-y-1" data-playground-nav>
    @foreach (collect($components)->groupBy('group') as $group => $items)
    <li>
        <p class="docs-nav-label">{{ $group }}</p>
        <ul class="space-y-0.5">
            @foreach ($items as $component)
            <li>
                <a class="docs-nav-link docs-nav-link--sub" href="{{ $component['reference'] }}" data-playground-component="{{ $component['id'] }}">{{ $component['label'] }}</a>
            </li>
            @endforeach
        </ul>
    </li>
    @endforeach
</ul>
@endsection

@section('content')
<p>Browse {{ count($components) }} components in the catalog. Each navigation link opens the related UI reference.</p>
<p>Interactive component previews are planned.</p>
@endsection
