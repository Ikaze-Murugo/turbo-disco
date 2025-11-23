# Murugo Platform - VPS Deployment Guide

## Quick Deployment Workflow

This guide provides step-by-step instructions for deploying updates to your Murugo Real Estate Platform on your VPS.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Pulling Latest Changes](#pulling-latest-changes)
3. [Applying Changes](#applying-changes)
4. [Database Migrations](#database-migrations)
5. [Service Restart Procedures](#service-restart-procedures)
6. [Verification](#verification)
7. [Rollback Procedure](#rollback-procedure)
8. [Troubleshooting](#troubleshooting)

---

## Prerequisites

Before deploying, ensure you have:

- SSH access to your VPS
- Git configured on the server
- Proper file permissions set
- Database credentials
- Backup of current deployment (recommended)

---

## 1. Pulling Latest Changes

### Step 1: Connect to Your VPS

```bash
ssh your_user@your_vps_ip
```

### Step 2: Navigate to Application Directory

```bash
cd /var/www/murugo
# Or wherever your application is installed
```

### Step 3: Check Current Status

```bash
# Check current branch
git branch

# Check for uncommitted changes
git status

# View current commit
git log -1
```

### Step 4: Stash Any Local Changes (if needed)

```bash
# If you have local changes you want to preserve
git stash

# Or if you want to discard local changes
git reset --hard HEAD
```

### Step 5: Pull Latest Changes

```bash
# Pull from main branch
git pull origin main

# If you encounter conflicts, resolve them manually
# Then continue with:
git add .
git commit -m "Resolve merge conflicts"
```

---

## 2. Applying Changes

### Step 1: Update Dependencies

```bash
# Update Composer dependencies
composer install --no-dev --optimize-autoloader

# Update NPM dependencies (if frontend changes were made)
npm install

# Build frontend assets
npm run build
```

### Step 2: Clear Application Cache

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 3: Set Proper Permissions

```bash
# Set ownership (replace www-data with your web server user)
sudo chown -R www-data:www-data /var/www/murugo

# Set directory permissions
sudo find /var/www/murugo -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/murugo -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/murugo/storage
sudo chmod -R 775 /var/www/murugo/bootstrap/cache
```

---

## 3. Database Migrations

### Check for Pending Migrations

```bash
# Check migration status
php artisan migrate:status
```

### Run Migrations (if any)

```bash
# Run migrations
php artisan migrate --force

# If you need to rollback (use with caution)
php artisan migrate:rollback

# If you need to refresh (WARNING: This will drop all tables)
php artisan migrate:fresh --force
```

### Seed Database (if needed)

```bash
# Run seeders
php artisan db:seed --force
```

**Note:** For this deployment (Chatwoot integration and landlord profile enhancements), **NO database migrations are required** as we only modified views and layouts.

---

## 4. Service Restart Procedures

### Restart PHP-FPM

```bash
# For PHP 8.2 (adjust version as needed)
sudo systemctl restart php8.2-fpm

# Check status
sudo systemctl status php8.2-fpm
```

### Restart Nginx

```bash
# Test Nginx configuration first
sudo nginx -t

# If configuration is OK, restart
sudo systemctl restart nginx

# Check status
sudo systemctl status nginx
```

### Restart Queue Workers (if using)

```bash
# Restart Laravel queue workers
php artisan queue:restart

# If using Supervisor
sudo supervisorctl restart all

# Check supervisor status
sudo supervisorctl status
```

### Restart Laravel Scheduler (if using)

```bash
# If you're using Laravel's task scheduler, ensure cron is running
sudo systemctl status cron
```

---

## 5. Verification

### Check Application Status

```bash
# Check if the application is running
curl -I https://your-domain.com

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check Nginx status
sudo systemctl status nginx

# Check error logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/www/murugo/storage/logs/laravel.log
```

### Test Key Features

1. **Chatwoot Widget**
   - Visit your website
   - Look for the Chatwoot chat widget in the bottom-right corner
   - Test sending a message

2. **Enhanced Landlord Profiles**
   - Navigate to any landlord profile page
   - Verify contact information is displayed prominently
   - Check that location and bio are visible
   - Ensure responsive design works on mobile

3. **General Functionality**
   - Test login/logout
   - Test property listings
   - Test search functionality
   - Check image uploads

---

## 6. Rollback Procedure

If something goes wrong, you can rollback to the previous version:

```bash
# View commit history
git log --oneline

# Rollback to previous commit
git reset --hard HEAD~1

# Or rollback to specific commit
git reset --hard <commit-hash>

# Force push (if needed)
git push origin main --force

# Clear caches again
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## 7. Troubleshooting

### Issue: Changes Not Visible

**Solution:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear browser cache or use incognito mode
```

### Issue: Chatwoot Widget Not Loading

**Possible Causes:**
1. Browser blocking the script (check browser console)
2. Incorrect Chatwoot URL or token
3. CORS issues

**Solution:**
```bash
# Check browser console for errors
# Verify Chatwoot is running: curl http://chat.dadishimwe.com

# If using HTTPS, ensure Chatwoot is also on HTTPS
# Update the script to use HTTPS:
# Change: var BASE_URL="http://chat.dadishimwe.com";
# To: var BASE_URL="https://chat.dadishimwe.com";
```

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check Laravel logs
sudo tail -f /var/www/murugo/storage/logs/laravel.log

# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Ensure proper permissions
sudo chown -R www-data:www-data /var/www/murugo/storage
sudo chmod -R 775 /var/www/murugo/storage
```

### Issue: Database Connection Error

**Solution:**
```bash
# Check .env file
cat /var/www/murugo/.env | grep DB_

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Restart database service
sudo systemctl restart postgresql
```

### Issue: Permission Denied Errors

**Solution:**
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/murugo

# Fix storage permissions
sudo chmod -R 775 /var/www/murugo/storage
sudo chmod -R 775 /var/www/murugo/bootstrap/cache

# Clear and recreate cache
php artisan cache:clear
php artisan config:cache
```

---

## 8. Complete Deployment Checklist

Use this checklist for every deployment:

- [ ] Backup current deployment
- [ ] SSH into VPS
- [ ] Navigate to application directory
- [ ] Check git status
- [ ] Stash or discard local changes
- [ ] Pull latest changes from repository
- [ ] Update Composer dependencies
- [ ] Update NPM dependencies (if needed)
- [ ] Build frontend assets
- [ ] Clear all caches
- [ ] Run database migrations (if any)
- [ ] Set proper file permissions
- [ ] Restart PHP-FPM
- [ ] Restart Nginx
- [ ] Restart queue workers (if using)
- [ ] Verify application is running
- [ ] Test Chatwoot widget
- [ ] Test enhanced landlord profiles
- [ ] Check error logs
- [ ] Test key functionality

---

## 9. Quick Reference Commands

### One-Line Deployment (Use with caution)

```bash
cd /var/www/murugo && \
git pull origin main && \
composer install --no-dev --optimize-autoloader && \
php artisan migrate --force && \
php artisan cache:clear && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
sudo systemctl restart php8.2-fpm && \
sudo systemctl restart nginx
```

### Check Application Health

```bash
# Quick health check
php artisan about

# Check queue status
php artisan queue:work --once

# Check scheduled tasks
php artisan schedule:list
```

---

## 10. Post-Deployment Monitoring

### Monitor Logs in Real-Time

```bash
# Laravel logs
tail -f /var/www/murugo/storage/logs/laravel.log

# Nginx access logs
sudo tail -f /var/log/nginx/access.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

### Monitor Server Resources

```bash
# Check disk space
df -h

# Check memory usage
free -h

# Check CPU usage
top

# Check running processes
ps aux | grep php
ps aux | grep nginx
```

---

## Support

If you encounter any issues during deployment:

1. Check the logs (Laravel, Nginx, PHP-FPM)
2. Verify all services are running
3. Ensure file permissions are correct
4. Check database connectivity
5. Review the troubleshooting section above

For additional support, contact: dadishimwe0@gmail.com

---

**Last Updated:** November 23, 2025  
**Version:** 1.0  
**Deployment Type:** Standard VPS (LEMP Stack)
