<h2 class="playground-config__label">Preview</h2>
@if ($gap)
    <div data-playground-gap="{{ $componentId }}" role="alert">
        <p>{{ $label }} did not render. The failure was logged as a playground gap.</p>
    </div>
@else
    <div data-playground-preview="{{ $componentId }}">
        {!! $preview !!}
    </div>
@endif
