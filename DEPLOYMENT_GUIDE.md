> # Murugo Real Estate Platform: Deployment Guide

This guide provides comprehensive instructions for deploying the Murugo Real Estate Platform on a Linux server (Ubuntu 22.04) using a standard LEMP stack (Nginx, MySQL, PHP) and Postgres for the database. It also covers essential security, performance, and maintenance practices.

## Table of Contents

1.  [Prerequisites](#prerequisites)
2.  [Server Setup](#server-setup)
3.  [Database Configuration](#database-configuration)
4.  [Application Deployment](#application-deployment)
5.  [Web Server Configuration](#web-server-configuration)
6.  [SSL Certificate](#ssl-certificate)
7.  [Running the Application](#running-the-application)
8.  [Maintenance and Updates](#maintenance-and-updates)

---

## 1. Prerequisites

-   **Domain Name**: A registered domain name (e.g., `murugo.com`).
-   **Server**: A fresh Ubuntu 22.04 server instance (e.g., from AWS, DigitalOcean, or any cloud provider).
-   **SSH Access**: SSH access to the server with a non-root user with `sudo` privileges.
-   **Git**: Git installed on your local machine and on the server.

---

## 2. Server Setup

### 2.1. Initial Server Configuration

Connect to your server via SSH:

```bash
ssh your_user@your_server_ip
```

Update the package repository and upgrade existing packages:

```bash
sudo apt update && sudo apt upgrade -y
```

### 2.2. Install Nginx

Nginx is a high-performance web server that will serve the application.

```bash
sudo apt install nginx -y
```

Allow Nginx through the firewall:

```bash
sudo ufw allow 'Nginx Full'
```

### 2.3. Install PHP and Required Extensions

The application requires PHP 8.2 and several extensions.

```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-cli php8.2-common php8.2-mysql php8.2-pgsql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath -y
```

### 2.4. Install Composer

Composer is a dependency manager for PHP.

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

---

## 3. Database Configuration

### 3.1. Install PostgreSQL

```bash
sudo apt install postgresql postgresql-contrib -y
```

### 3.2. Create Database and User

Log in to the PostgreSQL interactive terminal:

```bash
sudo -u postgres psql
```

Create a new database for the application:

```sql
CREATE DATABASE murugo_db;
```

Create a new user and grant it a password (replace `your_password` with a strong password):

```sql
CREATE USER murugo_user WITH ENCRYPTED PASSWORD 'your_password';
```

Grant the new user all privileges on the new database:

```sql
GRANT ALL PRIVILEGES ON DATABASE murugo_db TO murugo_user;
```

Exit the PostgreSQL prompt:

```sql
\q
```

---

## 4. Application Deployment

### 4.1. Clone the Repository

Clone the application repository into the `/var/www` directory:

```bash
sudo git clone https://github.com/Ikaze-Murugo/miniature-den.git /var/www/murugo
```

### 4.2. Set Permissions

Change the ownership of the application directory to your user and the web server group (`www-data`):

```bash
sudo chown -R $USER:www-data /var/www/murugo
```

Set the correct permissions for the storage and bootstrap/cache directories:

```bash
sudo chmod -R 775 /var/www/murugo/storage
sudo chmod -R 775 /var/www/murugo/bootstrap/cache
```

### 4.3. Install Dependencies

Navigate to the application directory and install the PHP dependencies:

```bash
cd /var/www/murugo
composer install --optimize-autoloader --no-dev
```

### 4.4. Configure Environment

Create a `.env` file from the example file:

```bash
cp .env.example .env
```

Generate a new application key:

```bash
php artisan key:generate
```

Now, edit the `.env` file to configure the application, especially the database connection and application URL:

```bash
nano .env
```

Update the following variables:

```dotenv
APP_NAME=Murugo
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your_domain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=murugo_db
DB_USERNAME=murugo_user
DB_PASSWORD=your_password
```

### 4.5. Run Migrations and Seeders

Run the database migrations to create the application tables:

```bash
php artisan migrate --force
```

Seed the database with initial data:

```bash
php artisan db:seed --force
```

### 4.6. Optimize for Production

Cache the configuration, routes, and views for better performance:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Web Server Configuration

### 5.1. Create Nginx Server Block

Create a new Nginx server block configuration file for your domain:

```bash
sudo nano /etc/nginx/sites-available/murugo
```

Add the following configuration (replace `your_domain.com` with your actual domain):

```nginx
server {
    listen 80;
    server_name your_domain.com www.your_domain.com;
    root /var/www/murugo/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5.2. Enable the Server Block

Create a symbolic link to enable the new server block:

```bash
sudo ln -s /etc/nginx/sites-available/murugo /etc/nginx/sites-enabled/
```

Test the Nginx configuration for syntax errors:

```bash
sudo nginx -t
```

If there are no errors, restart Nginx to apply the changes:

```bash
sudo systemctl restart nginx
```

---

## 6. SSL Certificate

### 6.1. Install Certbot

Certbot is a tool to automatically obtain and renew Let's Encrypt SSL certificates.

```bash
sudo apt install certbot python3-certbot-nginx -y
```

### 6.2. Obtain SSL Certificate

Run Certbot to obtain an SSL certificate and have it automatically configure Nginx (replace `your_domain.com`):

```bash
sudo certbot --nginx -d your_domain.com -d www.your_domain.com
```

Certbot will also set up a cron job to automatically renew the certificate before it expires.

---

## 7. Running the Application

At this point, your application should be live and accessible at `https://your_domain.com`.

### 7.1. Queue Worker

If your application uses queues, you should set up a Supervisor process to keep the queue worker running.

Install Supervisor:

```bash
sudo apt install supervisor -y
```

Create a new Supervisor configuration file:

```bash
sudo nano /etc/supervisor/conf.d/murugo-worker.conf
```

Add the following configuration:

```ini
[program:murugo-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/murugo/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/murugo/storage/logs/worker.log
```

Start the Supervisor process:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start murugo-worker:*
```

---

## 8. Maintenance and Updates

### 8.1. Deploying Updates

To deploy updates to your application, SSH into your server and run the following commands from the application directory (`/var/www/murugo`):

```bash
# Enter maintenance mode
php artisan down

# Pull the latest changes from the repository
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run database migrations
php artisan migrate --force

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exit maintenance mode
php artisan up
```

### 8.2. Backups

Regularly back up your database and application files. For PostgreSQL, you can use `pg_dump`:

```bash
pg_dump -U murugo_user -d murugo_db -f backup.sql
```

For application files, you can create a tarball of the `/var/www/murugo` directory.

---

This concludes the deployment guide. Your Murugo Real Estate Platform should now be successfully deployed and ready for use.

