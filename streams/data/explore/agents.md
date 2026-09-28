---
title: Building with AI agents?
label: Agents and MCP
parent: features
sort_order: 9
description: How Streams works as a machine interface for AI agents, and how agents can walk this Explore tree.
menu:
    - text: Prompt guide
      href: /docs/sdk/ai-prompts
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

Today, the SDK generators and the [prompt guide](/docs/sdk/ai-prompts) give agents a predictable way to create streams, entries, and UI. A local-development MCP server for `streams/sdk`, exposing stream introspection, entries, docs search, and the generators, is on the roadmap.

This tree is readable by agents too: add `.md` or `.json` to any Explore URL, or start from [/explore.json](/explore.json).
