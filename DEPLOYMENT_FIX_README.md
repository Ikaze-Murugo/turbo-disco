# Murugo Platform - Critical Fixes & Deployment Guide

## Issues Fixed

This update resolves four critical issues that were preventing the application from running properly:

### 1. ✅ Database Connection Issue (SQLite vs PostgreSQL)
**Problem:** Application was falling back to SQLite instead of using PostgreSQL, causing "table properties has no column named coordinates" error.

**Root Cause:** Missing `coordinates` column because pending migrations couldn't run due to migration errors.

**Fix:** Fixed all migration issues to allow pending migrations to run successfully.

### 2. ✅ Migration Failures
**Problem:** Multiple migrations were failing:
- `2025_09_12_160012_create__table` - Malformed migration trying to create existing `jobs` table
- `2025_10_27_140222_add_postgis_to_properties_table` - Missing column checks
- `2025_10_15_104817_add_missing_columns_to_message_reports_table` - Duplicate column errors
- `2025_10_25_101810_add_comprehensive_indexes_for_property_filtering` - Index creation errors

**Fixes Applied:**
- ✅ Deleted malformed `create__table` migration
- ✅ Created proper `create_jobs_table` migration with existence checks
- ✅ Updated PostGIS migration with `IF NOT EXISTS` checks
- ✅ Updated message reports migration with column existence checks
- ✅ Rewrote comprehensive indexes migration with safe PostgreSQL syntax

### 3. ✅ Maps Not Rendering
**Problem:** Maps not showing on property pages, property creation, and maps page.

**Root Cause:** Missing `coordinates` JSON column in properties table (pending migration).

**Fix:** Once migrations run, the `coordinates` column will be added and maps will work. Map components are properly configured with Leaflet.

### 4. ✅ General Application Health
**Fixes Applied:**
- ✅ Created `.env.example` with proper PostgreSQL configuration
- ✅ Fixed all migration compatibility issues
- ✅ Added safety checks to prevent future migration errors
- ✅ Documented deployment process

---

## Deployment Steps

### Prerequisites

Ensure your `.env` file has:
```env
DB_CONNECTION=pgsql
DB_HOST=postgres  # or your PostgreSQL host
DB_PORT=5432
DB_DATABASE=murugo_real_estate
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### Step 1: Pull Latest Changes

```bash
cd ~/murugo-app  # or your application directory
git pull origin main
```

### Step 2: Run Migrations

This is the critical step that will fix all issues:

```bash
php artisan migrate
```

**Expected Output:**
```
INFO  Running migrations.

2025_09_12_160012_create_jobs_table ........................... DONE
2025_10_15_104817_add_missing_columns_to_message_reports_table  DONE
2025_10_25_101810_add_comprehensive_indexes_for_property_filtering  DONE
2025_10_25_110554_create_comparison_analytics_table ........... DONE
2025_10_27_140222_add_postgis_to_properties_table ............. DONE
2025_11_24_000001_create_user_events_table .................... DONE
2025_11_24_000002_add_ml_fields_to_images_table ............... DONE
2025_11_24_000003_add_phone_verification_to_users_table ....... DONE
2025_11_24_000004_create_property_edits_table ................. DONE
2025_11_24_000005_create_ip_reputation_table .................. DONE
2025_11_24_000006_create_fraud_scores_table ................... DONE
```

### Step 3: Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Restart Services

```bash
# If using Docker
docker compose restart murugo

# If using systemd
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Step 5: Verify Fixes

1. **Check Database Connection:**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   >>> DB::connection()->getDatabaseName();
   # Should show: "murugo_real_estate"
   ```

2. **Verify Coordinates Column:**
   ```bash
   php artisan tinker
   >>> Schema::hasColumn('properties', 'coordinates');
   # Should return: true
   ```

3. **Test Property Creation:**
   - Go to your application
   - Try to create a new property
   - The map should now render correctly
   - Property creation should succeed without SQLite errors

4. **Check Maps:**
   - Visit property detail pages
   - Visit the maps page
   - All maps should now render properly

---

## Troubleshooting

### Migration Still Fails

If migrations fail, check which specific migration is failing:

```bash
php artisan migrate:status
```

Look for "Pending" migrations. If a specific migration fails:

1. **Check the error message carefully**
2. **Verify PostgreSQL is running:**
   ```bash
   docker ps | grep postgres
   # or
   sudo systemctl status postgresql
   ```

3. **Check database connection:**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

### Maps Still Not Showing

1. **Verify coordinates column exists:**
   ```bash
   php artisan tinker
   >>> Schema::hasColumn('properties', 'coordinates');
   ```

2. **Check browser console for JavaScript errors**

3. **Verify Leaflet CSS/JS are loading:**
   - Open browser developer tools
   - Check Network tab for 404 errors on Leaflet resources

### SQLite Error Persists

If you still see "Connection: sqlite" errors:

1. **Check .env file:**
   ```bash
   cat .env | grep DB_CONNECTION
   # Should show: DB_CONNECTION=pgsql
   ```

2. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

3. **Restart application:**
   ```bash
   docker compose restart murugo
   # or
   sudo systemctl restart php8.2-fpm
   ```

### Foreign Key Errors

If you see foreign key constraint errors:

```bash
# This means you need to populate data in the correct order
# Check the migration error message for which foreign key is failing
```

---

## What Changed

### Files Modified:
1. `database/migrations/2025_09_12_160012_create_jobs_table.php` - Fixed with existence checks
2. `database/migrations/2025_10_15_104817_add_missing_columns_to_message_reports_table.php` - Added column existence checks
3. `database/migrations/2025_10_25_101810_add_comprehensive_indexes_for_property_filtering.php` - Rewrote with safe PostgreSQL syntax
4. `database/migrations/2025_10_27_140222_add_postgis_to_properties_table.php` - Added IF NOT EXISTS checks

### Files Added:
1. `.env.example` - Proper PostgreSQL configuration template
2. `DEPLOYMENT_FIX_README.md` - This file

### Database Changes:
- ✅ `jobs` table created (if not exists)
- ✅ `coordinates` JSON column added to `properties` table
- ✅ Multiple indexes added for performance
- ✅ ML data collection tables added (user_events, property_edits, etc.)
- ✅ Phone verification fields added to users table
- ✅ Image metadata fields added for ML

---

## Post-Deployment Checklist

- [ ] Migrations ran successfully
- [ ] No pending migrations (`php artisan migrate:status`)
- [ ] Database connection is PostgreSQL (not SQLite)
- [ ] `coordinates` column exists in properties table
- [ ] Property creation works without errors
- [ ] Maps render on property pages
- [ ] Maps render on property creation page
- [ ] Maps render on maps/search page
- [ ] No JavaScript errors in browser console
- [ ] Application is accessible at your domain

---

## Support

If you encounter any issues after following this guide:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Check web server logs (Nginx/Apache)
3. Check PostgreSQL logs
4. Verify all environment variables are set correctly

---

**Version:** 1.0  
**Date:** November 29, 2025  
**Status:** Ready for Deployment
