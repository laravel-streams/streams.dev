---
title: Introduction
intro: Streams is a system for describing, managing, working with, presenting, and accessing application data.
sort: 0
---
## The anatomy of a Streams project

### Streams Core

The Streams Core package is responsible for describing and managing your data.

### Streams UI

The Streams UI package is responsible for developing and refining a consistent UI around your Stream data.

### Streams API

The Streams API package is responsible for providing a consistent and standardized RESTful API to your Stream data.

## What is a Stream?

A [Stream](streams) is a schematic for individual pieces of domain-model data.

> A Stream can initially serve as both description _and_ definition for your data thanks to out of the box NoSQL flat-file storage.

[Learn More](streams)

## Why use Streams?

### Agility

Streams is simple and easy to pick up and scaffold great ideas. It will guide you as those ideas develop and help you iterate and maintain the resulting project as well. Streams allows early stage projects to cleanly and efficiently create complex data architecture with simple definitions.

### Workflow

The workflow of Streams centers around agile development concepts and standardises the approach of discovering a project and establishing a scope before building.

By focusing on the data schema as a single point of origin from which all other services are derived, we can help automate a large number of proximity tasks like organizing and building out UI, validation, entry repositories, converting to Eloquent and more with minimal input needed.

### Data First

The initial stages of any project involve high level description of data objects and their role within the application. Start first by creating stream definitions for the data that your project is to be built around. [Fields](fields) can be used to describe the data’s structure.

```json
// streams/doo_dads.json
{
	"name": "Doo Dads",
	"fields": {
		"name": "text",
		"description": "textarea"
	}
}
```

```json
// streams/widgets.json
{
	"name": "Widgets",
	"fields": {
		"name": "text",
		"description": "textarea"
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

### Simplifying existing projects
Streams helps existing projects structure their data in a more efficient way than
a traditional MVC approach such as with vanilla Laravel. Streams is an *enhancement* of
your existing project - you don't need to restructure to see the benefits of its data modelling. With first-class support for Eloquent and a source-agnostic approach to data storage, you'll be able to integrate an existing application in no time.

```php
// streams/widgets.json
{
    "name": "Widgets",
    "source": {
        "type": "eloquent",
        "table": "migrations",
        "model": "The\\Model\\Class"
    },
	"fields": {
		"name": "text",
		"description": "textarea"
	}
}
```
