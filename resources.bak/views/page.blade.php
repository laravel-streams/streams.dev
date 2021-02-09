@extends('layouts/default')

@section('content')
<div class="flex flex-wrap min-h-screen">
    <main class="xs:w-full mx-auto p-10">
        {!! View::parse($entry->body, compact('entry'))->render() !!}
    </main>
</div>
@endsection