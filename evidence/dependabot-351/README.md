# Dependabot #351 validation

Baseline: master f9770ff. Override: postcss-selector-parser 7.1.6.

- Before `npm ls postcss-selector-parser`: @tailwindcss/typography@0.5.20 → postcss-selector-parser@6.0.10.
- Before `npm audit`: 2 moderate vulnerabilities; exit 1.
- After `npm ls postcss-selector-parser`: @tailwindcss/typography@0.5.20 → postcss-selector-parser@7.1.6 overridden.
- After `npm audit`: found 0 vulnerabilities; exit 0.
- `npm run build`: passes; generated assets match master exactly. The existing six CSS `::highlight` optimizer warnings also occur before the override.
- `php artisan test`: 46 passed, 11199 assertions.
- `npm run test:js`: 3 passed.

Full-page Chromium screenshots cover `/docs/installation` and `/docs/core/streams` at 1440 × 1000 viewport, light mode, reduced motion. Before uses master's committed build; after uses the rebuilt assets with the override. Each pair is byte-identical:

- installation: SHA-256 `35c4474b8200adc9dc7588f4f9bd9c1d70d6766ca08c271389690c17441a3c1d`
- core/streams: SHA-256 `fec93add8e675d31128ad26ac6291b9f2cbad1da576d63661e13237124feab88`

Reviewed prose, headings, lists, tables, inline code, fenced code, navigation and page layout. No styling regression observed. Preview servers were started on free ports and their recorded PIDs stopped after capture; no host processes were touched.

Master's package-lock.json at f9770ff already matches all redundant PR targets:

- #35: prismjs 1.30.0.
- #36: lodash 4.18.1.
- #37: source-map-js 1.2.2.
- #38: shell-quote 1.12.0 and concurrently 9.2.5.

All four were closed with comments linking #34. No deployment performed. GitHub alert #351 requires verification after review and merge.
