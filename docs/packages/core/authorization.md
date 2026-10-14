---
title: 'Core: Authorization gates'
nav_title: Authorization gates
description: Stream-config authorization gates are on the Core and API roadmaps and are not implemented in the inspected snapshot.
section: packages
package: core
order: 90
tags: [core, authorization]
status: planned
tracking_task: pending
---

Core's README lists this unfinished item: gates based on Laravel's gate layer, customized by stream config. The API README repeats that line on its roadmap.

In the inspected Core snapshot, `Integrator` can call `Gate::policy` and `Gate::define` only when an integration map includes `policies`. Nothing reads a `gates` key, or the `policy` string shown on [Streams](/docs/streams), off a stream definition.

Laravel policies you register yourself still work. This page does not document a Streams gate API, because that API is not in the snapshot. `tracking_task` stays `pending` until an Operations id exists.
