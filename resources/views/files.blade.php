@extends('layouts.shell')

@section('title', 'Files')

@section('main')
<div class="mx-auto max-w-5xl px-4 py-12 lg:px-6">
    <h1 class="docs-title mb-8">Files</h1>
    <ul class="grid gap-1 font-mono text-sm">
        @foreach ($stream->entries()->orderBy('path', 'asc')->get() as $file)
        <li><a class="docs-nav-link" href="/files/{{ $file->id }}">{{ $file->path }}</a></li>
        @endforeach
    </ul>
</div>
@endsection
