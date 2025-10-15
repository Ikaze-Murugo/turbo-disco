# Quick Deployment Reference

## 🚀 Standard Deployment (5 Commands)

```bash
# 1. SSH to VPS
ssh your-user@your-vps-ip

# 2. Go to app directory
cd /var/www/murugo-app

# 3. Pull changes & run migrations
git pull origin main && \
docker compose exec -T murugo php artisan migrate --force

# 4. Clear caches & optimize
docker compose exec -T murugo php artisan config:clear && \
docker compose exec -T murugo php artisan cache:clear && \
docker compose exec -T murugo php artisan view:clear && \
docker compose exec -T murugo php artisan config:cache && \
docker compose exec -T murugo php artisan route:cache

# 5. Restart
docker compose restart murugo
```

---

## 📦 With Database Backup (Recommended)

```bash
# Step 1: SSH
ssh your-user@your-vps-ip

# Step 2: Navigate
cd /var/www/murugo-app

# Step 3: Backup database
cp database/database.sqlite database/database.sqlite.backup-$(date +%Y%m%d-%H%M%S)

# Step 4: Pull & Update
git pull origin main && \
docker compose exec -T murugo php artisan migrate --force && \
docker compose exec -T murugo php artisan config:clear && \
docker compose exec -T murugo php artisan cache:clear && \
docker compose exec -T murugo php artisan view:clear && \
docker compose exec -T murugo php artisan config:cache && \
docker compose restart murugo

# Step 5: Verify
docker compose ps && docker compose logs murugo --tail=20
```

---

## 🔧 If Dockerfile Changed

```bash
# Rebuild and restart
docker compose build --no-cache murugo && \
docker compose up -d murugo && \
docker compose exec -T murugo php artisan migrate --force && \
docker compose exec -T murugo php artisan config:cache
```

---

## ⚡ One-Liner (Use with caution!)

```bash
cd /var/www/murugo-app && \
cp database/database.sqlite database/database.sqlite.backup-$(date +%Y%m%d-%H%M%S) && \
git pull origin main && \
docker compose exec -T murugo php artisan migrate --force && \
docker compose exec -T murugo php artisan optimize:clear && \
docker compose exec -T murugo php artisan optimize && \
docker compose restart murugo && \
echo "✅ Deployment complete!"
```

---

## 🆘 Quick Troubleshooting

### Container not running?
```bash
docker compose ps
docker compose logs murugo --tail=50
```

### Migration failed?
```bash
docker compose exec murugo php artisan migrate:status
docker compose exec murugo php artisan migrate:rollback --step=1
```

### Permission errors?
```bash
docker compose exec murugo chmod -R 775 storage bootstrap/cache
docker compose exec murugo chown -R www-data:www-data storage bootstrap/cache
```

### Rollback deployment?
```bash
git reset --hard HEAD~1
docker compose exec murugo php artisan migrate:rollback --step=1
cp database/database.sqlite.backup-[latest] database/database.sqlite
docker compose restart murugo
```

---

## 📊 Health Check

```bash
# Check if app is running
curl -I http://your-domain.com

# Check containers
docker compose ps

# Check logs
docker compose logs -f murugo --tail=30
```

---

## 🔑 SSH Quick Connect

Save this in your local `~/.ssh/config`:

```
Host murugo-vps
    HostName your-vps-ip
    User your-username
    IdentityFile ~/.ssh/your_key
    Port 22
```

Then connect with:
```bash
ssh murugo-vps
```

