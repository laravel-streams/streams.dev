@extends('layouts/default')
@section('navigation')
<div class="navbar container">

</div>
@endsection
@section('content')
<div class="flex flex-wrap min-h-screen mx-auto" style="max-width:1500px;">
    @include('partials.docs.aside')

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