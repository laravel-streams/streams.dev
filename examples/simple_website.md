---
__created_at: 1607487074
__updated_at: 1607487074
example_title: 'Simple Website'
example_datetime: '2020-12-09 04:11:14'
example_textarea: 'A simple description will do.'
example_content: 'Some content'
example_color: '#8147eb'
example_slug: simple-website
example_select: hard
example_radio: third
title: ''
example_markdown: "# Yay!\r\nStyles are no longer colliding here :-)"
example_date: '2020-12-09'
example_time: '04:11:14'
example_range: 39
example_boolean: true
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