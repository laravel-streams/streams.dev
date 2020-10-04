<div class="c-intro">


    <div class="c-intro__body w-full">
        <h1>
            {{ $entry->title }}
        </h1>
        {!! Str::markdown($entry->intro) !!}
    </div>
</div>