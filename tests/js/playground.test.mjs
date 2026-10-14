import test from 'node:test';
import assert from 'node:assert/strict';
import { createHash } from 'node:crypto';
import { readFileSync, readdirSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { join } from 'node:path';
import { PlaygroundStore } from '../../resources/js/playground-store.mjs';
import { simulateAction } from '../../resources/js/playground-actions.mjs';

const seedDirectory = fileURLToPath(new URL('../../resources/playground/', import.meta.url));
const streamsDirectory = fileURLToPath(new URL('../../streams/', import.meta.url));
const loadSeeds = () => Object.fromEntries(['people', 'posts', 'orders'].map((handle) => [
    handle, JSON.parse(readFileSync(join(seedDirectory, `${handle}.json`), 'utf8')),
]));

// Include names as well as contents: a new/deleted file fails this comparison.
function diskSnapshot(directory) {
    const files = {};
    for (const entry of readdirSync(directory, { withFileTypes: true })) {
        const path = join(directory, entry.name);
        if (entry.isDirectory()) Object.assign(files, diskSnapshot(path));
        else files[path] = createHash('sha256').update(readFileSync(path)).digest('hex');
    }
    return files;
}

test('every example action leaves seed hashes, file inventory and working data unchanged', () => {
    const before = { seeds: diskSnapshot(seedDirectory), streams: diskSnapshot(streamsDirectory) };
    const seeds = loadSeeds();
    const store = new PlaygroundStore(seeds);
    let actionCount = 0;

    for (const handle of store.handles()) {
        const sample = store.stream(handle).entries[0];
        const field = store.stream(handle).fields[0].handle;
        store.edit(handle, sample.id, { [field]: 'A private draft' });
        const working = store.stream(handle);
        for (const action of working.actions) {
            for (const entry of working.entries) {
                const preview = simulateAction(working, action.handle, entry);
                assert.equal(preview.simulated, true);
                assert.match(preview.message, /Would .+Simulation only; nothing was changed or sent\./);
                assert.deepEqual(store.stream(handle), working);
                assert.deepEqual(loadSeeds(), seeds);
                assert.deepEqual(diskSnapshot(seedDirectory), before.seeds);
                assert.deepEqual(diskSnapshot(streamsDirectory), before.streams);
                actionCount++;
            }
        }
    }
    assert.equal(actionCount, 24, 'All four actions for both entries in all three streams ran');
});

test('two sessions keep edits private and a fresh session starts from seed', () => {
    const seeds = loadSeeds();
    const first = new PlaygroundStore(seeds);
    const second = new PlaygroundStore(seeds);
    for (const handle of first.handles()) {
        const sample = seeds[handle].entries[0];
        const field = seeds[handle].fields[0].handle;
        first.edit(handle, sample.id, { [field]: 'First session edit' });
        assert.equal(first.entry(handle, sample.id)[field], 'First session edit');
        assert.deepEqual(second.stream(handle), seeds[handle]);
        second.edit(handle, sample.id, { [field]: 'Second session edit' });
        assert.equal(first.entry(handle, sample.id)[field], 'First session edit');
    }
    first.destroy();
    assert.deepEqual(first.handles(), []);
    const fresh = new PlaygroundStore(seeds);
    for (const handle of fresh.handles()) assert.deepEqual(fresh.stream(handle), seeds[handle]);
    assert.deepEqual(loadSeeds(), seeds);
});

test('reset restores every stream; returned values cannot mutate the store or seeds', () => {
    const seeds = loadSeeds();
    const store = new PlaygroundStore(seeds);
    store.edit('people', 'person-1', { name: 'Draft' });
    store.edit('posts', 'post-1', { title: 'Draft' });
    store.edit('orders', 'order-1', { total: 99 });
    const stream = store.stream('people');
    stream.entries[0].name = 'Leaked mutation';
    assert.equal(store.entry('people', 'person-1').name, 'Draft');
    const returned = store.edit('people', 'person-1', { name: 'Another draft' });
    returned.name = 'Leaked edit result';
    assert.equal(store.entry('people', 'person-1').name, 'Another draft');
    store.reset();
    for (const handle of store.handles()) assert.deepEqual(store.stream(handle), seeds[handle]);
});

test('previews show the proposed effect and return independent data', () => {
    const store = new PlaygroundStore(loadSeeds());
    const post = store.entry('posts', 'post-1');
    const publish = simulateAction(store.stream('posts'), 'publish', post);
    assert.equal(publish.before.status, 'draft');
    assert.equal(publish.after.status, 'published');
    publish.before.title = 'Mutation';
    publish.after.title = 'Mutation';
    assert.equal(post.title, 'Welcome to Streams');
    assert.equal(store.entry('posts', 'post-1').status, 'draft');
    const order = store.entry('orders', 'order-1');
    assert.equal(simulateAction(store.stream('orders'), 'refund', order).after.status, 'refunded');
    assert.equal(simulateAction(store.stream('orders'), 'delete', order).after, null);
    assert.equal(simulateAction(store.stream('orders'), 'create', order).before, null);
    const person = store.entry('people', 'person-1');
    assert.match(simulateAction(store.stream('people'), 'invite', person).message, /alex@example.test/);
});

test('unknown streams, entries, fields and actions fail without mutating data', () => {
    const seeds = loadSeeds();
    const store = new PlaygroundStore(seeds);
    assert.throws(() => store.stream('../pages'));
    assert.throws(() => store.stream('__proto__'));
    assert.throws(() => store.edit('people', 'missing', { name: 'Draft' }));
    assert.throws(() => store.edit('people', 'person-1', { id: 'other' }));
    assert.throws(() => store.edit('people', 'person-1', { name: {} }));
    assert.throws(() => simulateAction(store.stream('people'), 'publish', store.entry('people', 'person-1')));
    assert.throws(() => simulateAction(store.stream('people'), 'delete', { id: 'missing' }));
    for (const handle of store.handles()) assert.deepEqual(store.stream(handle), seeds[handle]);
});
