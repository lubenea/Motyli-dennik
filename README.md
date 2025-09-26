# Motýlí denník — Minimal PHP + SQLite app
This package is a minimal starter for the Motýlí denník web app intended to run on a subdomain `dennik.lubenea.sk`.
It includes a simple PHP+SQLite backend, a Leaflet map for picking locations, a basic observation CRUD, and a handoff kit.

## Quick start (development)
1. Copy the folder to your PHP-enabled host (PHP 8.x recommended).
2. Ensure `data/` is writable by the webserver.
3. Visit `init_db.php` once to create the SQLite database and a default admin user (admin / password).
4. Point your webserver document root for the subdomain to this package.

## Package contents
- index.php — main dashboard / add observation form
- list_observations.php — list and basic edit/delete
- add_observation.php — POST endpoint to save observation
- db.php — PDO SQLite helper
- init_db.php — creates DB and tables + sample data
- login.php / logout.php — minimal session-based auth
- species.json — small sample species autocomplete
- assets/ — css and js (includes Leaflet)
- data/app.db — created after running init_db.php
- handoff files and docs: PROJECT_STATE.md, MIGRATION.md, CHECKS.md, CHANGELOG.md, ENV.sample

## Notes
- This is a starting point. It purposely avoids file uploads (you said no photos for now).
- For production: add CSRF protection, password hashing improvements, HTTPS, input validation, rate limiting, and user management.
