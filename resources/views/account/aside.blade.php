@php
    $docs = Streams::make('docs')->entries()->where('enabled', true)->orderBy('sort', 'asc')->get();
@endphp

<aside class="w-3/12 text-black py-10 pl-20 mr-8">

    <div>
            
        <p class="font-bold uppercase">
            Account
        </p>

        <ul class="mt-2">
            <li>
                <a href="/account/settings" class="{{ Request::path() == 'account/settings' ? 'text-red-500 font-bold' : 'text-gray-500' }}">Settings</a>
            </li>
            <li>
                <a href="/account/account" class="{{ Request::path() == 'account/account' ? 'text-red-500 font-bold' : 'text-gray-500' }}">Addons</a>
            </li>
            <li>
                <a href="/account/keys" class="{{ Request::path() == 'account/keys' ? 'text-red-500 font-bold' : 'text-gray-500' }}">SSH Keys</a>
            </li>
        </ul>

    </div>

</aside>
