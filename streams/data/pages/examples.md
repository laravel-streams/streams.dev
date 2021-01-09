---
title: Examples
---
## Example

What would you like to know?

<br>

{{ Assets::url('ui::js/index.js') }}
<br>    
{{ Assets::tag('ui::js/index.js') }}
<br>    
{{ Assets::tag('ui::css/theme.css') }}

<br>
--- 
<br>

{{ Assets::collection('test')->merge([
    'ui::js/index.js',
    'ui::css/theme.css',
])->resolved() }}

<br>
--- 
<br>

@php

Assets::register('test_pack', [
    'ui::js/index.js',
    'ui::css/theme.css',
    'ui::css/theme.css.map',
]);

@endphp

{{ Assets::collection('test_bundle')
    ->load('test_pack')
    ->urls() }}
