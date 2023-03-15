<?php

namespace App\Components;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Request;
use Streams\Ui\Components\Form;

class LoginForm extends Form implements Renderable
{
    public array $fields = [
        [
            'handle' => 'email',
            'type' => 'email',
        ],
        [
            'handle' => 'password',
            'type' => 'string',
            'inupt' => [
                'type' => 'input.password',
            ],
        ],
    ];

    public string $template = <<<'blade'
        <div>{{ $slot ?? 'Hello' }}</div>
    blade;

    public function login()
    {
        $email = Request::input('email');
        $password = Request::input('password');

        if (auth()->attempt(compact('email', 'password'))) {
            return redirect()->intended('/admin');
        }

        dd('Invalid credentials');
    }

    public function __invoke()
    {
        return $this->render();
    }
}
