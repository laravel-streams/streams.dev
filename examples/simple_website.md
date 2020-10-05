---
test: Foo
title: 'Simple Website'
modified: '2020-10-05 14:28:16'
description: 'A simple description will do.'
content: 'Some content'
favorite_color: '#8147eb'
uri_slug: simple-website
example_type: simple
---

title: {{ dump($entry->expand('title')) }} // Should be StringValue

---

description: {{ dump($entry->expand('description')) }} // Should be StringValue

---

content: {{ dump($entry->expand('content')) }}

---

modified: {{ dump($entry->expand('modified')) }}

---

favorite_color: {{ dump($entry->expand('favorite_color')) }}
