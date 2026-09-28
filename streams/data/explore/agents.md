---
title: Building with AI agents?
label: Agents and MCP
parent: features
sort_order: 9
description: How Streams works as a machine interface for AI agents, and how agents can walk this Explore tree.
menu:
    - text: MCP server
      href: /docs/mcp
    - text: SDK Documentation
      href: /docs/sdk/introduction
      type: secondary
    - text: Back to Features
      href: /explore/features
      type: link
links:
    - text: This page as Markdown
      href: /explore/agents.md
    - text: The whole tree as JSON
      href: /explore.json
---
Laravel Streams is **agentic-first**. Good design is written down as configuration and conventions, so an agent reading your `streams/*.json` sees the same structure your team does, and produces output that fits.

`streams/sdk` ships a local-development [MCP server](/docs/mcp): run `php artisan mcp:start streams` and an agent can list and describe streams, validate the definitions it writes against the [stream definition schema](/docs/sdk/stream-schema), read and write entries, search the docs, and run the generators. It needs `laravel/mcp` (Laravel 11.45+ or 12.41+). On Laravel 10, `streams:list --json` and `streams:validate --json` give agents the same view from the command line, and the [prompt guide](/docs/sdk/ai-prompts) covers the generators.

This tree is readable by agents too: add `.md` or `.json` to any Explore URL, or start from [/explore.json](/explore.json).
