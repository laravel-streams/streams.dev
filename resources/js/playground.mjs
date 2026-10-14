import { PlaygroundStore } from './playground-store.mjs';
import { simulateAction } from './playground-actions.mjs';

export function initPlayground() {
    const root = document.querySelector('[data-playground]');
    if (!root || root.dataset.initialized) return;

    const seeds = JSON.parse(root.querySelector('[data-playground-seeds]').textContent);
    let store = new PlaygroundStore(seeds);
    const streamSelect = root.querySelector('[data-playground-stream]');
    const entrySelect = root.querySelector('[data-playground-entry]');
    const fields = root.querySelector('[data-playground-fields]');
    const actions = root.querySelector('[data-playground-actions]');
    const result = root.querySelector('[data-playground-result]');
    const message = root.querySelector('[data-playground-message]');
    const details = root.querySelector('[data-playground-details]');
    root.dataset.initialized = 'true';

    function clearResult() {
        result.hidden = true;
        message.textContent = '';
        details.textContent = '';
    }

    function renderFields() {
        const handle = streamSelect.value;
        const stream = store.stream(handle);
        const entry = store.entry(handle, entrySelect.value);
        fields.replaceChildren();
        actions.replaceChildren();
        clearResult();

        for (const field of stream.fields) {
            const label = document.createElement('label');
            const caption = document.createElement('span');
            caption.textContent = field.label;
            const input = document.createElement(field.type === 'textarea' ? 'textarea' : 'input');
            if (field.type !== 'textarea') input.type = field.type;
            if (field.type === 'number') input.step = 'any';
            input.value = entry[field.handle];
            input.name = field.handle;
            input.addEventListener('input', () => {
                const value = field.type === 'number' && input.value !== ''
                    ? Number(input.value) : input.value;
                store.edit(handle, entry.id, { [field.handle]: value });
                clearResult();
            });
            label.append(caption, input);
            fields.append(label);
        }

        for (const action of stream.actions) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'st-btn st-btn--sm';
            button.textContent = action.label;
            button.addEventListener('click', () => {
                const preview = simulateAction(store.stream(handle), action.handle, store.entry(handle, entry.id));
                message.textContent = preview.message;
                details.textContent = JSON.stringify({ before: preview.before, after: preview.after }, null, 2);
                result.hidden = false;
            });
            actions.append(button);
        }
    }

    function renderStream() {
        const stream = store.stream(streamSelect.value);
        entrySelect.replaceChildren();
        for (const entry of stream.entries) {
            entrySelect.add(new Option(entry.name ?? entry.title ?? entry.id, entry.id));
        }
        renderFields();
    }

    for (const handle of store.handles()) {
        streamSelect.add(new Option(store.stream(handle).name, handle));
    }
    streamSelect.addEventListener('change', renderStream);
    entrySelect.addEventListener('change', renderFields);
    root.querySelector('[data-playground-reset]').addEventListener('click', () => {
        store.reset();
        renderStream();
    });
    // Prevent any accidental form submission from ever reaching a server.
    root.querySelector('form').addEventListener('submit', (event) => event.preventDefault());
    renderStream();
    root.querySelector('[data-playground-controls]').hidden = false;

    // A back/forward-cache restore is a fresh playground, too.
    window.addEventListener('pagehide', () => {
        store.destroy();
        fields.replaceChildren();
        actions.replaceChildren();
        clearResult();
    });
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            store = new PlaygroundStore(seeds);
            renderStream();
        }
    });
}
