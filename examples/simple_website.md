---
example_title: 'Simple Website'
example_datetime: '2020-10-05 15:38:33'
example_textarea: 'A simple description will do.'
example_content: 'Some content'
example_color: '#8147eb'
example_slug: simple-website
example_select: hard
example_radio: second
title: ''
example_markdown: "# Yay!\r\nStyles are no longer colliding here :-)"
example_date: '2020-10-05'
example_time: '15:38:33'
example_range: 39
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
