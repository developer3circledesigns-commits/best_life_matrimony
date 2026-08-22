# Hostinger Shared Hosting - Deployment Guide

This project is **100% optimized for Hostinger Shared Hosting** (Apache `public_html`).

## Quick Deploy (2 minutes, no Node on server needed)

1. Build locally:
```bash
npm run build:hostinger   # same as npm run build, outputs to public_html + generates 404.html
npm run hostinger:zip     # optional: creates bestlife_hostinger.zip (~188MB)
```

2. Upload **contents** of `public_html/` (not the folder itself) to Hostinger:
   - hPanel → File Manager → `public_html` → Upload Files
   - Or FTP (FileZilla): `ftp://yourdomain.com` → `public_html/`

   Required files at `public_html/` root:
   ```
   public_html/
   ├── .htaccess         # 2853 bytes - SPA rewrite, caching, security headers
   ├── 404.html          # copy of index.html (vite.config.ts:19 copy404Plugin)
   ├── index.html
   ├── favicon.svg
   ├── robots.txt        # public/robots.txt:1
   ├── sitemap.xml       # public/sitemap.xml:1
   ├── assets/           # hashed js/css/mp4
   ├── images/parallax/
   └── layout-samples/
   ```
   Enable "Show hidden files" to verify `.htaccess` uploaded.

3. Visit `https://yourdomain.com` — all routes (`/about`, `/matches`, `/register`) work via `.htaccess` SPA fallback.

## What was configured for Hostinger

- `vite.config.ts:29` `outDir: 'public_html'`, `emptyOutDir: true` + `copy404Plugin` → `public_html/404.html`
- `public/.htaccess:1` → hardened for Hostinger:
  - SPA rewrite (`RewriteCond %{REQUEST_FILENAME} !-f/d → index.html`)
  - `ErrorDocument 404 /index.html`
  - Security headers (`X-Frame-Options`, `X-Content-Type-Options`)
  - Compression (`mod_deflate`) + caching (`mod_expires` + `immutable` for hashed assets)
  - HTTPS redirect ready (uncomment after SSL)
  - MIME types for `.mp4`, `.svg`, `.woff2`
- `public/robots.txt` + `public/sitemap.xml` → auto-copied to `public_html/`
- `package.json:7` scripts: `build:hostinger`, `hostinger:zip`, `hostinger:clean`
- `.gitignore:14` ignores `public_html` + `bestlife_hostinger.zip` (source repo stays lean)
- `src/router/index.tsx:22` uses `createBrowserRouter` with `*` → `/404` → handled by `.htaccess`

## Git-Based Deploy (alternative)

Two branches exist on GitHub:

- `main` — source (needs `npm run build:hostinger` locally)
- `hostinger` — **built static only at root** (ideal for hPanel → Git → Deploy from `hostinger` branch). This branch's root **is** `public_html` content — Hostinger can pull it directly to `public_html` without build step.

To use Hostinger's Git pull:
1. hPanel → Advanced → Git → Create Repository
2. Clone URL: `https://github.com/developer3circledesigns-commits/best_life.git`
3. Branch: `hostinger` (not `main`)
4. Install path: `public_html` (or `domains/yourdomain.com/public_html`)
5. Pull/Deploy

Update `hostinger` branch after each change:
```bash
npm run build:hostinger
# hostinger branch is rebuilt locally via same logic as CI; or push to main and let CI rebuild
git checkout hostinger
# ... copy public_html to root logic handled by CI workflow
```

### Auto-Deploy via GitHub Actions (FTP)

Workflow at `.github/workflows/deploy-hostinger.yml:1` (requires `workflow` PAT scope — add via GitHub UI if push rejected):
- On push to `main`: `npm ci` → `npm run build:hostinger` → FTP upload `public_html/` → force-push to `hostinger` branch.

Add secrets in GitHub: Settings → Secrets and variables → Actions:
```
FTP_SERVER=185.xxx.xxx.xxx or ftp.yourdomain.com
FTP_USERNAME=u12345678
FTP_PASSWORD=***
FTP_SERVER_DIR=/public_html/   # or /domains/yourdomain.com/public_html/
```
Leave secrets empty to skip FTP and only update `hostinger` branch.

## Subfolder Deploy (e.g. /bestlife)

`vite.config.ts:8`:
```ts
export default defineConfig({ base: "/bestlife/", plugins: [...] })
```
`public/.htaccess:12`:
```apache
RewriteBase /bestlife/
RewriteRule ^ /bestlife/index.html [L]
ErrorDocument 404 /bestlife/index.html
```
Rebuild and upload to `public_html/bestlife/`.

## Troubleshooting

- Blank page: verify `.htaccess` present, `index.html` loads, browser console for `404` on `assets/*` (wrong `base` if subfolder).
- 404 on refresh: `.htaccess` missing or `mod_rewrite` disabled (Hostinger enables by default).
- Large upload: video `assets/bride-groom-cinematic-hq-Ce50U6Dc.mp4` is 61MB — Hostinger FTP may be slow; chunked upload or use File Manager zip extract is faster.
- HTTPS: after SSL active, uncomment HTTPS redirect block in `public/.htaccess:7`.
- Cache: hashed assets use `immutable` — clearing Hostinger cache not needed; `index.html` is `no-cache`.

## Verification Checklist

- [ ] `public_html/.htaccess` (2853B) + `404.html` exist
- [ ] `public_html/robots.txt` + `sitemap.xml` accessible
- [ ] `https://yourdomain.com/robots.txt` returns 200
- [ ] Direct navigation to `/matches`, `/advertise`, `/contact` works (no 404)
- [ ] Browser devtools Network → `index-*.js` cached with `immutable`

See also `README.md:53` for summary.
