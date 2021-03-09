---
__created_at: 1615267622
__updated_at: 1615267622
title: Vs
intro: 'Vs PyroCMS'
enabled: true
---

<?php

$stream = Streams::make('features');
$streams = $stream->repository()->find('streams');
$pyrocms = $stream->repository()->find('pyrocms');

?>
|Feature|{{ $streams->name }}|{{ $pyrocms->name }}|
|---|---|---|
@foreach ($stream->fields->except('id') as $field)
|{{ $field->name() }}|{{ $streams->{$field->handle}['text'] }}|{{ $pyrocms->{$field->handle}['text'] }}|
@endforeach
