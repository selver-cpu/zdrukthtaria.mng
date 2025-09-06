# Production Deployment Guide

## Server Requirements

- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- Node.js 16+ and NPM 8+
- Web server (Nginx/Apache)
- Redis (recommended for caching/queues)
- Supervisor (for queue workers)

## Deployment Steps

1. **Server Setup**
   ```bash
   # Install required PHP extensions
   sudo apt update
   sudo apt install -y php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-curl
   ```

2. **Clone Repository**
   ```bash
   git clone [repository-url] /var/www/carpentry-app
   cd /var/www/carpentry-app
   ```

3. **Install Dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install --production
   npm run build
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

6. **Storage and Permissions**
   ```bash
   php artisan storage:link
   chown -R www-data:www-data /var/www/carpentry-app
   chmod -R 775 storage bootstrap/cache
   ```

7. **Queue Workers**
   Set up Supervisor to manage queue workers:
   ```
   [program:laravel-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/carpentry-app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   stopasgroup=true
   killasgroup=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/log/worker.log
   ```

8. **Scheduled Tasks**
   Add this to crontab:
   ```
   * * * * * cd /var/www/carpentry-app && php artisan schedule:run >> /dev/null 2>&1
   ```

## Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/carpentry-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Security Hardening

1. **File Permissions**
   ```bash
   chmod -R 750 /var/www/carpentry-app
   chmod -R 770 /var/www/carpentry-app/storage/
   chmod -R 770 /var/www/carpentry-app/bootstrap/cache/
   ```

2. **HTTPS**
   - Use Let's Encrypt for free SSL certificates
   - Configure HTTP/2
   - Enable HSTS

3. **Environment Variables**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Set `APP_URL` to your production URL

## Monitoring

1. **Logging**
   - Configure `LOG_CHANNEL=daily`
   - Set up log rotation

2. **Performance Monitoring**
   - Consider using Laravel Telescope
   - Set up error tracking (Sentry, Bugsnag)

## Backup Strategy

1. **Database Backups**
   ```bash
   # Daily database backup
   0 0 * * * mysqldump -u username -p database_name > /backups/db-$(date +\%Y\%m\%d).sql
   ```

2. **File Backups**
   ```bash
   # Weekly file backup
   0 0 * * 0 tar -czf /backups/files-$(date +\%Y\%m\%d).tar.gz /var/www/carpentry-app/storage
   ```

## Updating the Application

```bash
cd /var/www/carpentry-app
git pull
composer install --optimize-autoloader --no-dev
php artisan migrate --force
npm install --production
npm run build
php artisan cache:clear
php artisan view:cache
php artisan config:cache
php artisan route:cache
```

## Troubleshooting

- **500 Errors**: Check Laravel logs in `storage/logs`
- **Permission Issues**: Verify file ownership and permissions
- **Queue Not Working**: Check Supervisor status `sudo supervisorctl status`
- **Scheduled Tasks**: Verify cron is running and the path is correct
