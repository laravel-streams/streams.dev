---
title: Introduction
description: 'The system architecture and terminology.'
sort_order: 0
category: getting-started
status: ideation
---

👋 Welcome to Laravel Streams!

## What is Streams?

Streams is a system of unified packages providing an optimized foundation and workflow for **Laravel development**.

Application fundamentals like **data modeling**, **API interaction**, **user interfaces**, and more are defined with code-configured JSON files and establish best-practices design principles to support your work.

> The Streams system leans heavily on domain-driven design (DDD). We call these domain abstractions `streams`, hence our namesake.

---

### Motivation

The ever-changing and widening landscape of web applications, websites, and the like, has stressed the traditions and ideology of popular CMS options. And, after digging into our own CMS engine, we discovered that the problem is in our CMS paradigm. This project results from the deconstruction of that paradigm and establishes a new one built upon new fundamental principles and unique goals via unified ala-mode packages.

### Use Cases

Laravel Streams and its components are well suited to build various applications:

- Websites
- Prototyping
- App Backbone
- Headless CMS
- Integrated CMS
- Code Generator
- Application Core
- Project Bootstraps
- Development Automation


Time to get your feet wet!

<!-- @foreach (Streams::entries('docs')->where('category', 'getting_started')->orderBy('sort', 'asc')->get() as $doc)
- [{{$doc->link_title ?: $doc->title}}]({{$doc->id}})
@endforeach -->

## Core Packages

Know what you are looking for already? Dive right into our core packages.

- [Streams Core](core/introduction)
- [Streams UI](ui/introduction)
- [Streams API](api/introduction)
- [Streams CLI](cli/introduction)


## Community Resources

- <a href="https://discord.gg/vhz8NZC" rel="noreferrer noopener">Discord</a>
- <a href="https://stackoverflow.com/search?q=laravel+streams" rel="noreferrer noopener">Stack Exchange</a>
- <a href="https://github.com/laravel-streams/streams" rel="noreferrer noopener">GitHub</a>
- <a href="https://www.youtube.com/channel/UC4a-uVtWOHNCduY5T7_Q4wA" rel="noreferrer noopener">YouTube</a>
