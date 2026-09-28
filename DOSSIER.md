# Streams Dossier

> The working brief for Streams: what it is, why it exists, where every piece stands today, and the plan to reach a release candidate (RC).
> Status: approaching RC. This file describes the system **as it is**, then lays out the tracks that move it forward.
> Last updated: 2026-09-27 (America/Chicago).

---

## 1. Vision

Streams is an **agentic-first Laravel TALL-stack package system**. TALL means Tailwind, Alpine, Laravel, and Livewire. Streams grew out of the PyroCMS Streams engine.

It's a general-purpose toolkit for building nearly anything on Laravel. The packages cover:

- **Data modeling**: streams, fields, entries, criteria, and repositories.
- **UI**: code-configured admin panels and reusable components.
- **REST API**: a criteria-scoped hypermedia API that describes itself.
- **SDK and tooling**: generators, stubs, and a machine interface for agents.
- **Testing**: shared test harnesses.

**Domain-driven design is the core pattern.** Streams writes good design down as configuration and conventions, so both people and AI agents produce **predictable, consistent output** when they build on it.

**Where it's used in production:** Pinclicks and Group Vitals.

---

## 2. Package map (`~/Sites/_packages/`)

| Package | Composer / npm | Branch | State | Role |
|---|---|---|---|---|
| `streams-core` | `streams/core` | `2.0` | Active | Data platform: streams, fields, entries, criteria and repository abstraction |
| `streams-ui` | `streams/ui` | `1.0` | Active | Code-configured panels and components (Livewire 3) |
| `streams-api` | `streams/api` | `1.0` | Active | Hypermedia REST response builder |
| `api-client` | `@laravel-streams/api-client` 3.0.0 | `master` | Active | Self-discovering fluent JavaScript client |
| `streams-sdk` | `streams/sdk` | `1.0` | Work in progress (12 uncommitted changes) | Artisan generator and stub tooling. **This is not a client SDK.** |
| `streams-testing` | `streams/testing` | `1.0` | Active, small | Shared TestCase and test service provider (testbench ^8) |

Legacy, backup, and unrelated folders in `_packages` include `pyrocms*`, `protocms`, `streams-cms*`, `streams.bak`, `streams-ui.bak`, `laravel/`, `music-theory`, `mongodb`, and `todos-app`. Together they take up roughly 3 GB. See section 7 for cleanup.

### How the packages depend on each other

```
streams/core  <── streams/ui
     ^  ^  ^ <── streams/api  <──HTTP── @laravel-streams/api-client
     |  |  └──── streams/sdk (require-dev in apps)
     |  └─────── adapters (e.g. streams/mongodb)
     └── streams/testing (dev dependency of every package)

streams.dev: requires core + ui, sdk as a dev dependency. It symlinks
_packages/streams-{core,sdk,ui,testing}. streams/api is NOT installed.
```

### 2.1 Core (`streams/core`)
- **What it is:** a flat-file, JSON-configured data platform. Its namespaces are Addon, Application, Asset, Criteria, Entry, Field, Http, Image, Repository, Stream, Support, Validation, and View. It has 145 PHP files and 101 tests, and it supports Laravel `^10|^11|^12`.
- **Key idea:** you query through a **criteria and repository abstraction**, so the storage adapter can change without any change to application code. Storage can be anything from flat files (filebase) up to a SQL database such as Postgres. The latest commit is "Adapters" (2026-08-26).
- **How to use it:** define a stream in `streams/*.json`, then query it with `Streams::entries('handle')->where(...)->get()`.
- **How to extend it:** add custom field types, adapters, and addons.

### 2.2 UI (`streams/ui`)
- **What it is:** Filament-like admin panels configured in code, built with Livewire 3 builders, resources, notifications, and components. It has 263 PHP files and 49 tests.
- **Design:** it has its own separate design system (`tailwind.preset.js` with RGB-variable palettes and `--ui-*` variables). It still builds with **Laravel Mix**.
- **To do:** bring it onto the shared monochrome tokens and move it to Vite. This is part of Track A.

