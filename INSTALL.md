# Kumaraswamy Tax Consultancy Website Installation

## Files

- `index.php` - public responsive website
- `login.php` - client/admin login with CAPTCHA
- `register.php` - client registration
- `dashboard.php` - client dashboard, uploads and downloads
- `admin.php` - admin panel for clients, uploads and notifications
- `logout.php` - session logout
- `config.php` - database and shared security helpers
- `database.sql` - MySQL database schema and default admin
- `assets/css/style.css` - custom responsive blue/white theme
- `assets/js/app.js` - smooth navigation behavior and animations

## Setup

1. Copy the folder contents to your PHP server directory, for example `htdocs/kumaraswamy-tax-consultancy` in XAMPP.
2. Create/import the database:
   - Open phpMyAdmin.
   - Import `database.sql`.
3. Open `config.php` and update database settings if needed:
   - `$dbHost`
   - `$dbName`
   - `$dbUser`
   - `$dbPass`
4. Ensure the web server can create/write to the `uploads` folder. The app creates it automatically on first upload.
5. Visit `http://localhost/kumaraswamy-tax-consultancy/index.php`.

## Default Admin Login

- Email: `admin@kumaraswamytax.local`
- Password: `Admin@12345`

Change the admin password immediately after installation by replacing it through your own password-change workflow or updating the password hash in the database.

## Security Notes

- Passwords are stored using PHP `password_hash`.
- Database queries use PDO prepared statements.
- Sessions are regenerated after login.
- Forms include CSRF tokens.
- Login includes a simple CAPTCHA.
- For production, enable HTTPS, set secure cookie flags, restrict upload MIME types more strictly, and store private documents outside the public web root.
