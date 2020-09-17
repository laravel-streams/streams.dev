<div class="c-intro">
    <div class="container mx-auto">
        <div class="flex flex-wrap">
            <div class="c-intro__body w-full">
                <h1>
                    {{ $entry->title }}
                </h1>
                {!! Str::markdown($entry->intro) !!}
            </div>
        </div>
    </div>
</div>