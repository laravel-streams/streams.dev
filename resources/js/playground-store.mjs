const copy = (value) => structuredClone(value);

/** A page's private working copy. No cookies, storage, requests or shared state. */
export class PlaygroundStore {
    #seeds;
    #streams;

    constructor(seeds) {
        this.#seeds = copy(seeds);
        this.reset();
    }

    reset() {
        this.#streams = copy(this.#seeds);
    }

    destroy() {
        this.#seeds = {};
        this.#streams = {};
    }

    handles() {
        return Object.keys(this.#streams);
    }

    stream(handle) {
        if (!Object.hasOwn(this.#streams, handle)) {
            throw new Error('Unknown playground stream.');
        }

        return copy(this.#streams[handle]);
    }

    entry(handle, id) {
        const entry = this.stream(handle).entries.find((entry) => entry.id === id);
        if (!entry) throw new Error('Unknown playground entry.');
        return entry;
    }

    /** Editing the sample is allowed; running an action never edits it. */
    edit(handle, id, changes) {
        const stream = this.stream(handle);
        const entry = this.entry(handle, id);
        const fields = new Set(stream.fields.map((field) => field.handle));
        for (const [field, value] of Object.entries(changes)) {
            if (!fields.has(field) || !['string', 'number'].includes(typeof value)) {
                throw new Error('Unknown field or invalid playground value.');
            }
            entry[field] = value;
        }
        const index = stream.entries.findIndex((entry) => entry.id === id);
        this.#streams[handle].entries[index] = copy(entry);
        return copy(entry);
    }
}
