---
title: Architecture
---

# Architecture

## Introduction

Technically, Streams is a starter Laravel application with:

- [streams-core](core)
- [streams-ui](ui)
- [streams-api](api)

## What is this Streams you speak of?

Streams is a system for describing, managing, working with, accessing and building experiences upon application data for a variety of audiences.

### Data First

Data and language have brought us mastery of farm and land as well as self landing rocket ships and globally distributed experiences that shape people lives. 

We are focusing a little closer to home lol. This system was originally designed to *at least* provide a CMS experience. We are narrowing the focus while *at least* maintaining capability of a CMS of data. Let your front end team handle their business. 

The Streams Platform is a [Composer package](https://packagist.org/packages/anomaly/streams-platform) that serves as the foundation for PyroCMS. 

Nearly every functionality in every addon for PyroCMS relies on the Streams Platform to do it's work. The Streams Platform is responsible for everything from [Table Builders](/documentation/streams-platform/latest/ui/tables) to [Image](/documentation/streams-platform/latest/core-principles/image) and [Asset](/documentation/streams-platform/latest/core-principles/asset) pipelines to providing the actual addon layer itself. 

## The Application Reference

You will often see `{application}` mentioned in documentation. PyroCMS can handle multiple applications with a single installation. For this reason we use the `application_reference` to distinguish resources between potential multiple applications being served.  

### Where is my application reference?

You can find your `APPLICATION_REFERENCE` in the `.env` file within the root directory of your application.
