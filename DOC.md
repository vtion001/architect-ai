# 🐳 architect-ai — Docker Dev Setup Guide

> **Use this document after your demo.** It contains the full implementation plan
> to run the app inside Docker with Vite HMR working.
>
> Estimated time to apply: **15–20 minutes**

---

## Overview

The goal is `docker compose -f docker-compose.dev.yml up` → full app running with:
- **Laravel** (PHP) via Nginx + PHP-FPM
- **Vite** dev server (HMR) running inside the container
- **SQLite** database (auto-created, seeded)
- All on one command, zero manual setup

### Architecture

```
Browser
  │
  │  http://localhost:8081  (Nginx :80 in container)
  │
  ├────────────────────────────┐
  │                            │
  ▼                            ▼
/@vite/*, /resources/*      /api/*, /auth/*, *.php
  │                            │
  │  proxy                     │  proxy
  ▼                            ▼
Vite :5173               Nginx :80
(in container)              │
                            ▼
                      PHP-FPM :9000
                      (Laravel)
```

---

## Files Changed

| File | Change |
|------|--------|
| `Dockerfile` | +Node.js 20, npm install (node_modules baked into image) |
| `docker/supervisord.conf` | +Vite process managed by Supervisord |
| `docker/nginx/conf.d/app.conf` | Proxy `/@vite/*` and assets → Vite on :5173 |
| `vite.config.js` | `server.host: '0.0.0.0'`, HMR port, `VITE_PROXY_TARGET` env |
| `docker-compose.dev.yml` | **NEW** — SQLite dev stack |
| `.env.docker` | **NEW** — SQLite config + dev env vars |

---

## Step 1 — Verify Docker is Running

```powershell
docker --version
docker compose version
docker info 2>&1 | Select-String "Server Version"
```

If Docker Desktop is not running: **start it first**, then continue.

---

## Step 2 — Check That Nothing Else is Using the Ports

```powershell
netstat -ano | Select-String -Pattern ":8081|:5175"
```

If anything is listening on 8081 or 5175, stop it before proceeding:
```powershell
# Stop local PHP server
Get-Process -Id <PID> | Stop-Process -Force

# Stop local Vite
Get-Process -Id <PID> | Stop-Process -Force
```

---

## Step 3 — Build and Start the Container

```powershell
cd C:\Users\VJ_Rodriguguez\Desktop\REPOSITORY\architect-ai

# Build the image (installs Node.js + npm + node_modules inside)
docker compose -f docker-compose.dev.yml build --no-cache

# Start everything (PHP-FPM + Nginx + Vite auto-started by Supervisord)
docker compose -f docker-compose.dev.yml up -d
```

### First Run Only — Create Database Tables + Seed Dev User

```powershell
# Run migrations (creates all tables in SQLite)
docker compose -f docker-compose.dev.yml exec app php artisan migrate --force

# Seed the dev user
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class=DevUserSeeder --force
```

> **Note:** On subsequent runs, the SQLite file persists in the `sqlite-data` Docker volume.
> You only need to migrate/seed once (or after `docker compose down -v`).

---

## Step 4 — Verify It Works

Open in browser:

| URL | What | Expected |
|-----|------|----------|
| `http://localhost:8081` | App (Nginx) | ArchitGrid login page loads |
| `http://localhost:8081/up` | Health check | `{"status":"ok"}` |
| `http://localhost:8081/auth/login` | Login page | Form with email + password fields |

**Login:** `admin@dev.local` / `password123`

---

## Step 5 — Run the E2E Tests Inside Docker

```powershell
# The app container has the full test suite + dependencies
docker compose -f docker-compose.dev.yml exec app npx playwright install chromium --with-deps
docker compose -f docker-compose.dev.yml exec app npx playwright test tests/e2e/auth-login.spec.ts --reporter=list
docker compose -f docker-compose.dev.yml exec app npx playwright test tests/e2e/blog-batch.spec.ts --reporter=list
```

---

## Common Operations

### View logs

```powershell
# All services
docker compose -f docker-compose.dev.yml logs -f

# One service (vite, nginx, php-fpm)
docker compose -f docker-compose.dev.yml logs -f app
```

### Restart a service

```powershell
docker compose -f docker-compose.dev.yml restart app
```