### 2.3 API (`streams/api`)
- **What it is:** a hypermedia REST response builder.
  - Endpoints are scoped by criteria. Entries support List, Show, Create, Update, Patch, Delete, and Query; streams support full CRUD.
  - Responses use ETag caching through `ApiCache`.
  - Schema dumps come from `DumpApiSchema` and `CreateApiDocumentation`, and a swagger-ui bundle is included.
- **Auth:** there is none built in. **The consuming app owns the middleware**, through `api.gate` and `gate_middleware`.
- **Gaps:**
  - The OpenAPI spec (`streams/data/api_docs/openapi.yaml`) is empty (`paths: {}`).
  - The API is not installed on streams.dev, and `.env` has `STREAMS_API_ENABLED=false`.
  - A leftover `HelloWorld` controller is still in the code.

### 2.4 API Client (`@laravel-streams/api-client`)
- **What it is:** a zero-dependency JavaScript client published to npm. It discovers the API's structure on its own and offers a fluent, Laravel-style criteria builder plus a middleware pipeline.
- It was converted from TypeScript, and the planning notes are in `SUMMARY.md` and `MIGRATION.md`.
- **Cleanup:** `NOTES.md` and `README.old.md` point to outdated URLs.

### 2.5 SDK (`streams/sdk`)
- **What it is today:** Artisan generator and stub tooling. It provides `make:stream`, `make:entry`, `make:addon`, `streams:list`, `streams:describe`, `streams:schema`, `streams:tap`, `streams:livewire`, and `streams:admin`.
- **Uncommitted work (12 changes):**
  - An "AI-Powered TALL Stack Development" rebrand in the README and `composer.json`.
  - New `MakeLivewire` and `MakeAdmin` commands.
  - Admin and Livewire stubs.
  - Example streams for blog, contacts, docs, and products.
  - A `docs/` folder.
- **Why it matters now:** streams.dev symlinks this package, so the site's SDK docs already describe these uncommitted commands.
- **Plan:** fold a **local-development MCP server** into the SDK. MCP (Model Context Protocol) is the standard way AI agents call tools. The SDK stays `require-dev` and becomes the **machine interface for agents**. See Track C.

### 2.6 Testing (`streams/testing`)
- **What it is:** a shared TestCase built on orchestra/testbench `^8`, installed as a local path package.
- **Risk:** testbench ^8 effectively limits testing to Laravel 10, while core claims support for 10 through 12.

### 2.7 Agentic layer: none today
There is no MCP server, no `llms.txt`, no embeddings, and no tool-calling code. The only AI-related material is the prompt guidance in `sdk_docs/ai-prompts.md` (445 lines) and the uncommitted SDK rebrand.

---

## 3. streams.dev, the docs and marketing site

- **Stack:** Laravel 10.50.3, streams/core, streams/ui, Livewire 3, Blade, Tailwind 3.2 with the typography plugin, Alpine 3, Vite 3, Prism, tocbot, and Fuse.js for the Cmd+K search.
- **How pages are built:** there are no controllers.
  - Routes are defined in `streams/*.json`.
  - `pages.json` sends every page URL to `streams/data/pages/*.html`.
  - Each docs stream (`docs`, `core_docs`, `ui_docs`, `api_docs`, `sdk_docs`, `testing_docs`, `client_docs`, `explore`) renders through the docs view.
- **Content:** markdown with frontmatter in `streams/data/{handle}/`, rendered by `app/Support/DocumentationMarkdown.php`.
- **Search:** the index is served at `/search/docs.json` and built by `App\Support\DocsSearchIndex`.
- **Addon catalog:** stored inline in `streams/packages.json`.
- **Local development:** `php artisan serve` on `127.0.0.1:8000`, or `composer dev` on :8330 with Vite on :8331. Debugbar is on.
- **Branches:** `develop` (current), `master`, and `production`. There are also 8 stale dependabot branches.

---

## 4. Design direction

**Target:** monochrome, ethereal, glassy, modern, and sleek. Shapes are **rounded, not brutalist**, and geometric, with no visual noise.

**Where the design stands today:**
- The tokens in `resources/scss/_tokens.scss` are light-mode monochrome, which already matches `STYLE.md` ("black, white, and subtle grays only").
- There is no dark mode.
- The only glass effect is the `backdrop-blur-sm` on the top bar.
- The buttons use an inline, brutalist offset-shadow style that is repeated by hand rather than built as a component.
- Some classes sit outside the tokens (`bg-gray-50`, `bg-slate-50`).
- A few stale color sources remain: `themes.json`, the blue `theme_color` in `manifest.json`, and the Tailwind v2 settings `mode: jit` and `purge`.
- streams-ui keeps its own separate design system.

