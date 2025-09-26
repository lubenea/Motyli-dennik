# MIGRATION
1. Copy files to your server.
2. Make sure `data/` is writable by the webserver user.
3. Visit /init_db.php to create the SQLite DB and sample data.
4. Update ENV.sample -> .env and change ADMIN_PASS.
5. Secure the site (HTTPS, firewall, PHP settings).
