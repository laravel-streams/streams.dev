<div data-playground class="grid gap-8">
    <script type="application/json" data-playground-seeds>{!! json_encode(\App\Support\PlaygroundSeeds::all(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
    <noscript>Enable JavaScript to try the in-memory playground.</noscript>

    <div data-playground-controls hidden>
        <form class="ui-form grid gap-8" aria-label="Playground sample editor" autocomplete="off">
            <div class="grid gap-6 sm:grid-cols-2">
                <label>
                    <span>Stream</span>
                    <select data-playground-stream name="stream"></select>
                </label>
                <label>
                    <span>Example entry</span>
                    <select data-playground-entry name="entry"></select>
                </label>
            </div>
            <p>Edit these sample values in this page's memory, then preview an action. Reloading, leaving, or closing this page discards all edits.</p>
            <div data-playground-fields class="grid gap-6"></div>
            <div data-playground-actions class="flex flex-wrap items-center gap-3"></div>
            <div>
                <x-button type="button" data-playground-reset>Reset all samples</x-button>
            </div>
        </form>
    </div>

    <div data-playground-result role="status" aria-live="polite" hidden>
        <p data-playground-message></p>
        <details class="mt-6">
            <summary>View simulated result</summary>
            <pre data-playground-details class="mt-3 overflow-auto"></pre>
        </details>
    </div>
</div>
