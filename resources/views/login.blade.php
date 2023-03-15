@extends('layouts/default')

@section('content')

<x-form action="/login/auth" method="post">
    
    @ui('email', [
        'name' => 'email',
        'label' => 'Email',
        'placeholder' => 'Email',
        'value' => old('email', 'ryan@pyrocms.com'),
        'required' => true,
    ])

    @ui('password', [
        'name' => 'password',
        'label' => 'Password',
        'placeholder' => 'Password',
        'value' => old('password', 'password'),
        'required' => true,
    ])

    <x-button>
        Login
    </x-button>

</x-form>

@endsection
