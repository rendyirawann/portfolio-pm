# Deploy — Portfolio (Laravel 12 + PostgreSQL)

Target: an Ubuntu 22.04/24.04 VPS with Nginx, PHP 8.3-FPM and PostgreSQL,
served over HTTPS. Replace `domain-anda.com` with your domain.

## 1. Server packages (once)

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx postgresql git unzip curl software-properties-common
sudo add-apt-repository -y ppa:ondrej/php && sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-pgsql php8.3-mbstring php8.3-xml \
    php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath php8.3-exif
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer
```

`php8.3-gd` (with WebP) is required: uploaded images are compressed with GD.

## 2. Database (once)

```bash
sudo -u postgres psql -c "CREATE USER portfolio WITH PASSWORD 'GANTI_PASSWORD_DB';"
sudo -u postgres psql -c "CREATE DATABASE portfolio_pm OWNER portfolio;"
```

## 3. Get the code

```bash
sudo mkdir -p /var/www && cd /var/www
sudo git clone https://github.com/rendyirawann/portfolio-pm.git
sudo chown -R $USER:www-data portfolio-pm && cd portfolio-pm
composer install --no-dev --optimize-autoloader
```

## 4. `.env`

```bash
cp .env.example .env
nano .env        # fill every <...> value — see the notes below
php artisan key:generate
```

Values that must be set:

| Key | Value |
|---|---|
| `APP_ENV` / `APP_DEBUG` | `production` / `false` |
| `APP_URL` | `https://domain-anda.com` (used in sitemap, OG tags, PDF export) |
| `DB_*` | the user/password from step 2 |
| `MAIL_USERNAME` / `MAIL_PASSWORD` | Gmail + an **App Password** (Google Account → Security → App passwords) |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | the first admin login (used once by the seeder) |
| `SESSION_SECURE_COOKIE`, `SECURITY_HSTS` | `true` (HTTPS only) |
| `TRUSTED_PROXIES` | `*` only if the site is behind Cloudflare/a load balancer |

## 5. Migrate, seed, permissions, cache

```bash
php artisan migrate --force
php artisan db:seed --force          # roles, admin account, starter portfolio content
sudo chown -R www-data:www-data storage bootstrap/cache public/uploads
sudo chmod -R 775 storage bootstrap/cache public/uploads
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

After the first login, upload the photos (Konten Halaman → Hero / Profil) —
uploaded files are not in git.

## 6. Nginx

`/etc/nginx/sites-available/portfolio`:

```nginx
server {
    listen 80;
    server_name domain-anda.com www.domain-anda.com;
    root /var/www/portfolio-pm/public;
    index index.php;
    client_max_body_size 120M;           # 10 MB per file, several files per save
    server_tokens off;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    # Uploaded files are data, never code.
    location ^~ /uploads/ {
        location ~* \.(php\d*|phtml|phar|html?|svg|shtml)$ { deny all; }
        add_header X-Content-Type-Options nosniff always;
        add_header Cache-Control "public, max-age=31536000, immutable";
        try_files $uri =404;
    }

    location ~* \.(css|js|png|jpe?g|gif|webp|avif|svg|ico|woff2?)$ {
        add_header X-Content-Type-Options nosniff always;
        add_header Cache-Control "public, max-age=31536000, immutable";
        try_files $uri =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known) { deny all; }

    gzip on;
    gzip_types text/css application/javascript application/json image/svg+xml application/xml;
}
```

```bash
sudo ln -s /etc/nginx/sites-available/portfolio /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

PHP upload limits — `/etc/php/8.3/fpm/php.ini`:

```ini
upload_max_filesize = 12M
post_max_size = 120M
max_file_uploads = 40
memory_limit = 512M
expose_php = Off
```

```bash
sudo systemctl restart php8.3-fpm
```

## 7. HTTPS

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com
```

Then check https://securityheaders.com and https://pagespeed.web.dev.

## 8. Scheduler & queue (recommended)

```bash
( crontab -l 2>/dev/null; echo "* * * * * cd /var/www/portfolio-pm && php artisan schedule:run >> /dev/null 2>&1" ) | crontab -
```

## Updating later

```bash
cd /var/www/portfolio-pm
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

## Shared hosting (cPanel) instead of a VPS

1. Upload the project **outside** `public_html` (e.g. `~/portfolio-pm`), or clone it via cPanel → Git.
2. Point the domain's document root to `~/portfolio-pm/public`
   (or copy `public/*` into `public_html` and fix the two paths in `public_html/index.php`).
3. Create a **PostgreSQL** database in cPanel and fill `.env` (the admin search uses PostgreSQL's `ilike`, so MySQL is not supported as-is).
4. In cPanel → Terminal: run steps 4 and 5 above.
5. Enable AutoSSL, then set `SECURITY_HSTS=true`.
