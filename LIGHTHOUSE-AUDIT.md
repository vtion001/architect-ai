# Lighthouse CI Performance Audit Guide

## Quick Start

### Prerequisites
- Node.js 20+
- Chrome/Chromium installed (for local audits)
- Application running on `http://localhost:8081`

### Local Audit Commands

```bash
# Install Lighthouse CI (if not already)
npm install -g lighthouse @lhci/cli

# Desktop audit
npx lighthouse http://localhost:8081/dashboard \
  --output=html \
  --output-path=./reports/lh-dashboard.html \
  --chrome-flags="--headless --no-sandbox" \
  --preset=desktop

# Mobile audit
npx lighthouse http://localhost:8081/dashboard \
  --output=html \
  --output-path=./reports/lh-mobile.html \
  --chrome-flags="--headless --no-sandbox" \
  --preset=mobile

# Full CI audit (all pages, 3 runs each)
npm run lh:ci
```

### Pages to Audit
- `/dashboard` - Main authenticated home
- `/documents` - Document builder
- `/research` - Research engine
- `/content-creator` - Content generation

## Performance Budgets

| Metric | Target | Error Threshold |
|--------|--------|-----------------|
| Performance Score | ≥ 0.9 | < 0.9 |
| LCP | < 2.5s | > 2.5s |
| CLS | < 0.1 | > 0.1 |
| FID | < 100ms | > 100ms |
| FCP | < 1.8s | > 1.8s |
| Speed Index | < 3.4s | > 3.4s |
| TBT | < 300ms | > 300ms |

## CI Integration

The Lighthouse CI workflow runs on:
- Push to main/master/develop branches
- Pull requests to main/master/develop

Reports are uploaded as artifacts and published to temporary public storage.

## Known Issues

### Windows Permissions Error
If you see `EPERM: Permission denied` errors on Windows with Lighthouse:
1. Use WSL/Linux for Lighthouse audits, OR
2. Run `npm run lh:ci` in CI (GitHub Actions)

### Bundle Size Warnings
The following chunks exceed 300KB and may need optimization:
- `vendor-icons` (346KB) - Lucide imports all icons
- `vendor-chart` (197KB) - Chart.js full bundle
- `app.css` (295KB) - Tailwind CSS

## Optimization Recommendations

### High Priority
1. **Lucide Icons**: Only import used icons instead of full library
2. **Chart.js**: Use tree-shaking or import specific chart types
3. **Tailwind CSS**: Audit unused classes in production

### Medium Priority
1. **Font Preloading**: Add `<link rel="preload">` for critical fonts
2. **Image Optimization**: Ensure all images have width/height attributes
3. **Lazy Components**: Defer non-critical Alpine components

### Low Priority
1. **Critical CSS Extraction**: Extract above-fold CSS
2. **Service Worker**: Add offline caching for repeat visits
3. **HTTP/2 Push**: Enable if using HTTP/2 server
