@extends('layouts.docs')

@section('content')
    <div class="documentation-content">
        {!! \App\Support\DocumentationMarkdown::toHtml($entry->body) !!}
    </div>
@endsection
