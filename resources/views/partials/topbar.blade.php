<nav>
    <div class="container mx-auto flex py-4 items-center">

        <a href="/" class="flex items-center">
            <div class="h-14 w-14">
                <img src="{!! URL::asset('img/logo.svg') !!}" alt="{{ config('app.name') }} Logo">
            </div>
            <span class="font-bold text-xl hover:underline">{!! Streams::repository('pages')->findBy('uri', '/')->title !!}</span>
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
