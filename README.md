# Pi Studio Pilates - PHP + PostgreSQL Multilingual Website

A modern, secure, multilingual (TR/EN) website for Pi Studio Pilates built with PHP 8+ and PostgreSQL (Supabase).

## Features

- **Multilingual Support**: Turkish (tr) and English (en) with URL structure `/{lang}/{slug}`
- **Clean URLs**: SEO-friendly URLs with Apache .htaccess rewrite rules
- **Video Playlist**: Seamless looping video background with multiple video support
- **Admin Panel**: Complete CMS for managing pages, settings, and contact messages
- **Security**: CSRF protection, rate limiting, PDO prepared statements, bcrypt password hashing
- **SEO Optimized**: Meta tags, Open Graph, Twitter Cards, hreflang tags
- **Responsive Design**: Mobile-first, modern, minimal aesthetic
- **GDPR Compliant**: Cookie consent banner, privacy controls

## Technology Stack

- **Backend**: PHP 8+ with PDO
- **Database**: PostgreSQL (Supabase/Bolt Database)
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Web Server**: Apache with mod_rewrite

## Requirements

- PHP 8.0 or higher
- PostgreSQL 12+ (provided by Supabase/Bolt)
- Apache web server with mod_rewrite enabled
- PHP extensions: pdo, pdo_pgsql, mbstring, openssl

## Installation

### Step 1: Clone or Extract Project

Extract the project files to your web server directory.

### Step 2: Configure Environment Variables

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your Supabase/Bolt database credentials:
   ```env
   PGHOST=aws-0-eu-central-1.pooler.supabase.com
   PGPORT=6543
   PGDATABASE=postgres
   PGUSER=postgres.your_project_id
   PGPASSWORD=your_database_password

   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://pistudiopilates.com
   ```

### Step 3: Import Database Schema

Run the PostgreSQL migration file to create tables and seed initial data:

```bash
psql -h your_host -p 6543 -U your_user -d postgres -f pistudio.sql
```

Or use a PostgreSQL client (pgAdmin, DBeaver, etc.) to execute the `pistudio.sql` file.

The migration will create:
- `users` table with admin user (username: **pinar**, password: **pistudyo2025!**)
- `pages` and `page_translations` tables with TR/EN content
- `settings` table with site configuration
- `media` and `contact_messages` tables

### Step 4: Configure Web Server

#### Apache Configuration

The project includes a `.htaccess` file in `/public` directory. Ensure:

1. Your document root points to `/public` directory
2. `mod_rewrite` is enabled
3. `AllowOverride All` is set for the directory

Example Apache VirtualHost:

```apache
<VirtualHost *:80>
    ServerName pistudiopilates.com
    DocumentRoot /var/www/pistudio/public

    <Directory /var/www/pistudio/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### For Development

Use PHP's built-in server:

```bash
cd public
php -S localhost:8000
```

### Step 5: Upload Video Files

Place your background video files in `/public/assets/video/`:

- `bg.mp4`
- `bg1.mp4`
- `bg2.mp4`
- `bg3.mp4`

The video playlist will automatically loop through available files. Missing files are skipped gracefully.

### Step 6: Set Permissions

Ensure the `storage` directory is writable:

```bash
chmod -R 755 storage/
chmod -R 755 storage/uploads/
```

## Admin Panel Access

### Login Credentials

- **URL**: `https://yourdomain.com/admin/login.php`
- **Username**: `pinar`
- **Password**: `pistudyo2025!`

**IMPORTANT**: Change the admin password immediately after first login!

### Admin Features

- **Pages**: Manage all page content with TR/EN translations
- **Settings**: Configure site name, WhatsApp number, Instagram links, video playlist
- **Messages**: View contact form submissions, export to CSV
- **Account**: Change admin password with security validation

## Configuration

### WhatsApp Integration

Configure WhatsApp number in Settings (Admin Panel):

```
+05417672104
```

The number is automatically formatted for WhatsApp links.

### Instagram Links

Two Instagram accounts can be configured:

- **Studio**: `https://www.instagram.com/pi_studyo_pilatess`
- **Instructor**: `https://www.instagram.com/uverrcinnkaa`

