<aside class="w-aside pl-4 mr-6">

    <div class="sticky top-0 ">
        <div class="c-scrollbar w-aside__scrollbar">
        <ul>
            <li>
                <a href="/docs">Docs Home</a>
            </li>
        </ul>
        <?php $areas = ['docs' => 'Streams', 'core' => 'Core', 'ui' => 'UI']; ?>

        @if (count(request()->segments()) == 3 && request()->segment(2) == 'core')
        <?php $docs = Streams::entries('docs_core')->where('enabled', true)->orderBy('sort', 'asc')->get(); ?>
        <?php $suffix = '/core'; ?>
        @elseif (count(request()->segments()) == 3 && request()->segment(2) == 'ui')
        <?php $docs = Streams::entries('docs_ui')->where('enabled', true)->orderBy('sort', 'asc')->get(); ?>
        <?php $suffix = '/ui'; ?>
        @else
        <?php $docs = Streams::entries('docs')->where('enabled', true)->orderBy('sort', 'asc')->get(); ?>
        <?php $suffix = ''; ?>
        @endif

        <ul>
            @foreach ($stream->entries()->where('enabled', true)->orderBy('sort', 'ASC')->where('category',
            null)->get() as $page)
            <li>
                <a class="tmplink" href="{{$page->id}}">{{ $page->title }}</a>
                {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
            </li>
            @endforeach
        </ul>

        @foreach ($stream->fields->category->config['options'] as $category => $label)

        <?php $pages = $stream->entries()->where('enabled', true)->orderBy('sort', 'ASC')->where('category', $category)->get() ?>

        @if ($pages->isNotEmpty())
        <h4>{{ $label }}</h4>
        <ul>
            @foreach ($pages as $page)
            <li>
                <a href="{{$page->id}}">{{ $page->title }}</a>
                {{-- <strong>[{{ $page->stage ?: 'outlining' }}]</strong> --}}
            </li>
            @endforeach
        </ul>
        @endif

        @endforeach
        
    </div>
    </div>

</aside>