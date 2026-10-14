# Playground browser acceptance tests

After the repository's normal Composer/npm setup (including `.env`):

```bash
npx playwright install chromium
npm run build
npm run test:browser
```

The runner chooses an unused local port, starts its own PHP process, prints its
PID, and stops that exact process in `finally`. It does not use an existing app
server. The server uses a disposable testing key and array sessions/cache.

Tests run every example action against both entries in all three streams. They
compare seed and registered-stream file inventories and SHA-256 hashes, check
that no action sends requests or changes the working data, and use separate
browser contexts to verify private edits and fresh seeds. Reset, reload, and
navigation are covered; synthetic page transition events exercise back/forward
cache handlers deterministically without requiring the browser to cache a page.

Playground edits live only in the current page's JavaScript memory. Switching
sample streams preserves those edits; reset, reload, navigation, or closing the
page discards them. There is no local/session storage or server write endpoint.
The fixtures under `resources/playground` deliberately are not registered as
file-backed Streams sources, so the admin panel cannot persist playground edits.
