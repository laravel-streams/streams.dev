---
title: Command reference
nav_title: Command reference
description: 'Every Artisan command in streams/sdk: what it writes, its arguments and options, JSON output for agents, and the command classes that are not registered.'
section: packages
package: sdk
order: 20
tags: [sdk, commands]
status: ready
---

`streams/sdk` adds Artisan commands to generate and check streams, entries, addons, schemas, and Livewire components. Install it as a dev dependency:

```bash
composer require --dev streams/sdk:1.0.x-dev
```

Commands are registered only when the app runs in the console. Run `php artisan list` to see what your installed version provides.

## Available commands

| Command | What it does |
|---------|--------------|
| [`make:stream`](#makestream) | Writes a validated `streams/{id}.json` |
| [`make:entry`](#makeentry) | Creates or updates an entry in the stream's source |
| [`make:addon`](#makeaddon) | Scaffolds an addon package in `addons/{vendor}/{name}/` |
| [`streams:list`](#streamslist) | Lists registered streams, as a table or JSON |
| [`streams:validate`](#streamsvalidate) | Checks definitions against the JSON Schema and the running app |
| [`streams:schema`](#streamsschema) | Writes a `{id}.schema.json` entry schema per stream |
| [`streams:livewire`](#streamslivewire) | Generates Livewire 3 index, form, and show components |

Agents can call the same operations through the [MCP server](/docs/mcp) (`php artisan mcp:start streams`), which needs `laravel/mcp` and Laravel 11.45+ or 12.41+.

### make:stream

```bash
php artisan make:stream {id} {--name=} {--description=} {--force}
```

Writes `streams/{id}.json`. The ID must be snake_case (lowercase letters, numbers, and underscores, starting with a letter), and it is also the file name. `--name` defaults to the ID word-cased (`blog_posts` becomes "Blog Posts"). The file sets `config.source.format` to `json` without a `source.type`, so the app's default adapter is used, and it has one `id` field of type `uuid` with `config.default: true`.

The definition is validated (the same checks as [`streams:validate`](#streamsvalidate)) before it is written, then registered. The command refuses to replace an existing file unless you pass `--force`, and it refuses an ID that the app or an addon already registers.

```bash
php artisan make:stream blog_posts --description="Articles on the blog."
# Stream created: streams/blog_posts.json
```

The file starts with `"$schema": "https://streams.dev/schema/streams.schema.json"`. That URL is served by this site at [/schema/streams.schema.json](/schema/streams.schema.json), so editors that understand `$schema` validate and autocomplete the file.

### make:entry

```bash
php artisan make:entry {stream} {input?} {--update}
```

Creates an entry in `{stream}`. `input` is query-string formatted (`title=Hello&status=draft`). The command prompts for every field you didn't provide, validates the input against the stream's rules, then saves. With `--update`, validation treats the entry as existing, and if the input includes the stream's key (`key_name`, default `id`) and that entry exists, its attributes are updated.

```bash
php artisan make:entry posts "title=Hello&status=draft"
php artisan make:entry posts "id=hello&status=live" --update
```

Validation errors are printed and nothing is saved. On success the saved entry is printed as JSON.

### make:addon

```bash
php artisan make:addon {vendor/name} {--description=} {--force}
```

Scaffolds a Composer package under `addons/{vendor}/{name}/` with a `composer.json` and a service provider at `src/{Name}Provider.php`. The name must be a valid Composer package name. Without `--description` the command asks for one. It refuses to overwrite an existing addon unless you pass `--force`.

```bash
php artisan make:addon acme/reviews --description="Product reviews."
# Created: addons/acme/reviews/composer.json
# Created: addons/acme/reviews/src/ReviewsProvider.php
```

Add the directory as a Composer path repository to install it. See [Addons](/docs/addons).

### streams:list

```bash
php artisan streams:list {--json}
```

Lists every registered stream, sorted by ID, with its name, source type, field count, and description. `--json` prints an array instead, which is what scripts and agents should read:

```json
[
    {
        "id": "posts",
        "name": "Posts",
        "description": "Blog posts.",
        "source": "filebase",
        "fields": ["id", "title", "status", "author_id"],
        "extends": null
    }
]
```

### streams:validate

```bash
php artisan streams:validate {paths?*} {--json}
```

Validates stream definitions. With no paths it checks every `streams/*.json`. Each file is checked in three steps, and later steps run only when earlier ones pass:

1. The [stream definition JSON Schema](/schema/streams.schema.json) (the same file `$schema` points at).
2. The running app: field types must be registered, `extends` must name a registered stream, adapter and model classes must exist, and `related` must sit inside `config`. A related stream that is not registered yet is a warning. So is an `@` import that does not resolve.
3. A real build with `Streams::build()`.

```bash
php artisan streams:validate
php artisan streams:validate streams/posts.json streams/authors.json --json
```

The table output ends with `N checked, M invalid.`. `--json` prints an object keyed by file, each with `valid`, `errors`, and `warnings`. The exit code is non-zero when any file is invalid, so the command works as a CI or pre-commit check.

### streams:schema

```bash
php artisan streams:schema {--include=} {--exclude=} {--path=}
```

Writes a JSON schema (`{id}.schema.json`) for the *entries* of every registered stream, built from Core's `StreamSchema`: the stream's tag metadata merged with its object schema. This is not the definition schema that `$schema` points at. `--include` and `--exclude` take comma-separated stream IDs. `--path` is relative to the project root and must already exist; it defaults to the project root.

```bash
mkdir -p storage/schemas
php artisan streams:schema --include=posts,authors --path=storage/schemas
```

### streams:livewire

```bash
php artisan streams:livewire {stream} {--type=all} {--force}
```

Generates Livewire 3 components for a stream. `--type` is `index`, `form`, `show`, or `all` (the default). The components use the stream's repository and criteria, so they work with any source adapter.

| Type | Class | What it does |
|------|-------|--------------|
| `index` | `{Stream}Index` | Paginated, sortable table with a delete action |
| `form` | `{Stream}Form` | Create and edit form, validated with rules taken from the stream's fields |
| `show` | `{Stream}Show` | Read-only view of one entry |

Classes go in `config('livewire.class_namespace')` (default `App\Livewire`, so `app/Livewire/BlogPostsIndex.php`). Views go in `resources/views/livewire/`, named after the class in kebab case (`blog-posts-index.blade.php`). Protected fields are never rendered, and a generated `integer` or `uuid` key is left out of the form.

The command stops without writing anything if any target file exists, unless you pass `--force`. It does not register routes. It prints the routes to add to `routes/web.php`:

```php
Route::get('/blog-posts', \App\Livewire\BlogPostsIndex::class)->name('blog_posts.index');
Route::get('/blog-posts/create', \App\Livewire\BlogPostsForm::class)->name('blog_posts.create');
Route::get('/blog-posts/{entry}/edit', \App\Livewire\BlogPostsForm::class)->name('blog_posts.edit');
Route::get('/blog-posts/{entry}', \App\Livewire\BlogPostsShow::class)->name('blog_posts.show');
```

The components are full-page, so they render inside your Livewire layout (`config('livewire.layout')`). Use [Streams UI](/docs/ui/introduction) instead when you want a configured control panel rather than files you own and edit.

`streams:admin` has been removed. Generate the components with `streams:livewire` and put them behind your own layout and routes.

## Not registered

These command classes exist in the SDK source but are **not registered**, so `php artisan` won't find them:

| Command | Intended purpose | State |
|---------|------------------|-------|
| `streams:show` | Show one stream's attributes | Implemented, registration commented out. Use the MCP `describe-stream` tool. |
| `entries:list` | Paginated table of a stream's entries | Implemented, registration commented out. Use the MCP `list-entries` tool. |
| `entries:show` | Show one entry | Implemented, registration commented out. Use the MCP `read-entry` tool. |
| `streams:describe` | Write `streams/{id}.json` by inspecting a URL, JSON, database table, or Eloquent model | Implemented, registration commented out |
| `streams:tap` | Call a "tap" URL with query-string input | Not implemented (empty handler) |

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
- [MCP server](/docs/mcp)
- [Stream definition schema](/docs/sdk/stream-schema)
- [Core streams](/docs/core/streams)
