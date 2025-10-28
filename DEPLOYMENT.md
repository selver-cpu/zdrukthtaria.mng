# Deployment Guide - Carpentry Management System

## Server Requirements

### Minimum System Requirements
- **OS**: Ubuntu 20.04 LTS or newer / CentOS 8+ / Debian 11+
- **RAM**: 2GB minimum (4GB recommended)
- **Storage**: 20GB minimum
- **CPU**: 2 cores minimum

### Required Software Stack

#### 1. Web Server
```bash
# Install Nginx
sudo apt update
sudo apt install nginx

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

#### 2. PHP 8.3+
```bash
# Add PHP repository
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php

# Install PHP and required extensions
sudo apt update
sudo apt install php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-pgsql php8.3-zip php8.3-gd php8.3-mbstring \
    php8.3-curl php8.3-xml php8.3-bcmath php8.3-json php8.3-intl \
    php8.3-readline php8.3-tokenizer php8.3-fileinfo
```

#### 3. PostgreSQL Database
```bash
# Install PostgreSQL
sudo apt install postgresql postgresql-contrib

# Start and enable PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Create database and user
sudo -u postgres psql
CREATE DATABASE carpentry_db;
CREATE USER carpentry_user WITH PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE carpentry_db TO carpentry_user;
\q
```

#### 4. Composer (PHP Package Manager)
```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### 5. Node.js & NPM (for asset compilation)
```bash
# Install Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

## Deployment Steps

### 1. Clone Repository
```bash
# Navigate to web directory
cd /var/www

# Clone the repository
sudo git clone <your-repository-url> carpentry-app
sudo chown -R www-data:www-data carpentry-app
cd carpentry-app
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies and build assets
npm install
npm run build
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file with your production settings:
```env
APP_NAME="Carpentry Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=carpentry_db
DB_USERNAME=carpentry_user
DB_PASSWORD=your_secure_password

# Email Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Carpentry Management"

# Session and Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### 4. Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed initial data (optional)
php artisan db:seed --force
```

### 5. File Permissions
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/carpentry-app
sudo chmod -R 755 /var/www/carpentry-app
sudo chmod -R 775 /var/www/carpentry-app/storage
sudo chmod -R 775 /var/www/carpentry-app/bootstrap/cache

# Create symbolic link for storage
php artisan storage:link
```

### 6. Nginx Configuration
Create `/etc/nginx/sites-available/carpentry-app`:
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/carpentry-app/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Security: deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # File upload size (match PHP and Laravel limits)
    client_max_body_size 500M;
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/carpentry-app /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 7. SSL Certificate (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 8. Process Management (Optional - for queues)
If using queues, install Supervisor:
```bash
sudo apt install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/carpentry-worker.conf
```

Add to supervisor config:
```ini
[program:carpentry-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/carpentry-app/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/carpentry-app/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start carpentry-worker:*
```

## Security Considerations

### 1. Firewall Configuration
```bash
# Enable UFW firewall
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
```

### 2. Regular Updates
```bash
# Create update script
sudo nano /usr/local/bin/update-carpentry-app
```

Add to update script:
```bash
#!/bin/bash
cd /var/www/carpentry-app
git pull origin main
composer install --optimize-autoloader --no-dev
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl reload nginx
```

```bash
sudo chmod +x /usr/local/bin/update-carpentry-app
```

### 3. Backup Strategy
```bash
# Database backup script
sudo nano /usr/local/bin/backup-carpentry-db
```

Add to backup script:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/carpentry"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Database backup
pg_dump -h localhost -U carpentry_user carpentry_db > $BACKUP_DIR/db_backup_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/carpentry-app/storage/app

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

```bash
sudo chmod +x /usr/local/bin/backup-carpentry-db

# Add to crontab for daily backups
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-carpentry-db
```

## Monitoring & Maintenance

### 1. Log Monitoring
- Application logs: `/var/www/carpentry-app/storage/logs/`
- Nginx logs: `/var/log/nginx/`
- PHP-FPM logs: `/var/log/php8.3-fpm.log`

### 2. Performance Optimization
```bash
# Enable OPcache
sudo nano /etc/php/8.3/fpm/conf.d/10-opcache.ini
```

Add OPcache configuration:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 3. Health Checks
Create monitoring script at `/usr/local/bin/check-carpentry-health`:
```bash
#!/bin/bash
# Check if application is responding
curl -f http://localhost/login > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "Application not responding" | mail -s "Carpentry App Alert" admin@yourdomain.com
fi
```

## Troubleshooting

### Common Issues:
1. **Permission errors**: Check file ownership and permissions
2. **Database connection**: Verify PostgreSQL credentials in `.env`
3. **Asset compilation**: Run `npm run build` after code updates
4. **Cache issues**: Clear Laravel cache with `php artisan cache:clear`

### Useful Commands:
```bash
# Clear all Laravel caches
php artisan optimize:clear

# Check application status
php artisan about

# View logs
tail -f storage/logs/laravel.log

# Check queue status (if using queues)
php artisan queue:work --verbose
```

## Initial Admin User

After deployment, create an admin user:
```bash
php artisan tinker
```

In tinker console:
```php
$user = new App\Models\User();
$user->emri = 'Admin';
$user->mbiemri = 'User';
$user->email = 'admin@yourdomain.com';
$user->fjalekalimi_hash = Hash::make('secure_password');
$user->rol_id = 1; // Admin role
$user->aktiv = 1;
$user->save();
```

## Support

For issues or questions:
- Check application logs in `storage/logs/`
- Review server logs in `/var/log/`
- Ensure all dependencies are properly installed
- Verify database connectivity and permissions
