---
title: Workflows
nav_title: Workflows
description: 'Streams\Core\Support\Workflow runs named steps in order and fires before and after callbacks.'
section: guides
category: development
package: all
order: 70
tags: [workflows]
status: ready
---

`Streams\Core\Support\Workflow` is a small ordered step runner in Core. It is not a job pipeline, a queue workflow, or an agent workflow. The Core README still lists replacing it with Laravel pipelines as an open question, so treat it as a stable class with an undecided future, not as a product surface you should build a framework on.

Nothing in Core, UI, or the API dispatches a workflow for you. You subclass it when you want named steps and callbacks in your own code.

## Run steps

Subclass `Workflow`, set `$steps`, and call `process()`. Each step is resolved through the container. A class name uses `handle`. A `Class@method` string or a `[Class::class, 'method']` pair calls that method.

```php
use Streams\Core\Support\Workflow;

class PublishPost extends Workflow
{
    public array $steps = [
        'validate' => ValidatePost::class,
        'notify' => NotifySubscribers::class.'@handle',
    ];
}

(new PublishPost)->process(['post' => $post]);
```

`process()` returns `void`. It stores the array on `$workflow->payload` and passes that array to `App::call()` as named arguments, so a step method receives values whose parameter names match the array keys. It does not merge return values back into the payload. Share mutable state by passing an object.

For each step named `validate`, the runner fires `before_validate`, runs the step, then fires `after_validate`.

## Add steps

```php
$workflow->addStep('archive', ArchivePost::class);          // append
$workflow->doFirst('guard', GuardPost::class);               // position 0
$workflow->doBefore('notify', 'audit', AuditPost::class);    // before an existing name
$workflow->doAfter('validate', 'stamp', StampPost::class);   // after an existing name
```

`doBefore` and `doAfter` look the target up with `array_search` and pass that position to `addStep()`, which type-hints `?int`. A missing target makes `array_search` return `false`, and the call throws `TypeError`. Check that the target name exists.

## Callbacks

`Workflow` uses Core's `FiresCallbacks` trait.

```php
$workflow->addCallback('before_validate', function ($post) {
    // $post is the payload value whose key matches this parameter
});
```

`fire()` also calls a method on the workflow when one exists. The method name is `on` plus the callback name in camel case, so `before_validate` looks for `onBeforeValidate`.

`passThrough($object)` forwards each callback to that object. The object's matching `on…` method runs first:

```php
$workflow->passThrough($listener); // $listener->onBeforeValidate($post)
```

Global listeners use `Workflow::addCallbackListener('before_validate', $callback)`. The listener key is the class name plus the callback name, so register it on your subclass, not on `Workflow`, if you want it scoped to that subclass.

## Related

- [Callbacks](/docs/core/callbacks)
- [Agents](/docs/agents)
- [Extending Core](/docs/core/extending-core)