### Video Playlist

Configure in Settings as JSON array:

```json
["assets/video/bg.mp4","assets/video/bg1.mp4","assets/video/bg2.mp4","assets/video/bg3.mp4"]
```

### Email Notifications (Optional)

To receive email notifications for contact form submissions, configure SMTP in `.env`:

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_FROM_EMAIL=info@pistudiopilates.com
```

## URL Structure

### Frontend URLs

- Home (TR): `/tr/home` or `/`
- Home (EN): `/en/home`
- Instructor (TR): `/tr/egitmen`
- Instructor (EN): `/en/instructor`
- Contact (TR): `/tr/contact`
- Contact (EN): `/en/contact`

### Admin URLs

- Login: `/admin/login.php`
- Dashboard: `/admin/index.php`
- Logout: `/admin/logout.php`

## Security Features

1. **CSRF Protection**: All forms include CSRF tokens
2. **Rate Limiting**: Login and contact form submissions are rate-limited
3. **Password Hashing**: Bcrypt with PHP's `password_hash()`
4. **Session Security**: HttpOnly, Secure, SameSite cookies
5. **Input Validation**: Server-side validation and XSS protection
6. **SQL Injection Prevention**: PDO prepared statements
7. **Session Regeneration**: Session ID regenerated on login

## Database Schema

### Tables

- **users**: Admin user accounts
- **pages**: Page records (language-neutral)
- **page_translations**: Multilingual page content (TR/EN)
- **settings**: Key-value configuration store
- **media**: Uploaded files and assets
- **contact_messages**: Contact form submissions

### Seed Data

The migration includes:
- Admin user: pinar / pistudyo2025!
- Pages: home, egitmen, instructor, contact
- Full instructor profile content in TR and EN
- Site settings with WhatsApp and Instagram links

## Customization

### Adding New Pages

1. Insert page in `pages` table
2. Add translations in `page_translations` for both TR and EN
3. Update navigation in `/public/index.php` if needed

### Changing Styles

Edit `/public/assets/css/style.css` for frontend styles
Edit `/public/assets/css/admin.css` for admin panel styles

### Video Behavior

The video playlist JavaScript (`/public/assets/js/main.js`) handles:
- Seamless looping through all videos
- Automatic restart when playlist ends
- Preloading for smooth transitions
- Graceful handling of missing files

## Troubleshooting

### Database Connection Errors

- Verify `.env` credentials match your Supabase/Bolt database
- Check PostgreSQL server is accessible
- Ensure `pdo_pgsql` PHP extension is installed

### 404 Errors on Clean URLs

- Enable `mod_rewrite` in Apache
- Set `AllowOverride All` for the directory
- Check `.htaccess` file exists in `/public`

### Admin Login Issues

- Clear browser cache and cookies
- Verify password is exactly: `pistudyo2025!`
- Check database connection

### Video Not Playing

- Ensure video files exist in `/public/assets/video/`
- Check file permissions (readable by web server)
- Verify video format (MP4, H.264 codec recommended)
- Check browser console for errors

## Performance Optimization

- **Caching**: Apache caching headers configured in `.htaccess`
- **Compression**: Gzip compression enabled for text files
- **Lazy Loading**: Images lazy-loaded with modern browser features
- **Database**: Indexes on frequently queried columns

## Production Deployment

1. Set `APP_DEBUG=false` in `.env`
2. Set `SESSION_SECURE=true` (requires HTTPS)
3. Configure SSL certificate
4. Enable HTTPS redirect in `.htaccess` (uncomment lines)
5. Review and update security headers
6. Change default admin password
7. Configure email notifications
8. Set up regular database backups

## Support

For issues or questions:
- Check this README first
- Review PHP error logs
- Verify database connectivity
- Check Apache error logs

## License

This project is custom-built for Pi Studio Pilates.

---

**Admin Login**: pinar / pistudyo2025!
**WhatsApp**: +05417672104
**Instagram Studio**: [@pi_studyo_pilatess](https://www.instagram.com/pi_studyo_pilatess)
**Instagram Instructor**: [@uverrcinnkaa](https://www.instagram.com/uverrcinnkaa)
