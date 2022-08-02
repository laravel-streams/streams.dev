@extends('layouts/default')

@section('content')
<div class="flex flex-wrap min-h-screen">
    <main class="w-full mx-auto">
        
        {{ $addon->composer['name'] }}

    </main>
</div>
@endsection
