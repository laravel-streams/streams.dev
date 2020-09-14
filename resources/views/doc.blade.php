@extends('layouts/default')

@section('content')
<div class="flex flex-wrap min-h-screen">
    <aside class="w-aside p-8">
        @include('partials.docs.aside')
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
    @include('partials.docs.toc')
</div>
@endsection