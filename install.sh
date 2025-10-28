#!/bin/bash

# ==============================================================================
# Carpentry Management System - Simple Installation Script
# ==============================================================================

set -e

# Check if running as root
if [ "$(id -u)" -ne 0 ]; then
  echo "This script must be run as root. Please use sudo." >&2
  exit 1
fi

# --- User Input ---
echo "--- Carpentry App Simple Installer ---"
read -p "Enter the domain name (e.g., localhost or app.yourdomain.com): " DOMAIN
read -sp "Enter a password for the database user 'carpentry_user': " DB_PASSWORD
echo
read -p "Enter the email address for the admin user: " ADMIN_EMAIL
read -sp "Enter the password for the admin user: " ADMIN_PASSWORD
echo

# --- Variables ---
DB_NAME="carpentry_db"
DB_USER="carpentry_user"
PROJECT_DIR="/var/www/carpentry-app"
WEB_USER="www-data"

echo "--- Installing system dependencies... ---"

# Update system
apt-get update

# Install basic packages
apt-get install -y curl wget git unzip software-properties-common

# Add PHP repository
add-apt-repository ppa:ondrej/php -y
apt-get update

# Install Nginx
apt-get install -y nginx

# Install PHP 8.3 and extensions
apt-get install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-pgsql php8.3-zip php8.3-gd php8.3-mbstring \
    php8.3-curl php8.3-xml php8.3-bcmath php8.3-intl php8.3-readline

echo "--- Configuring PHP for large file uploads... ---"

# Configure PHP for large file uploads (500MB)
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 500M/' /etc/php/8.3/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 500M/' /etc/php/8.3/fpm/php.ini
sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.3/fpm/php.ini
sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.3/fpm/php.ini

# Install PostgreSQL
apt-get install -y postgresql postgresql-contrib

# Install Composer
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
fi

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs

echo "--- Setting up database... ---"

# Create database user and database
sudo -u postgres createuser --pwprompt --superuser $DB_USER || echo "User may already exist"
sudo -u postgres createdb --owner=$DB_USER $DB_NAME || echo "Database may already exist"

# Configure PostgreSQL authentication
echo "local   $DB_NAME   $DB_USER   md5" | sudo tee -a /etc/postgresql/16/main/pg_hba.conf
sudo systemctl restart postgresql

echo "--- Setting up application... ---"

# Create project directory and copy files
mkdir -p $PROJECT_DIR
cp -r . $PROJECT_DIR
cd $PROJECT_DIR

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Configure Laravel
cp .env.example .env
sed -i "s/APP_URL=.*/APP_URL=http:\/\/$DOMAIN/" .env
sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
sed -i "s/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/" .env
sed -i "s/# DB_PORT=3306/DB_PORT=5432/" .env
sed -i "s/# DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/# DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/# DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env

# Generate key and run migrations
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

# Create admin user
php artisan tinker --execute="App\\Models\\User::create(['emri' => 'Admin', 'mbiemri' => 'User', 'email' => '$ADMIN_EMAIL', 'fjalekalimi_hash' => Illuminate\\Support\\Facades\\Hash::make('$ADMIN_PASSWORD'), 'rol_id' => 1, 'aktiv' => 1]);"

# Set permissions
chown -R $WEB_USER:$WEB_USER $PROJECT_DIR
chmod -R 775 $PROJECT_DIR/storage
chmod -R 775 $PROJECT_DIR/bootstrap/cache
mkdir -p $PROJECT_DIR/storage/logs
mkdir -p $PROJECT_DIR/storage/app/public/dokumentet_projekti
chown -R $WEB_USER:$WEB_USER $PROJECT_DIR/storage
chmod -R 775 $PROJECT_DIR/storage/app/public/dokumentet_projekti
php artisan storage:link

echo "--- Clearing Laravel cache... ---"
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo "--- Configuring web server... ---"

# Remove default nginx site
rm -f /etc/nginx/sites-enabled/default

# Configure Nginx for large file uploads
echo "--- Configuring Nginx for large file uploads... ---"
sed -i '/types_hash_max_size 2048;/a\\tclient_max_body_size 500M;' /etc/nginx/nginx.conf

# Create Nginx configuration for default site
cat > /etc/nginx/sites-available/default <<EOF
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    
    root $PROJECT_DIR/public;
    index index.php index.html index.htm;
    
    server_name _;
    
    client_max_body_size 500M;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx

# Restart services to apply all configurations
echo "--- Restarting services... ---"
systemctl restart php8.3-fpm
systemctl restart nginx
systemctl restart postgresql

# Enable services to start on boot
systemctl enable nginx
systemctl enable php8.3-fpm
systemctl enable postgresql

echo "=================================================="
echo "Installation Complete!"
echo "=================================================="
echo "Your application is available at: http://$DOMAIN"
echo "Admin email: $ADMIN_EMAIL"
echo ""
echo "Configuration Applied:"
echo "- PHP upload limit: 500MB"
echo "- Nginx client max body size: 500MB"
echo "- File upload directory permissions: configured"
echo "- Laravel cache: cleared"
echo "- All services: enabled and started"
echo "==================================================""
