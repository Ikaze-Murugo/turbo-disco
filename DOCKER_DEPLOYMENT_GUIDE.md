# Docker Deployment Guide for VPS

## 🚀 Complete Deployment Process

This guide covers deploying updates from GitHub to your VPS running the Murugo application in Docker.

---

## 📋 Prerequisites

Before starting, ensure you have:
- SSH access to your VPS
- Docker and Docker Compose installed on VPS
- Git installed on VPS
- Application already running in Docker containers

---

## 🔄 Deployment Steps

### **Step 1: SSH into Your VPS**

```bash
ssh your-user@your-vps-ip
# Example: ssh ubuntu@123.45.67.89
```

---

### **Step 2: Navigate to Application Directory**

```bash
cd /path/to/murugo-app
# Example: cd /var/www/murugo-app or ~/murugo-app
```

---

### **Step 3: Backup Database (IMPORTANT!)**

Before pulling changes, always backup your database:

```bash
# For SQLite
cp database/database.sqlite database/database.sqlite.backup-$(date +%Y%m%d-%H%M%S)

# Or for PostgreSQL
# docker-compose exec db pg_dump -U your_db_user your_db_name > backup-$(date +%Y%m%d-%H%M%S).sql
```

---

### **Step 4: Pull Latest Changes from GitHub**

```bash
# Stash any local changes (if any)
git stash

# Pull latest changes
git pull origin main

# If you had stashed changes and want to reapply them
# git stash pop
```

---

### **Step 5: Update Dependencies (if composer.json or package.json changed)**

**Option A: Update dependencies INSIDE the running container**
```bash
# Update PHP dependencies
docker-compose exec app composer install --optimize-autoloader --no-dev

# Update Node dependencies
docker-compose exec app npm install --production

# Build assets
docker-compose exec app npm run build
```

**Option B: Rebuild the Docker image (if Dockerfile changed)**
```bash
docker-compose build --no-cache app
```

---

### **Step 6: Run Database Migrations**

```bash
# Run migrations
docker-compose exec app php artisan migrate --force

# If you need to seed data (be careful in production!)
# docker-compose exec app php artisan db:seed --force
```

---

### **Step 7: Clear All Caches**

```bash
# Clear application caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear

# Optimize for production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

---

### **Step 8: Set Correct Permissions**

```bash
# Fix permissions for Laravel
docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

---

### **Step 9: Restart Services**

**Option A: Graceful restart (recommended)**
```bash
docker-compose restart app
```

**Option B: Full restart (if needed)**
```bash
docker-compose down
docker-compose up -d
```

**Option C: Restart specific services only**
```bash
# Restart PHP-FPM
docker-compose restart app

# Restart Nginx
docker-compose restart nginx

# Restart Queue workers (if you have them)
docker-compose restart queue
```

---

### **Step 10: Verify Deployment**

```bash
# Check if containers are running
docker-compose ps

# Check application logs
docker-compose logs -f app --tail=50

# Check nginx logs
docker-compose logs -f nginx --tail=50

# Test the application
curl -I http://your-domain.com
# Should return HTTP 200 OK
```

---

## 🔧 Troubleshooting

### **Issue: Migration Errors**

```bash
# Check migration status
docker-compose exec app php artisan migrate:status

# Rollback last migration
docker-compose exec app php artisan migrate:rollback --step=1

# Fresh migration (WARNING: This will delete all data!)
# docker-compose exec app php artisan migrate:fresh --force
```

### **Issue: Permission Denied Errors**

```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### **Issue: Composer Memory Limit**

```bash
# Run composer with more memory
docker-compose exec app php -d memory_limit=-1 /usr/bin/composer install --no-dev --optimize-autoloader
```

### **Issue: Container Won't Start**

```bash
# Check container logs
docker-compose logs app

# Check for syntax errors in Docker files
docker-compose config

# Rebuild from scratch
docker-compose down -v
docker-compose up -d --build
```

---

## 📝 Quick Deployment Commands (All-in-One)

For regular updates where only code/migrations changed:

```bash
#!/bin/bash
# save this as deploy.sh

set -e  # Exit on error

echo "🚀 Starting deployment..."

# Backup database
echo "📦 Backing up database..."
cp database/database.sqlite database/database.sqlite.backup-$(date +%Y%m%d-%H%M%S)

# Pull changes
echo "⬇️  Pulling latest changes..."
git stash
git pull origin main

# Run migrations
echo "🗄️  Running migrations..."
docker-compose exec -T app php artisan migrate --force

# Clear caches
echo "🧹 Clearing caches..."
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan view:clear

# Optimize
echo "⚡ Optimizing..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Restart
echo "🔄 Restarting services..."
docker-compose restart app

# Verify
echo "✅ Checking status..."
docker-compose ps

echo "🎉 Deployment complete!"
```

Make it executable:
```bash
chmod +x deploy.sh
```

Run it:
```bash
./deploy.sh
```

---

## 🔐 Security Best Practices

1. **Always backup before deploying**
   ```bash
   # Automated backup script
   0 2 * * * cd /var/www/murugo-app && cp database/database.sqlite backups/db-$(date +\%Y\%m\%d).sqlite
   ```

2. **Use environment variables for secrets**
   - Never commit `.env` file
   - Use Docker secrets or environment variables

3. **Keep Docker images updated**
   ```bash
   docker-compose pull
   docker-compose up -d
   ```

4. **Monitor logs regularly**
   ```bash
   docker-compose logs -f --tail=100
   ```

---

## 🐳 Docker Compose Reference

### **Common Docker Compose Commands**

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Restart services
docker-compose restart

# View logs
docker-compose logs -f app

# Execute command in container
docker-compose exec app php artisan [command]

# Build images
docker-compose build

# Pull images
docker-compose pull

# List containers
docker-compose ps

# Remove volumes (CAUTION: Deletes data)
docker-compose down -v
```

---

## 📊 Monitoring

### **Check Application Health**

```bash
# CPU and Memory usage
docker stats

# Disk usage
df -h
docker system df

# Container resource usage
docker-compose top
```

### **Database Health**

```bash
# For SQLite
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();

# Check database size
ls -lh database/database.sqlite
```

---

## 🔄 Rollback Procedure

If deployment fails:

```bash
# 1. Rollback git changes
git reset --hard HEAD~1

# 2. Rollback migrations
docker-compose exec app php artisan migrate:rollback --step=1

# 3. Restore database backup
cp database/database.sqlite.backup-[timestamp] database/database.sqlite

# 4. Restart services
docker-compose restart app

# 5. Clear caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

---

## 📚 Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/10.x/deployment)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Optimization](https://laravel.com/docs/10.x/deployment#optimization)

---

## ⚠️ Important Notes

1. **Always test migrations locally first** before running on production
2. **Keep database backups** - automate daily backups
3. **Monitor disk space** - logs and databases can grow quickly
4. **Use queue workers** for heavy tasks (emails, reports)
5. **Set up proper monitoring** (Sentry, LogRocket, etc.)
6. **Enable HTTPS** with Let's Encrypt SSL certificates

---

## 🆘 Emergency Contacts

- **Application Issues**: Check `/storage/logs/laravel.log`
- **Docker Issues**: Check `docker-compose logs`
- **Database Issues**: Restore from backup immediately

---

**Last Updated**: October 15, 2025
**Deployment Target**: Production VPS (Docker)

