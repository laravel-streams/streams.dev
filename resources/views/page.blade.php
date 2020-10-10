@extends('layouts/default')
@section('navigation')
@include('partials.navbar')
@endsection
@section('content')


<div class="container mx-auto">
    <div class="o-page-body w-12/12">
        {!! $entry->body !!}
    </div>
</div>
@endsection