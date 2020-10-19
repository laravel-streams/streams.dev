---
example_title: 'Simple Websitesss'
streams__import: simple_website
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
