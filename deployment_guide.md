# Murugo Platform Deployment and Cache Management Guide

This document provides a comprehensive guide for deploying the Murugo platform and managing its cache, views, and configuration, specifically tailored for your local development, GitHub workflow, and VPS deployment using Docker Compose.

---

## 1. Understanding the Deployment Workflow

Your workflow involves three stages:
1.  **Local Development:** Making changes on your machine.
2.  **GitHub:** Version control and collaboration.
3.  **VPS Deployment:** Running the application in a production environment using Docker Compose.

The key to a smooth deployment is understanding when and how to rebuild assets and clear caches.

### The Critical Step: Frontend Asset Compilation

Frontend assets (CSS, JavaScript) are compiled from source files (e.g., `resources/css/app.css`) into optimized files (e.g., `public/build/assets/app.css`).

-   **When to Rebuild:** **ALWAYS** after changing any file in the `resources/` directory (CSS, JS, Blade components that affect styling/scripts).
-   **Why:** Your VPS container is built with a specific set of compiled assets. If you change the source CSS/JS but don't rebuild the assets, the container will still serve the old, broken files.

---

## 2. Local Development Commands

Run these commands on your local machine after making changes to CSS, JS, or Blade files.

| Command | Purpose | When to Use |
| :--- | :--- | :--- |
| `npm run dev` | Starts the development server with hot-reloading. | During active development of frontend code. |
| `npm run build` | Compiles and minifies all production assets. | **Before committing to GitHub.** |
| `php artisan cache:clear` | Clears the application cache. | After changing configuration or environment variables. |
| `php artisan view:clear` | Clears compiled Blade views. | After changing Blade files (especially if changes don't appear). |
| `php artisan config:clear` | Clears the configuration cache. | After changing files in the `config/` directory. |

---

## 3. VPS Deployment Workflow (Docker Compose)

The most reliable way to deploy changes is to **rebuild the Docker image** when frontend assets are changed.

### Scenario A: Backend-Only Changes (PHP, Controller, Model, Route)

If you only changed PHP files (e.g., a Controller, Model, or Route), you only need to restart the application container.

```bash
# 1. Pull the latest code
cd /root/murugo-app
git pull origin main

# 2. Clear caches and restart the application container
docker compose exec murugo php artisan cache:clear
docker compose exec murugo php artisan view:clear
docker compose exec murugo php artisan config:clear
docker compose restart murugo
```

### Scenario B: Frontend Changes (CSS, JS, Blade, Styling Issues)

If you changed any files in `resources/` (CSS, JS, Blade) or are experiencing **styling issues**, you must rebuild the image to include the new compiled assets.

```bash
# 1. Pull the latest code
cd /root/murugo-app
git pull origin main

# 2. Shut down, rebuild, and restart the containers
# --no-cache ensures a fresh build, including asset compilation
docker compose down
docker compose build --no-cache
docker compose up -d

# 3. Clear caches (best practice after a rebuild)
docker compose exec murugo php artisan cache:clear
docker compose exec murugo php artisan view:clear
docker compose exec murugo php artisan config:clear

echo "✅ Deployment Complete. Check your site and clear your browser cache."
```

### Scenario C: Database Changes (Migrations)

If you added or modified database migrations.

```bash
# 1. Pull the latest code
cd /root/murugo-app
git pull origin main

# 2. Run migrations inside the application container
docker compose exec murugo php artisan migrate --force

# 3. Clear caches
docker compose exec murugo php artisan cache:clear
docker compose exec murugo php artisan view:clear
```

---

## 4. Cache Management Reference

| Command | What it Clears | When to Use |
| :--- | :--- | :--- |
| `php artisan cache:clear` | General application cache (Redis, File, etc.) | Most common cache clear. Use after any functional change. |
| `php artisan view:clear` | Compiled Blade templates | **Crucial for seeing Blade file changes.** |
| `php artisan config:clear` | Cached configuration files | After changing files in `config/` or `.env`. |
| `php artisan route:clear` | Cached route definitions | After changing files in `routes/`. |
| `php artisan optimize:clear` | Clears all of the above caches. | The nuclear option; safest after a major deployment. |

**Best Practice:** After any deployment, always run:
```bash
php artisan optimize:clear
```
and then **clear your browser cache** (Ctrl+Shift+R or Cmd+Shift+R).
