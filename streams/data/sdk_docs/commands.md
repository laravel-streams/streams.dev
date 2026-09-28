---
title: Command reference
description: 'Every Artisan command in streams/sdk: what it writes, its arguments and options, and which commands are not registered yet.'
sort_order: 1
status: ready
---

`streams/sdk` adds Artisan generators for streams, entries, addons, schemas, and Livewire scaffolding. Install it as a dev dependency:

```bash
composer require --dev streams/sdk:1.0.x-dev
```

Commands are registered only when the app runs in the console. Run `php artisan list` to see what your installed version provides.

## Available commands

| Command | Writes | Status |
|---------|--------|--------|
| [`make:stream`](#makestream) | `streams/{id}.json` | Stable |
| [`make:entry`](#makeentry) | An entry in the stream's source | Stable |
| [`make:addon`](#makeaddon) | `addons/{vendor}/{name}/` | Stable |
| [`streams:schema`](#streamsschema) | `{id}.schema.json` per stream | Stable |
| [`streams:livewire`](#streamslivewire) | Livewire class and view | New, not yet committed to the `1.0` branch |
| [`streams:admin`](#streamsadmin) | Admin layout, dashboard, navigation, components, routes | New, not yet committed to the `1.0` branch |

### make:stream

```bash
php artisan make:stream {id}
```

Writes `streams/{id}.json` from `stubs/stream.stub`. The name is the ID with `-` and `_` turned into spaces and word-cased. The stub sets `config.source.format` to `json` and does not set `source.type`, so the app's default adapter is used. It includes an empty `description` and one `id` field of type `uuid` with `"default": true`. The file references `$schema: https://streams.dev/schema/streams.schema.json`. That schema is not published on this site yet (Track C owns publishing it), so editors cannot validate against it.

```bash
php artisan make:stream blog_posts
# streams/blog_posts.json  ("name": "Blog Posts")
```

Overwrites an existing file without asking.

### make:entry

```bash
php artisan make:entry {stream} {input?} {--update}
```

Creates an entry in `{stream}`. `input` is query-string formatted (`title=Hello&status=draft`). The command prompts for every field you didn't provide, validates the input against the stream's rules, then saves. With `--update`, validation treats the entry as existing, and if the input includes the stream's key (`key_name`, default `id`) and that entry exists, its attributes are updated.

```bash
php artisan make:entry posts "title=Hello&status=draft"
php artisan make:entry posts "id=hello&status=published" --update
```

Validation errors are printed and nothing is saved. On success the saved entry is printed as JSON.

### make:addon

```bash
php artisan make:addon {vendor/name}
```

Scaffolds a Composer package under `addons/{vendor}/{name}/` with a `composer.json` and a service provider at `src/{Name}Provider.php`. The name must be a valid Composer package name, and the command asks for a short description.

```bash
php artisan make:addon acme/reviews
# addons/acme/reviews/composer.json
# addons/acme/reviews/src/ReviewsProvider.php
```

Add the directory as a Composer path repository to install it. See [Addons](/docs/addons).

### streams:schema

```bash
php artisan streams:schema {--include=} {--exclude=} {--path=}
```

Writes a JSON schema (`{id}.schema.json`) for every registered stream, built from Core's `StreamSchema`: the stream's tag metadata merged with its object schema. `--include` and `--exclude` take comma-separated stream IDs. `--path` is relative to the project root and must already exist; it defaults to the project root.

```bash
mkdir -p storage/schemas
php artisan streams:schema --include=posts,authors --path=storage/schemas
```

### streams:livewire

```bash
php artisan streams:livewire {stream} {--type=index} {--force}
```

Generates a Livewire component for a stream. `--type` is `index`, `form`, or `show`. It writes `app/Http/Livewire/{Stream}{Type}.php` and `resources/views/livewire/{stream}-{type}.blade.php`, then prints suggested routes. It asks before overwriting unless you pass `--force`.

The generated class uses the `App\Http\Livewire` namespace, which is Livewire 2's default. Livewire 3 discovers `App\Livewire` by default, so register the component or move it.

### streams:admin

```bash
php artisan streams:admin {stream} {--layout=sidebar} {--theme=light} {--force}
```

Generates a standalone Livewire admin for one stream. The stream must already exist or the command exits with an error.

- `resources/views/admin/layouts/app.blade.php`. `--layout` selects `stubs/admin/layouts/{layout}.stub`. The only stub shipped today is `sidebar`. `--layout=topbar` looks for a file that is not in the package, so that option fails.
- `--theme` (default `light`) is substituted for the literal token `{{ theme }}` in the layout stub. The shipped sidebar stub does not contain that token, so the option is accepted and then discarded.
- `resources/views/admin/dashboard.blade.php` and `resources/views/admin/partials/navigation.blade.php`
- index, form, and show components (it calls `streams:livewire` three times), moved to `App\Http\Livewire\Admin`
- routes under `/admin/{stream}`, which it prints and, if you confirm, appends to `routes/web.php`

This is separate from [Streams UI](/docs/ui/introduction) panels. Use UI when you want a configured control panel, and `streams:admin` when you want plain Livewire files to own and edit. Panel colors are documented in [Theming](/docs/ui/theming).

## Not available yet

These command classes exist in the SDK source but are **not registered**, so `php artisan` won't find them:

| Command | Intended purpose | State |
|---------|------------------|-------|
| `streams:list` | Paginated table of streams (`--query`, `--show`, `--per-page`, `--page`) | Implemented, registration commented out |
| `streams:show` | Show one stream's attributes | Implemented, registration commented out |
| `entries:list` | Paginated table of a stream's entries | Implemented, registration commented out |
| `entries:show` | Show one entry | Implemented, registration commented out |
| `streams:describe` | Write `streams/{id}.json` by inspecting a URL, JSON, database table, or Eloquent model | Implemented, registration commented out |
| `streams:tap` | Call a "tap" URL with query-string input | Not implemented (empty handler) |

The planned Streams MCP server will expose listing, describing, and schema tools to agents. See [MCP](/docs/mcp).

## How to extend it

`make:entry` prompts for each missing field through a console input bound as `streams.console.inputs.{type}`. `SdkServiceProvider::registerInputs()` binds string, boolean, select/enum, array, and object inputs. `number`, `decimal`, `date`, `time`, and `datetime` use the string input. `integer` is listed twice in that map; the later entry wins, so `integer` is also prompted as a string and `IntegerConsoleInput` is never bound. To prompt for a custom field type, set the full map in `config('streams.console.inputs')`. The config value replaces the default map, so copy the defaults from `registerInputs()` and add yours:

```php
// config/streams/console.php
return [
    'inputs' => [
        // ...the SDK defaults...
        'money' => \App\Console\Inputs\MoneyConsoleInput::class,
    ],
];
```

## Related

- [SDK introduction](/docs/sdk/introduction)
- [Admin panels](/docs/sdk/admin-panels)
- [Core streams](/docs/core/streams)
