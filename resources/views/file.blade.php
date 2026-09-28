@extends('layouts.shell')

@section('main')
<div class="mx-auto max-w-5xl px-4 py-12 lg:px-6">
    <x-code-block :title="$entry->path ?? $entry->id" lang="json" :code="$entry->toJson(JSON_PRETTY_PRINT)" />
</div>
@endsection
