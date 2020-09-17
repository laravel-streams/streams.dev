<div class="c-intro">
    <div class="container mx-auto">
        <h1>
            {{ $entry->title }}
        </h1>
        {!! Str::markdown($entry->intro) !!}
    </div>
</div>