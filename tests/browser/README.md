Run the playground editor regression test from the repository root:

    npm install
    npx playwright install chromium
    npm run test:browser

The test starts its own PHP preview on an available localhost port, prints its PID, and stops only that process in its cleanup. It checks live JSON edits without navigation, inline JSON and schema errors, retention of the last valid preview, and recovery after a valid edit. It requires the normal Composer dependencies and local application setup.
