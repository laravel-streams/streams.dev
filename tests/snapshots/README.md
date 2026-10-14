# Playground HTML snapshots

Every component in `resources/playground/catalog.json` gets a named render test.
Every editable example (the catalog component's JSON defaults) also passes through
the Livewire config update path and must match the same HTML snapshot. New catalog
entries automatically become test cases; a missing snapshot fails the test.

Run the playground tests without changing snapshots:

```sh
php artisan test --filter=Playground
```

To intentionally refresh snapshots after reviewing a rendering change:

```sh
UPDATE_SNAPSHOTS=1 php artisan test --filter=PlaygroundComponentSnapshotTest
```

Review the resulting HTML diff and rerun without `UPDATE_SNAPSHOTS` before
committing. CI never enables snapshot updates. Snapshots normalize line endings, trailing whitespace,
CSRF tokens, and Livewire instance IDs/serialized runtime state, while retaining
rendered markup, labels, links, control attributes, and action directives.
These are HTML snapshots; no browser screenshot comparison is involved.

Hostile-input tests reject unknown properties, foreign/unknown builder classes,
unsafe links (including nested links), and callback configuration. A direct
Livewire action call must fail. They compare file hashes and file membership
across application code, routes, flat-file content, docs, resources, public
assets, and storage before and after each attempt. Blade's error view is warmed
before comparison so legitimate initial compilation is outside the observation.

The CI job requires the SDK catalog dependency commits to be available on GitHub;
publishing those package commits remains Ryan's responsibility.
