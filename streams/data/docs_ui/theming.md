---
title: Theming
category: developers
sort: 99
stage: outlining
enabled: true
---

1. [ ] **What** are themes?
1. [ ] How do you **use** themes?
2. [ ] How do you **build** themes?
3. [ ] How do you **extend** themes?

- **Intro:** Introduce the idea in one sentence.
- **Explanation:** An elevator pitch that signals the reader to continue or not (keep looking for relevant page).
- **Sections/Features:** Separate sections/sub-sections (h2s/h3s) consistently. This will build the ToC.
- **Next Steps:** Next actions to take that are intentional versus simply additional reading.
- **Code Examples:** Code examples and snippets.
- **Insights:** Tips, post scriptum, creative links.
- **Additional Reading:** Link to related ideas/topics/guides/recipes.

## Introduction

Streams UI theming makes use of variables, configuration, and templating to effect the output of the UI.

- Only CSS variables for easy customization
- Uses existing services/utilities to "set up the theme"
- Themes are applied at runtime

### Themes

- Themes are [Streams addons](#addons/themes) that configure the UI.
- Themes can be applied easily.
## Configuration

- We need to stash component views and more in config for customization (reference applications)

## Registered Assets

```php
Assets::register('ui::widget.css', 'ui::css/widget.css');
Assets::register('ui::widget.js', 'ui::js/widget.js');

Assets::load('ui::widget.css');
Assets::load('ui::widget.js');
```
