@extends('layouts.shell', ['chrome' => false])

@section('main')
    {!! View::parse($entry->body) !!}
@endsection