**Worth preserving: the "explore" logic-tree experience on the live streams.dev.**
- It's an interactive path that walks a reader through how to use Streams.
- The local `develop` branch has the `explore` stream and route, but no content, so `/explore/*` returns 404.
- The full capture is in [Appendix A](#appendix-a-live-explore-capture). The content and views are recoverable from the `production` branch.

---

## 5. Documentation state

- **Size:** about 110 pages. The hub has 30, core 23, UI 18, API 19, SDK 6, testing 6, and client 8.
- **Broken links:**
  - `/docs/client/introduction` returns 404, because the sidebar assumes every package has an intro page.
  - `/ui` returns 500 (Livewire can't find its `form` component).
  - `/addons/streams/mongodb` returns 500 (the catalog lists it as `ryanthompson/mongodb`).
  - `/explore/*` returns 404.
- **Outdated:**
  - The install guide clones from `github.com:streams/…`, but the correct org is `laravel-streams`.
  - The docs pin Laravel 10 and PHP 8.0.2, while core supports Laravel 10 through 12.
  - Install commands use `1.0.x-dev`.
  - Each package repo keeps its own `docs/` folder, and those have drifted from the site.
- **Needed:**
  - One source of truth for the docs.
  - Agent docs.
  - A real API reference built from OpenAPI.
  - API auth guidance.
  - A reference for the SDK commands.
  - Theming docs.
  - A changelog, plus upgrade and versioning docs.
  - `/llms.txt` and `/llms-full.txt`.
  - Raw markdown for every page (`/docs/{pkg}/{id}.md`).
  - An `AGENTS.md` in each repo.
  - A JSON Schema for stream definitions.

**Docs standard:** every package and feature covers four things. What it is, why it exists, how to use it, and how to extend it.

---

## 6. Tracks

### Track A: Redesign and components
- Build one shared layout shell from `layouts/docs` and the partials, and fold `page`, `addons`, and `blank` into it.
- Create Blade components: button, card, pill, code block, callout, glass panel, and nav.
- Build out the token system:
  - Monochrome light and dark schemes.
  - Glass layers (translucent surfaces with a backdrop blur and a thin border).
  - Elevation, radius, and motion tokens.
- Move to Tailwind v4 (or a clean v3 setup) and upgrade Vite. Remove `themes.json` and fix the manifest colors.
- Rebuild the homepage without the brutalist buttons.
- Carry the explore logic tree forward in the new design language.
- Bring streams-ui onto the same tokens and move it off Laravel Mix.

### Track B: Docs
- Make streams.dev the single source of truth. Remove the per-repo `docs/` folders, or sync them from the site automatically.
- Fix the broken links, and make a missing package return a 404 instead of a 500. Add a title and meta description to every page, and use an absolute favicon path.
- Correct the GitHub org and version pins. Document the versions each package actually supports.
- Add the new sections: agentic (MCP, `llms.txt`, workflows), the API reference, API auth, tenancy, and caching, the SDK command reference, theming, adapters, and the changelog.
- Serve `llms.txt`, `llms-full.txt`, and raw markdown for each page, generated from `DocsSearchIndex`.

### Track C: SDK and MCP
- Decide what happens to the 12 uncommitted SDK changes: commit them, or throw them out.
- Fold a local-development MCP server into `streams/sdk` (kept as `require-dev`). It would expose these tools:
  - `streams:list`, `streams:describe`, and `streams:schema`
  - Entries create, read, update, and delete
  - Docs search
  - The make:* generators
- Publish a JSON Schema for stream definitions, so agents can check the configs they write.
- Add an `AGENTS.md` to each repo.
- Rename things so the terms are clear: "SDK" means the PHP tooling and MCP, "client" means the JavaScript client.

### Track D: RC gap analysis
- Streams owns this track. Measure what's missing against what Pinclicks and Group Vitals actually need.
- **Adopt semantic versioning (semver) before Group Vitals depends on it.** Tag stable releases and stop pointing installs at `*.x-dev`.
- Line up the version constraints:
  - Testing uses testbench ^8, while core claims Laravel ^10–^12.
  - No package declares a PHP constraint.
  - streams.dev runs Laravel 10.
- Fix the naming:
  - Three repos all call themselves `streams/streams`.
  - Namespaces are mixed across `streams/*`, `laravel-streams/*`, and `@laravel-streams/*`.
  - The catalog has an invalid UUID and lists packages that don't exist.

---

## 7. Cleanup before the redesign

- Archive the backup and legacy folders in `_packages` (about 3 GB). Review the 37 uncommitted changes in `streams-ui.bak` before removing it.
- Prune the stale branches in streams.dev, core, ui, and api. Remove the unused `submodules/` folder from streams.dev.
- Commit or clean the uncommitted streams.dev work: pail, the `composer dev` script, and the Vite port.
- Delete leftover artifacts:
  - The `HelloWorld` controller
  - `docs.bak/`
  - `public/vendor/web-tinker`
  - Laravel Mix leftovers
  - `locustfile.py`
  - `sami.php`
  - `coverage/` directories
  - `.DS_Store` files

---

## 8. How the work runs

- **Streams steers:** this agent owns the plan, the priorities, and review.
- **Light work** (summarizing, drafting, and small edits) goes to Qwen or DeepSeek.
- **Heavy builds** (redesign, MCP, and multi-file refactors) go to the Claude or Astra CLI.
- **Permissions:** Ryan has given full autonomy. Work can be delegated freely, with no confirmation needed for each step.

---

## Appendix A: Live explore capture

Captured 2026-09-27 from https://streams.dev and the `production` branch. Nothing here has been ported to `develop` yet.

### What it is
- Explore is a 14-page "choose your path" funnel.
- It's built entirely from server-rendered links. Each choice is a plain `<a>` that does a full page load, and `instant.page` prefetches links on hover.
- There is no JS state, Alpine, Livewire, or hash routing.
- The "tree" only exists as the links written into each node.

### URLs
- The entry point is `/explore/idea`. The header "Explore" button and the homepage "Explore More" button both link there.
- `/explore` itself returns 404; there's no index page.
- The pattern is a single flat level, `/explore/{id}`. `{id}` is the name of the markdown file.
- Four targets are dead and return 404: `/explore/core`, `/explore/mission`, `/explore/rad`, and `/explore/cli`.

### Node tree
```
idea: "Explore your project or idea with Laravel Streams."
  [Ok, let's do it!] start | [No, thanks.] Twitter
start: "New Laravel project or an existing one?"
  [New Project] new | [Existing Project] existing
new: "Creating a new Laravel Streams project."   [Explore Features] features; resources: installation, examples
existing: "What kind of support does your project need?"   [Tell us on Discord] | [Explore Features] features
features (hub): "What kind of features does your project require?"
  [Data Modeling] data: "Data is where we begin." links to core/streams and core/sources docs
  [User Interface] ui: links to ui/introduction (describes UI as "still in heavy development")
  [REST API] api: API docs and API client docs
  [Bespoke Functionality] bespoke: "Do your thing."
  [Third-party PHP Packages] packages: "We play nicely with everyone."
  [Open Source] open-source: philosophy essay, "Disagree?" link to Discord
  [Multi-Tenancy] multi-tenancy: links to core/applications docs
  (each leaf has a Back to Features button)
Orphans (no page links to them):
  principles: 6 value buttons, all rendering href="" (dead)
  streams: an older duplicate of data
Planned but never built (commented out): cli, code generation, planning, starters
```

### Content model (worth keeping)
- Each node is one markdown file in `streams/data/explore/{id}.md`, defined by `streams/explore.json`. The stream is translatable and has an admin table.
- Each file's front matter drives three kinds of navigation:
  - `menu`: stacked, full-width choices
  - `options`: an inline row of choices
  - `links`: a small "More resources" row
- Each item has a `text`, an `href`, and a `type`, where `type` is `primary`, `secondary`, or `link`.
- The schema also declares a `parent` field, but no node uses it.
- The files render through `resources/views/explore.blade.php` and `resources/views/layouts/explore.blade.php`.

### Visual pattern
- The content column is two-thirds of the page width and centered.
- The headline is the question, set very large and extra-bold: `text-4xl`, then `sm:text-6xl`, then `lg:text-7xl`, with `font-extrabold`, tight tracking, and `gray-900` text.
- Body text is `text-2xl` with `leading-10`.
- The choices are big pills: `px-6 py-3 rounded-3xl font-bold text-2xl`.
  - Primary: black background, `gray-800` on hover.
  - Secondary: `gray-200` background, `gray-300` on hover.
  - Link: black text, `gray-400` on hover.
  - All three use `hover:shadow-md` with a 200ms ease-in-out transition.
- The explore pages are monochrome only. The site sets its root font size to 22px, which scales everything up.
- The pills are rounded, not brutalist, so this already fits the new design direction.

### Where the code lives
- Everything is on `production`, which is the same commit as `master` (1e68b68, 2023-09-16).
- `develop` is a separate history started on 2022-12-01, with no common ancestor with `production`. It never had the explore data or views; nothing was deleted.
- `develop`'s `explore.json` was rewritten on 2026-06-04 to render through the `docs` view, and it dropped the `menu`, `options`, `links`, and `parent` fields.
- An empty, untracked `streams/data/explore/` folder appeared locally on Sep 27 at 21:50 CT.
- To recover the files, run `git show production:streams/data/explore/<id>.md` and `git show production:resources/views/explore.blade.php`.

### To fix when porting (Track A/B)
- Add an `/explore` index page or a redirect.
- Fix the dead links, and give `principles` real `href`s.
- In `api.md`, remove the trailing commas in the labels and the stray quote in `type: link'`.
- Remove the orphaned `streams.md`.
- Fix the typos ("Javacsript", "in tact").
- Either use `parent` for breadcrumbs or drop it.
- Remove the dead Google Analytics tag (Universal Analytics was shut down).
- Rework it as agentic-first: add an "agents / MCP" branch, and serve each node as raw markdown or JSON so agents can walk the same tree.

### Live vs local homepage
- **Live:** "Optimized Laravel Development" hero with unDraw illustrations, a black Mac-window code panel showing `pages.json`, and an "Explore More" call to action. Square black and gray buttons, a black footer, and Laravel Mix with Tailwind 3.1.7 and Alpine 2.8.
- **Local:** a sticky blurred top bar, quick-link cards, and addon cards with brutalist offset-shadow buttons. Vite, Tailwind 3.2, a Cmd+K search, and **no Explore link**.
- **Keep:** the Mac-window code panel and the Explore call to action. The local Cmd+K search is the better of the two.

### Browser walkthrough notes
- Every step is a full page load, and the browser tab title stays "Streams" on every page.
- There are no breadcrumbs, no step counter, no progress bar, and no transitions between pages. The only motion is the button hover (a shadow and a color change).
- Moving back works through each leaf's "Back to Features" button or the browser's back button.
- The explore pages have no code samples. The only code block on the site is the homepage's black `pages.json` window.
- The header has the logo on the left, then a light gray search field, a gray "Explore" block, and a black "Get Started" block with a stacked label and an arrow.
- The footer is black, with gray icons.
- The illustrations are flat and a muted purple-gray (`#2f2e41`, `#3f3d56`). They're the only color in the explore flow besides the traffic-light dots.
- The live docs have a gray "pre-release" banner and two duplicate "Installation" cards. A debug line ("0.13 s | 16 mb") shows in the sidebar in production.
- **Worth keeping:** the huge question headlines, the black pill choices, the generous white space, and the header layout. The redesign should add the things the live flow lacks: a sense of where you are (a breadcrumb or path, built from `parent`), gentle transitions, and a unique title for each page.

### Screenshots
Saved in `.dossier/` next to this file.
- Live homepage: `.dossier/live-home-top.png`
- Explore entry (`/explore/idea`): `.dossier/explore-idea.png`
- Mid node (`/explore/start`): `.dossier/explore-start.png`
- Features hub pills: `.dossier/explore-features.png`
- Leaf (`/explore/ui`): `.dossier/explore-ui-leaf.png`
- Live docs: `.dossier/live-docs.png`
