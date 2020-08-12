@extends('layouts/default')

@section('content')

<div class="o-doc">

    <style rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/styles/default.min.css"></style>
    
    <div class="flex flex-wrap">
        <div class="container m-auto">
            <main class="o-main">
                <h1>
                    {{ $entry->title }}
                </h1>
                @if ($entry->intro)
                    <div class="o-intro">
                        {!! Str::markdown($entry->intro) !!}
                    </div>
                @endif
                {!! Str::markdown(View::parse($entry->body)) !!}
            </main>
        </div>
    </div>
    
</div>

@endsection
