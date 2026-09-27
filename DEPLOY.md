# Deploy — Portfolio (Laravel 12 + PostgreSQL)

The app reads its base address from **`APP_URL` only**, so the same code runs:

- **Now:** on a server IP in a subfolder, over HTTP → `http://103.1.2.3/portfolio`
- **Later:** on a full domain over HTTPS → `https://domain-anda.com`

Every link, asset, upload, redirect, sitemap entry and PDF export follows
`APP_URL`. Moving to a domain is an `.env` + web-server change (section B);
no code changes and no re-uploading.

Replace `103.1.2.3` with your server IP. The subfolder name used below is
`portfolio`; any name works as long as it matches in `.env` and Nginx.

---

## A. Deploy to `http://IP/portfolio` (current setup)

### 1. Server packages (once) — Ubuntu 22.04/24.04

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx postgresql git unzip curl software-properties-common
sudo add-apt-repository -y ppa:ondrej/php && sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-pgsql php8.3-mbstring php8.3-xml \
    php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath php8.3-exif
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer
```

`php8.3-gd` (with WebP) is required: uploaded images are compressed with GD.

### 2. Database (once)

```bash
sudo -u postgres psql -c "CREATE USER portfolio WITH PASSWORD 'GANTI_PASSWORD_DB';"
sudo -u postgres psql -c "CREATE DATABASE portfolio_pm OWNER portfolio;"
```

### 3. Code

The project lives **outside** the web root; only its `public/` folder is exposed.

```bash
sudo mkdir -p /var/www && cd /var/www
sudo git clone https://github.com/rendyirawann/portfolio-pm.git
sudo chown -R $USER:www-data portfolio-pm && cd portfolio-pm
composer install --no-dev --optimize-autoloader
```

### 4. `.env` for IP + subfolder (HTTP)

```bash
cp .env.example .env
nano .env
```

Set these values (the rest of `.env.example` can stay as is):

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://103.1.2.3/portfolio

DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=portfolio_pm
DB_USERNAME=portfolio
DB_PASSWORD=GANTI_PASSWORD_DB

# No HTTPS on a bare IP: these three MUST be false, or login breaks.
SESSION_SECURE_COOKIE=false
SECURITY_HSTS=false
SECURITY_CSP_UPGRADE_INSECURE=false

# Keep this app's cookies inside its subfolder (other apps may share the IP).
SESSION_PATH=/portfolio

CORS_ALLOWED_ORIGINS=http://103.1.2.3

MAIL_USERNAME=email-anda@gmail.com
MAIL_PASSWORD=gmail-app-password
MAIL_FROM_ADDRESS=email-anda@gmail.com

ADMIN_EMAIL=admin@email-anda.com
ADMIN_PASSWORD=password-admin-yang-kuat
```

```bash
php artisan key:generate
```

### 5. Migrate, seed, permissions, cache

```bash
php artisan migrate --force
php artisan db:seed --force          # roles, admin account, starter content
sudo chown -R www-data:www-data storage bootstrap/cache public/uploads
sudo chmod -R 775 storage bootstrap/cache public/uploads
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### 6. Nginx — mount `public/` at `/portfolio`

Add this inside the `server { ... }` block that answers on the IP
(usually `/etc/nginx/sites-available/default`):

```nginx
    client_max_body_size 120M;       # 10 MB per file, several files per save
    server_tokens off;

    location = /portfolio { return 301 /portfolio/; }

    location ^~ /portfolio/ {
        alias /var/www/portfolio-pm/public/;
        index index.php;
        try_files $uri $uri/ @portfolio;

        # Uploaded files are data, never code.
        location ~* ^/portfolio/uploads/.*\.(php\d*|phtml|phar|html?|svg|shtml)$ { deny all; }

        location ~* \.(css|js|png|jpe?g|gif|webp|avif|svg|ico|woff2?)$ {
            add_header X-Content-Type-Options nosniff always;
            add_header Cache-Control "public, max-age=31536000, immutable";
            try_files $uri =404;
        }

        location ~ \.php$ {
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            fastcgi_param SCRIPT_NAME /portfolio/index.php;
            fastcgi_pass unix:/run/php/php8.3-fpm.sock;
            fastcgi_hide_header X-Powered-By;
        }
    }

    location @portfolio {
        rewrite ^/portfolio/(.*)$ /portfolio/index.php?$query_string last;
    }

    location ~ /\.(?!well-known) { deny all; }
```

```bash
sudo nginx -t && sudo systemctl reload nginx
```

PHP limits — `/etc/php/8.3/fpm/php.ini`:

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

### 7. Check

- Website: `http://103.1.2.3/portfolio`
- Admin: `http://103.1.2.3/portfolio/admin/login`
- Then upload the photos again (Konten Halaman → Hero / Profil); uploaded files are not in git.

**If you use Apache instead of Nginx:** enable `mod_rewrite`, then add
`Alias /portfolio /var/www/portfolio-pm/public` plus
`<Directory /var/www/portfolio-pm/public> AllowOverride All  Require all granted </Directory>`
to the site config, and add `RewriteBase /portfolio/` under `RewriteEngine On`
in `public/.htaccess`.

---

## B. Later: move to a full domain

1. Point the domain's DNS (A record) to the server IP.
2. Nginx — a dedicated server block with the domain as root:

```nginx
server {
    listen 80;
    server_name domain-anda.com www.domain-anda.com;
    root /var/www/portfolio-pm/public;
    index index.php;
    client_max_body_size 120M;
    server_tokens off;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~* ^/uploads/.*\.(php\d*|phtml|phar|html?|svg|shtml)$ { deny all; }

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
}
```

3. HTTPS:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com
```

4. `.env` — change only these lines:

```dotenv
APP_URL=https://domain-anda.com
SESSION_PATH=/
SESSION_SECURE_COOKIE=true
SECURITY_HSTS=true
SECURITY_CSP_UPGRADE_INSECURE=true
CORS_ALLOWED_ORIGINS=https://domain-anda.com
```

5. Apply:

```bash
cd /var/www/portfolio-pm
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Uploaded images and files keep working: the database stores them as paths
relative to `public/`, never as full URLs. You can keep the old `/portfolio`
block for a while and replace it with
`location ^~ /portfolio { return 301 https://domain-anda.com$request_uri; }`
to redirect old links (strip the `/portfolio` prefix with a `rewrite` if needed).

Then check https://securityheaders.com and https://pagespeed.web.dev.

---

## Updating the code

```bash
cd /var/www/portfolio-pm
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

## Scheduler (recommended)

```bash
( crontab -l 2>/dev/null; echo "* * * * * cd /var/www/portfolio-pm && php artisan schedule:run >> /dev/null 2>&1" ) | crontab -
```
