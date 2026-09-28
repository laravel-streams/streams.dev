@extends('layouts.docs')

@section('content')
    <div class="docs-hub-content">
        {!! View::parse($entry->body) !!}
    </div>
@endsection
