---
title: introduction
intro: Streams is a system for describing, managing, working with, presenting, and accessing application data.
---
#### Streams Core

The core is responsible for describing and managing your data.

#### Streams UI

The UI package is responsible for streamlining UI around your Stream data.

#### Streams API

The API package is responsible for providing a consistent and standardized RESTful API to your Stream data.

## What is a Stream?

A [Stream](streams) is a description of domain-model data.

> A Stream can initially serve as both description _and_ definition for your data thanks to out of the box NoSQL flat-file storage.

[Learn More](streams)

## Why use Streams?

### Agility

Streams is simple and easy to pick up and scaffold great ideas. It will guide you as those ideas develop and help you iterate and maintain the resulting project as well.

### Workflow

We have designed our workflow around agile development concepts from the ground up and aligned Streams with the idea of discovering the project and establishing a scope before building.

By focusing on the data and bubbling up information from there, we can help automate a large number of proximity tasks like organizing and building out UI, validation, entry repositories, converting to Eloquent and more with minimal input needed.

#### Data First

During or after the discovery phase of projects it begins to become clear that the project may need doo dads and widgets. Start first by creating stream definitions for the data that your project is to be built around. [Fields](fields) can be used to describe the data’s structure.

```json
// streams/doo_dads.json
{
	“name”: “Doo Dads”,
	“fields”: {
		“name”: “text”,
		“description”: “textarea”
	}
}
```

```json
// streams/widgets.json
{
	“name”: “Widgets”,
	“fields”: {
		“name”: “text”,
		“description”: “textarea”
	}
}
```

We now have two flat-file storage schemas ready to use.

```php
Streams::make(‘doo_dads’)->create([
	‘name’ => ‘Springy Thing’,
	‘description’ => ‘Not sure what this is but it\’s springy and awesome.’,
]);
```

And fetching...

```php
$entry = Streams::make(‘doo_dads’)->where(‘name’, ‘LIKE’, ‘Spring%’)->first();

echo $entry->name;
```

And updating...

```php
$entry = Streams::make(‘doo_dads’)->where(‘name’, ‘LIKE’, ‘Spring%’)->first();

$entry->name = ‘Clock Spring’;

$entry->save();
```

You get it.. big deal right?

What about when your project is ready to go and you expect millions of doo dads?

```php
Streams::schema(‘doo_dads’)->make();
```
