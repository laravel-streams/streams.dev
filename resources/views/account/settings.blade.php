@extends('layouts.account')

@section('content')
<div class="ls-doc flex min-h-screen">
    
    @include('account.aside')

    <div class="w-7/12 flex-grow py-10 pr-4">
        
        {!! Streams::make('users')->form([
            'entry' => auth()->id(),
            'fields' => [
                'password.enabled' => false,
                'gitlab_user.enabled' => false,
            ]
        ]) !!}
        
        {{-- <h1 class="text-4xl sm:text-6xl lg:text-7xl leading-none font-extrabold tracking-tight text-gray-900 mt-8 mb-8">
            {{ $entry->title }}
        </h1>
        
        @if ($entry->intro)
        <p class="text-2xl tracking-tight mb-10 opacity-40">{{ $entry->intro }}</p>
        @endif

        <div class="doc-body">
            {!! $entry->body()->render() !!}
        </div>

        <div class="flex mt-12 mb-8">
            <a href="https://github.com/laravel-streams/{{$entry->stream()->project}}/blob/master/docs/{{$entry->id}}.md" target="_blank" class="hover:text-gray-700 text-gray-400 text-sm outline-none focus:outline-none transition duration-200 ease-in-out">Improve this page.</a>
        </div> --}}
    </div>

    @include('partials.docs.toc')
</div>
@endsection
