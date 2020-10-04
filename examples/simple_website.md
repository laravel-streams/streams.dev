---
test: Foo
title: 'Simple Website'
modified: '2020-10-04T08:00'
description: 'A simple description will do.'
content: 'Some content'
favorite_color: '#8147eb'
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
