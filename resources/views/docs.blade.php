@extends('layouts.docs')

@section('content')
    <div class="documentation-content prose prose-neutral">
        {!! \App\Support\DocumentationMarkdown::toHtml($entry->body) !!}
    </div>
@endsection
