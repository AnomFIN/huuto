# Asetukku — static frontend package

Asetukku is a static frontend demo (no backend, no database). Interactive flows use browser `localStorage` for local-first behavior.

## Why this design
- Campaign and cookie popups are implemented with **pure popup state logic** in `popup-state.js` and a small imperative shell in `index.html`.
- Local storage access is wrapped with safe guards (`readSafeStorage`, `writeSafeStorage`) to avoid runtime breaks in restricted browser modes.
- Campaign popup has a controlled cooldown and deadline so UX stays effective, not spammy.
- Cookie acceptance persists deterministically (`accepted`) for clean compliance UX.

## Run
```bash
cd asetukku
python3 -m http.server 8080
```
Open `http://localhost:8080`.

## Test
```bash
node --test tests/popup-state.test.mjs
```

## Verify manually
1. Open homepage in private window: campaign popup should appear (before deadline) and cookie banner should appear.
2. Click **Muistuta myöhemmin** in campaign popup; refresh page: popup stays hidden during cooldown period.
3. Click **Hyväksy evästeet**; refresh page: cookie banner stays hidden.
4. Clear localStorage and verify both popups appear again.

## Troubleshooting
- If data does not load, do not use `file://` path; run through a local HTTP server.
- If popup state seems stale, clear `localStorage` keys prefixed with `asetukku:`.

## TODO (next iterations)
- Add explicit **Reject analytics** option and policy link in cookie banner.
- Move popup styling from inline block into shared CSS pipeline.
- Add Playwright smoke test for popup visibility states.
