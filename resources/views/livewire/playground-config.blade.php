<section class="playground-config" data-playground-editor="{{ $componentId }}">
    <div>
        <h2 class="playground-config__label" id="playground-config-heading-{{ $componentId }}">Example config</h2>
        <p class="playground-config__help">
            Edit the streams make node: the builder class and the defaults passed to <code>make()</code>.
            The JSON is checked against <a href="{{ url('/schema/catalog.schema.json') }}">catalog.schema.json</a>.
            Unknown keys are rejected. A valid edit re-renders the preview here, without saving or reloading the page.
        </p>
    </div>

    <div class="st-code">
        <div class="st-code__chrome">
            <span class="st-code__lights" aria-hidden="true"><span></span><span></span><span></span></span>
            <span class="st-code__title">node.json</span>
        </div>
        <label class="sr-only" for="playground-config-{{ $componentId }}">Example config JSON</label>
        <textarea
            id="playground-config-{{ $componentId }}"
            class="playground-config__input"
            data-playground-config
            rows="16"
            spellcheck="false"
            autocapitalize="off"
            autocomplete="off"
            wire:model.live.debounce.400ms="config"
            aria-invalid="{{ $schemaErrors ? 'true' : 'false' }}"
            @if ($schemaErrors) aria-describedby="playground-config-errors-{{ $componentId }}" @endif
        >{{ $config }}</textarea>
    </div>

    <p class="playground-config__status" wire:loading data-playground-rendering>Updating the preview…</p>

    @if ($schemaErrors)
        <div class="playground-config__errors" id="playground-config-errors-{{ $componentId }}" data-playground-errors role="alert">
            <p>This JSON does not match catalog.schema.json.</p>
            <ul>
                @foreach ($schemaErrors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($shareUrl !== '')
        <p class="playground-config__help" data-playground-share>
            <a href="{{ $shareUrl }}" data-playground-share-link>Share this example</a>
        </p>
    @elseif ($shareError !== '')
        <p class="playground-config__errors" data-playground-share-error role="alert">{{ $shareError }}</p>
    @endif

    <script>
        if (!window.__playgroundPreviewListener) {
            window.__playgroundPreviewListener = true;
            window.addEventListener('playground-rendered', (event) => {
                const detail = event.detail || {};
                const stage = document.querySelector('[data-playground-stage="' + detail.id + '"]');

                if (stage && typeof detail.html === 'string') {
                    stage.innerHTML = detail.html;
                }
            });
            window.addEventListener('playground-shared', (event) => {
                const url = event.detail && event.detail.url;

                if (typeof url !== 'string' || url.charAt(0) !== '/') {
                    return;
                }

                const link = document.querySelector('[data-playground-share-link]');

                if (link) {
                    link.setAttribute('href', url);
                }

                history.replaceState(null, '', url);
            });
        }
    </script>
</section>
