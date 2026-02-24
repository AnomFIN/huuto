# Asetukku — static frontend package + secure admin panel

Asetukku is a static frontend demo (no backend DB). Interactive flows and popup state are local-first via `localStorage`, and admin configuration is file-based (`data/admin-settings.json`).

## Why this design
- Admin auth reads password from `.env` (`ADMIN_PANEL_PASSWORD`) and never hardcodes secrets.
- Settings are persisted into one JSON file for predictable backups, simple diffs and fast recovery.
- Validation + normalization is done server-side in `admin.php` before any write.
- CSRF token + session guard protect the settings save flow.
- Popup logic stays in a separate module (`popup-state.js`) to keep UI shell clean.

## Setup
```bash
cd asetukku
cp .env.example .env
# edit .env and set ADMIN_PANEL_PASSWORD
```

## Run (PHP + static pages)
```bash
cd asetukku
php -S 127.0.0.1:8080
```
Open:
- Front page: `http://127.0.0.1:8080/index.html`
- Admin panel: `http://127.0.0.1:8080/admin.php`

## Test
```bash
cd asetukku
node --test tests/popup-state.test.mjs
php -l admin.php
```

## Verify manually
1. Open `/admin.php`, enter password prompt **“Syötä salasana”**.
2. Change campaign title and cookie message, save, verify `data/admin-settings.json` changed.
3. Logout and verify panel is closed.
4. Open homepage and verify campaign/cookie popups still work normally.

## Troubleshooting
- If admin says password missing, ensure `.env` exists and has `ADMIN_PANEL_PASSWORD`.
- If saving fails, verify write permissions for `asetukku/data/admin-settings.json`.

## TODO (next iterations)
- Wire `admin-settings.json` dynamically to all public pages (site-wide config loader).
- Add role-based admin accounts + password hash storage.
- Add audit trail file for settings version history.
