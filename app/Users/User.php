<?php

namespace App\Users;

use Streams\Core\Entry\Entry;
use Streams\Core\Support\Facades\Streams;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthAuthenticatable;

class User extends Entry implements Authenticatable
{
    use AuthAuthenticatable;

    public function __construct(array $attributes = [])
    {
        $attributes['stream'] = 'users';

        parent::__construct($attributes);
    }

    public function newQuery()
    {
        return Streams::users();
    }

    public function getKeyName()
    {
        return 'id';
    }
}
