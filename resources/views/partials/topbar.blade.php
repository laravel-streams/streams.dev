<nav>
    <div class="container mx-auto flex py-4 items-center">

        <a href="/" class="flex items-center opacity-70 hover:opacity-100 -ml-3">
            <div class="h-14 w-14 mr-2">
                <img src="{!! URL::asset('img/logo.svg') !!}" alt="{{ config('app.name') }} Logo">
            </div>
            
            <span class="{{ Request::path() !== '/' ? 'sr-only' : null }} text-xl font-bold">
                {{ config('app.name') }}
            </span>
        </a>

        <ul class="flex flex-grow justify-end gap-10">
            @foreach (Streams::pages()->where('nav_disabled', '!=', true)->orderBy('sort_order', 'ASC')->get() as $page)
            <li class="{{ Str::startsWith(Request::url(), URL::to($page->uri)) ? 'font-bold' : '' }}">
                <a class="hover:underline" href="{{ URL::to($page->uri) }}">{{ $page->title }}</a>
            </li>
            @endforeach
        </ul>

    </div>
</nav>
