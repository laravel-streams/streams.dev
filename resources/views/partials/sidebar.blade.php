<aside class="w-60">
    <div class="py-4 w-60">

        <ul>
            <li>
                <a class="font-bold" href="/docs">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                      </svg>                      
                </a>
            </li>
            @foreach(Streams::entries('docs_categories')->orderBy('sort_order', 'ASC')->get() as $category)
            <li class="mt-4">
                <span class="text-md font-bold">{{ $category->name }}</span>
                <ul class="flex flex-col mt-2">
                    @foreach (Streams::docs()->where('category', $category->id)->orderBy('sort_order', 'ASC')->get() as $page)
                    <li class="{{ Request::segment(2) == $page->id ? 'font-bold text-accent' : '' }}">
                        <a class="hover:underline" href="/docs/{{ $page->id }}">{{ $page->title }}</a>
                    </li>
                    @endforeach
                </ul>
            </li>
            @endforeach
        </ul>

    </div>
</aside>
