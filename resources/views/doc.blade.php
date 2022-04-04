@extends('layouts.default')

@section('content')
<div class="ls-doc flex min-h-screen">
    
    @include('partials.docs.aside')

    <div class="ls-doc__content w-3/4 flex-grow pb-16 px-16">
        
        @if (App::environment() == 'local')
        <div class="pt-6">
            <a href="vscode://file{{ base_path($entry->stream()->path_prefix . '/' . $entry->id . '.md') }}" class="hover:text-gray-700 text-gray-400 text-sm outline-none focus:outline-none transition duration-200 ease-in-out">Edit this page.</a>
        </div>
        @endif
        
        <h1 class="text-4xl sm:text-6xl lg:text-6xl leading-none font-extrabold tracking-tight text-gray-900 mt-8 mb-8">
            {{ $entry->title }}
        </h1>
        
        <div class="ls-doc__toc mb-10"></div>

        <div class="doc-body">
            {!! $entry->body()->render() !!}
        </div>

        <div class="flex mt-12 mb-8">
            <a href="{{$entry->stream()->github_docs}}/{{$entry->id}}.md" target="_blank" class="hover:text-gray-700 text-gray-400 text-sm outline-none focus:outline-none transition duration-200 ease-in-out">Improve this page.</a>
        </div>
    </div>

</div>

@include('partials.docs.modal')

@endsection
