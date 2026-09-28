@extends('layouts.shell')

@section('main')
    {!! View::parse($entry->body) !!}
@endsection
