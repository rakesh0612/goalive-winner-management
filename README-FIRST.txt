README-FIRST (Live FM Winner System — Cloudways, SFTP)

Upload steps (SFTP only):
1) Connect to Cloudways SFTP → go to applications/<your-app-name>/public_html/
2) Upload the contents of the 'public_html' folder from this ZIP into your server's public_html.
   - index.html (your UI with login modal)
   - import.html
   - .htaccess
   - ga/api/* (backend PHP)
3) Edit ga/api/config.php and set DB credentials from Cloudways → Access Details.
4) Create tables:
   - Open Cloudways → Application → Database Manager → SQL editor
   - Open schema.sql from this ZIP (outside public_html), copy ALL and run it.
5) Create admin (one-time):
   - Visit https://YOURDOMAIN/ga/api/setup_admin.php
   - Enter name, email, password → Submit
   - DELETE the file ga/api/setup_admin.php from the server.
6) Test:
   - Visit https://YOURDOMAIN/ → sign in (modal shows if not signed in)
   - Visit https://YOURDOMAIN/ga/api/me.php → should show JSON with your user
   - Visit https://YOURDOMAIN/import.html → upload CSV with header:
     name,phone_cc,phone_local,contest_type,win_date,show_id,batch_id,seq

Notes:
- The database has a 90-day cooldown trigger to prevent repeat winners by phone.
- Change the cooldown later by editing both the trigger (schema.sql) and the PHP ($COOLDOWN_DAYS).
- Always use HTTPS. Keep config.php secrets safe.
