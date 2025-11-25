# Deployment Instructions - Chatwoot CSP Fix & ML Strategy

## Overview

This deployment includes:
1. **Chatwoot Widget CSP Fix** - Resolves the Content Security Policy issue preventing the Chatwoot widget from loading
2. **ML Strategy Roadmap Document** - Comprehensive machine learning strategy for fraud detection and listing quality assurance

---

## Quick Deployment Steps

### 1. Connect to Your VPS

```bash
ssh your_user@your_vps_ip
```

### 2. Navigate to Application Directory

```bash
cd /var/www/murugo
# Or wherever your application is installed
```

### 3. Pull Latest Changes

```bash
git pull origin main
```

### 4. Clear Application Caches

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Restart PHP-FPM

```bash
# For PHP 8.2 (adjust version as needed)
sudo systemctl restart php8.2-fpm

# Verify it's running
sudo systemctl status php8.2-fpm
```

### 6. Restart Nginx

```bash
# Test configuration first
sudo nginx -t

# If OK, restart
sudo systemctl restart nginx

# Verify it's running
sudo systemctl status nginx
```

---

## What Was Fixed

### Chatwoot Widget CSP Issue

**Problem:**
The Chatwoot widget was not appearing on the site because the Content Security Policy (CSP) was blocking the script from loading.

**Console Error:**
```
Refused to load the script 'https://chat.dadishimwe.com/packs/js/sdk.js' because it violates the following Content Security Policy directive: "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com"
```

**Solution:**
Updated the `SecurityHeadersMiddleware.php` to include:
- `https://chat.dadishimwe.com` in `script-src` directive
- `https://chat.dadishimwe.com` and `wss://chat.dadishimwe.com` in `connect-src` directive (for WebSocket connections)
- `https://chat.dadishimwe.com` in `frame-src` directive (for iframe embedding)

**File Modified:**
- `app/Http/Middleware/SecurityHeadersMiddleware.php`

---

## Verification Steps

### 1. Check Chatwoot Widget

1. Visit your website: `https://murugo.dadishimwe.com`
2. Look for the Chatwoot chat widget in the **bottom-right corner** of the page
3. The widget should appear as a small circular icon with a chat bubble
4. Click on it to open the chat window

### 2. Check Browser Console

1. Open your browser's Developer Tools (F12)
2. Go to the **Console** tab
3. Refresh the page
4. **You should NOT see** any CSP errors related to `chat.dadishimwe.com`
5. **You should see** the Chatwoot SDK loading successfully

### 3. Test on Multiple Pages

Test the widget on:
- Homepage: `https://murugo.dadishimwe.com`
- Property listings: `https://murugo.dadishimwe.com/properties`
- Landlord profiles: `https://murugo.dadishimwe.com/landlords/{id}`
- Authenticated pages (after login)

The widget should appear consistently across all pages.

### 4. Test Chat Functionality

1. Click on the Chatwoot widget
2. Send a test message
3. Verify the message is sent successfully
4. Check your Chatwoot admin panel to see if the message was received

---

## Database Migrations

**No database migrations are required for this deployment.**

This update only modifies:
- Middleware configuration (CSP headers)
- Documentation (ML Strategy document)

---

## Rollback Procedure

If you encounter any issues, you can rollback:

```bash
cd /var/www/murugo

# View commit history
git log --oneline

# Rollback to previous commit
git reset --hard HEAD~1

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## Troubleshooting

### Issue: Widget Still Not Appearing

**Possible Causes:**
1. Browser cache not cleared
2. CDN cache not cleared (if using Cloudflare or similar)
3. PHP-FPM not restarted properly

**Solutions:**

1. **Clear Browser Cache:**
   - Use Ctrl+Shift+R (or Cmd+Shift+R on Mac) to hard refresh
   - Or use Incognito/Private browsing mode

2. **Clear Cloudflare Cache (if applicable):**
   - Log in to Cloudflare dashboard
   - Go to Caching → Configuration
   - Click "Purge Everything"

3. **Verify PHP-FPM Restart:**
   ```bash
   sudo systemctl status php8.2-fpm
   # If not running:
   sudo systemctl start php8.2-fpm
   ```

### Issue: CSP Errors Still Appearing

**Check:**
1. Verify the changes were pulled correctly:
   ```bash
   cd /var/www/murugo
   git log -1 --stat
   # Should show SecurityHeadersMiddleware.php as modified
   ```

2. Verify caches were cleared:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

3. Check the actual CSP header being sent:
   ```bash
   curl -I https://murugo.dadishimwe.com | grep -i "content-security-policy"
   ```

### Issue: WebSocket Connection Failing

**Check:**
1. Verify Chatwoot server is running:
   ```bash
   curl -I https://chat.dadishimwe.com
   ```

2. Check WebSocket connectivity:
   - Open browser console
   - Look for WebSocket connection errors
   - Verify `wss://chat.dadishimwe.com` is accessible

---

## ML Strategy Document

A new document has been added to the repository: **ML_STRATEGY_ROADMAP.md**

This document includes:
- Analysis of current data collection
- Recommended additional data points for ML
- Fraud detection model recommendations
- Fake listing identification strategies
- Feature engineering approaches
- Implementation roadmap
- Success metrics

**Location:** `/var/www/murugo/ML_STRATEGY_ROADMAP.md`

**To view:**
```bash
cd /var/www/murugo
cat ML_STRATEGY_ROADMAP.md
```

---

## Post-Deployment Checklist

- [ ] SSH into VPS
- [ ] Navigate to application directory
- [ ] Pull latest changes (`git pull origin main`)
- [ ] Clear all caches
- [ ] Restart PHP-FPM
- [ ] Restart Nginx
- [ ] Visit website and verify Chatwoot widget appears
- [ ] Check browser console for errors
- [ ] Test widget on multiple pages
- [ ] Send test message through widget
- [ ] Verify message received in Chatwoot admin panel
- [ ] Check ML Strategy document is present

---

## Support

If you encounter any issues during deployment:

1. Check the logs:
   ```bash
   # Laravel logs
   tail -f /var/www/murugo/storage/logs/laravel.log
   
   # Nginx error logs
   sudo tail -f /var/log/nginx/error.log
   
   # PHP-FPM logs
   sudo tail -f /var/log/php8.2-fpm.log
   ```

2. Verify all services are running:
   ```bash
   sudo systemctl status php8.2-fpm
   sudo systemctl status nginx
   ```

3. Contact: dadishimwe0@gmail.com

---

**Deployment Date:** November 24, 2025  
**Commit Hash:** c498404  
**Changes:** CSP fix for Chatwoot widget + ML Strategy document  
**Downtime Required:** None (zero-downtime deployment)
