<aside class="mr-8">
    <div class="py-4">

        <a href="/docs">Documentation</a>

        <br><br>

        <ul class="flex flex-col">
            @foreach (Streams::docs()->orderBy('sort_order', 'ASC')->get() as $page)
            <li class="{{ Request::segment(2) == $page->id ? 'font-bold' : '' }}">
                <a class="hover:underline" href="/docs/{{ $page->id }}">{{ $page->title }}</a>
            </li>
            @endforeach
        </ul>

    </div>
</aside>
