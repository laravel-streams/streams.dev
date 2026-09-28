@extends('layouts.shell')

@section('main')
<div class="mx-auto flex max-w-6xl flex-col gap-10 px-[var(--st-gutter)] pt-[var(--st-section-y-sm)] pb-[var(--st-section-y)] md:flex-row">
    @include('partials.filters')

    <div class="min-w-0 flex-1">
        {!! View::parse($entry->body) !!}
    </div>
</div>
@endsection
