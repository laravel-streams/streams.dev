@extends('layouts.docs')

@section('content')
    <div class="documentation-content prose prose-neutral">
        {!! \App\Support\DocumentationMarkdown::toHtml($entry->body) !!}
    </div>

    {{-- Local only, and only this article view. The docs hub, homepage, and Explore do not use it. --}}
    @php
        $editUrl = null;
        if (App::environment('local') && isset($entry) && is_object($entry) && method_exists($entry, 'stream')) {
            $editStream = $entry->stream();
            $relative = 'streams/data/'.$editStream->id.'/'.$entry->id.'.'.data_get($editStream, 'config.source.format', 'md');
            if (is_file(base_path($relative))) {
                $editUrl = 'https://github.com/laravel-streams/streams.dev/blob/develop/'.$relative;
            }
        }
    @endphp
    @if ($editUrl)
        <div class="docs-edit">
            <x-button :href="$editUrl" variant="ghost" size="sm" target="_blank" rel="noopener noreferrer">Edit this page</x-button>
        </div>
    @endif
@endsection
