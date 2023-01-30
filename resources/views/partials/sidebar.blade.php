<aside class="w-60">
    <div class="py-4 w-60">

        <ul>
            @foreach(Streams::entries('docs_categories')->orderBy('sort_order', 'ASC')->get() as $category)
            <li class="mt-4">
                <span class="text-md font-bold">{{ $category->name }}</span>
                <ul class="flex flex-col mt-2">
                    @foreach (Streams::docs()->where('category', $category->id)->orderBy('sort_order', 'ASC')->get() as $page)
                    <li class="{{ Request::segment(2) == $page->id ? 'font-bold' : '' }}">
                        <a class="hover:underline" href="/docs/{{ $page->id }}">{{ $page->title }}</a>
                    </li>
                    @endforeach
                </ul>
            </li>
            @endforeach
        </ul>

    </div>
</aside>
