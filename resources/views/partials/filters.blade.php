<aside class="w-60">
    <div class="py-4 w-60">

        <ul>
            @foreach (Streams::make('packages')->fields->get('type')->options() as $key => $type)
            <li><a class="hover:underline" href="/addons?type={{ $key }}">{{ $type }}</a></li>
            @endforeach
            {{-- <li><a class="hover:underline" href="#">Starters</a></li>
            <li><a class="hover:underline" href="#">Core</a></li> --}}
            {{-- <li><a class="hover:underline" href="#">Fields</a></li> --}}
            {{-- <li><a class="hover:underline" href="#">Inputs</a></li>
            <li><a class="hover:underline" href="#">Analytics</a></li>
            <li><a class="hover:underline" href="#">Forms</a></li>
            <li><a class="hover:underline" href="#">Dev Tools</a></li>
            <li><a class="hover:underline" href="#">Optimization</a></li> --}}
        </ul>

    </div>
</aside>
