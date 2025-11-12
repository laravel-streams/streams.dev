## Streams.dev

Streams is a modular ecosystem of Laravel packages (Core, UI, API, SDK) for building configurable, data-driven web applications and control panels.

This repository hosts the Streams developer platform and example packages used to compose production applications. The project provides:

- Streams Core: domain-driven, code-configured streams and field types.
- Streams UI: control-panel components and builder helpers (forms, tables, pages).
- Streams API: a RESTful API layer to expose Streams data to clients.
- Streams SDK & Testing: developer tools and test helpers.

Key resources
- Documentation (developer guides, API spec, and tutorials): /streams/data/docs and /streams/data/api_docs
- Packages inventory and sample projects: /streams/packages.json and /streams/data/packages

Important notes
- The JSON API response format in existing endpoints is intentionally preserved; documentation and API changes should not change the output envelope unless explicitly noted and versioned.

Getting started
1. Install dependencies via Composer.
2. Review `streams/packages.json` to see installed Streams packages and example projects.
3. Read the API docs in `streams/data/api_docs/` for Builder-style examples and migration notes.

Filling out docs
I've added a set of skeleton API documentation pages under `streams/data/api_docs/` (Quick Start, Builder guide, Endpoints, Querying, Pagination, Auth, Errors, Migration, Testing, Examples). Each page contains a Table of Contents to make it easy to complete content incrementally.

If you'd like, I can now start implementing the Builder contract in `streams/api` after we finalize the docs and tests.