### Open a shell inside the container

```powershell
docker compose -f docker-compose.dev.yml exec app bash
```

### Check Vite is running inside container

```powershell
docker compose -f docker-compose.dev.yml exec app curl -s http://localhost:5175 | head -5
# Should return: <!DOCTYPE html>...
```

### Stop everything

```powershell
docker compose -f docker-compose.dev.yml down
```

### Full reset (deletes SQLite volume — removes ALL data)

```powershell
docker compose -f docker-compose.dev.yml down -v
# Then re-run: docker compose -f docker-compose.dev.yml up -d
# And re-migrate + seed (Step 3)
```

---

## Troubleshooting

### "502 Bad Gateway" on JS/CSS assets

Vite hasn't finished starting. Wait 5–10 seconds after `docker compose up`, then refresh.

Check if Vite is running:
```powershell
docker compose -f docker-compose.dev.yml logs app 2>&1 | Select-String "VITE"
```

If Vite failed to start, check the logs:
```powershell
docker compose -f docker-compose.dev.yml logs app
```

### "Database file does not exist"

The SQLite file wasn't created. Fix:
```powershell
docker compose -f docker-compose.dev.yml exec app php artisan migrate --force
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class=DevUserSeeder --force
```

### `npm install` fails in Docker build

Usually means the base image couldn't reach npm registry. Check:
```powershell
docker compose -f docker-compose.dev.yml build 2>&1 | Select-String "npm"
```

### Port already in use

```powershell
# Find what's using the port
netstat -ano | Select-String -Pattern ":8081|:5175"
# Kill it
Stop-Process -Id <PID> -Force
```

### Changes to .blade.php/.js/.css not showing

Source code is mounted from your host (`./:/var/www/html`), so changes should be picked up automatically by Vite's HMR.

If not, force-reload (Ctrl+Shift+R) or restart Vite:
```powershell
docker compose -f docker-compose.dev.yml restart app
```

### Supervisord not starting Vite

Check supervisord.conf is correctly mounted:
```powershell
docker compose -f docker-compose.dev.yml exec app cat /etc/supervisord.conf | Select-String "vite"
```

---

## Rollback (Instant Revert)

If something goes wrong and you want to go back to local dev:

```powershell
git checkout -- Dockerfile docker/supervisord.conf docker/nginx/conf.d/app.conf vite.config.js docker-compose.dev.yml .env.docker
```

Then restart your local servers:
```powershell
# Terminal 1
php artisan serve --port=8081

# Terminal 2
npm run dev
```

---

## File Reference

### `Dockerfile`
- Base: `serversideup/php:8.3-fpm-nginx` (PHP 8.3 + Nginx)
- Installs Node.js 20 LTS via NodeSource
- Copies `package*.json` and runs `npm install` (bakes node_modules into image)
- node_modules is NOT volume-mounted (native Rollup binaries match container OS)

### `docker/supervisord.conf`
- Manages 3 processes: `php-fpm`, `nginx`, `vite`
- Vite starts on `0.0.0.0:5173` so Nginx can proxy to it
- Vite `autorestart=true` — if it crashes, Supervisord restarts it

### `docker/nginx/conf.d/app.conf`
- Proxies `/@vite/*`, `.hot-update.*` → `http://127.0.0.1:5173` (Vite)
- Proxies WebSocket upgrade headers for HMR
- All other routes go to PHP-FPM via FastCGI

### `vite.config.js`
- `server.host: '0.0.0.0'` — binds to all interfaces (required for Nginx proxy)
- `VITE_PROXY_TARGET` env var: `http://localhost:80` (Nginx in Docker) or `http://localhost:8081` (local)
- All Laravel routes (`/api`, `/auth`, `/dashboard`, etc.) proxy to the backend

### `docker-compose.dev.yml`
- Single `app` service: runs Supervisord (PHP-FPM + Nginx + Vite)
- Named volume `sqlite-data`: persists SQLite file across restarts
- Port 8081 → Nginx :80, Port 5175 → Vite HMR

### `.env.docker`
- `DB_CONNECTION=sqlite` — no MySQL needed
- `DB_DATABASE=/var/www/html/database/database.sqlite` — inside the volume
- `APP_KEY` pre-set (same as local .env)
