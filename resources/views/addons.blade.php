@extends('layouts.shell')

@section('main')
<div class="mx-auto flex max-w-6xl flex-col gap-8 px-4 pt-12 pb-24 md:flex-row lg:px-6">
    @include('partials.filters')

    <div class="min-w-0 flex-1">
        {!! View::parse($entry->body) !!}
    </div>
</div>
@endsection
