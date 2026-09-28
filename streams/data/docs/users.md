---
title: Users
nav_title: Users
description: Authentication and user streams in Streams applications.
section: guides
category: advanced
package: all
order: 40
tags: [users]
status: ready
---

## Overview

Streams does not replace Laravel authentication. Use Laravel's user model, sessions, Sanctum, or Passport as your team already does.

Define a `users` stream when you want Streams repositories, CP resources, or API exposure for user records.

## Typical setup

- Laravel `User` model for auth
- Optional `users` stream with Eloquent source pointing at the same table
- UI panel resources for admin user management
- API routes only if you intentionally expose user endpoints

## Learn more

- [Streams Core — entries](/docs/core/entries)
- [UI panels](/docs/ui/panels)
- [API authentication](/docs/api/authentication)
