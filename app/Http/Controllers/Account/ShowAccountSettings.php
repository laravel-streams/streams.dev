<?php

namespace App\Http\Controllers\Account;

use Illuminate\Routing\Controller;

class ShowAccountSettings extends Controller
{
    public function __invoke()
    {
        return view('account.settings');
    }
}
