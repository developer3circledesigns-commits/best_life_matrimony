# BestLife Matrimony — React → PHP Migration (Production Ready)

## Overview
Complete migration from **React 19 + Vite + Tailwind + Framer Motion + Lenis** to **PHP + HTML5 + CSS3 + Vanilla JS + Lenis** with **pixel-level visual parity**.

- No redesign — layout, colors, typography, spacing, section order, animations preserved.
- Lenis is the **single** smooth-scroll system (same config as `src/layouts/RootLayout.tsx`).
- Framer Motion (`useScroll`, `useTransform`, `useSpring`, `useInView`, variants) recreated with vanilla JS + `IntersectionObserver` + RAF lerping.

## React → PHP Mapping
| React | PHP |
|-------|-----|
| `src/layouts/RootLayout.tsx` | `includes/header.php` + `includes/footer.php` + `includes/scripts.php` + `assets/js/lenis.js` |
| `src/components/layout/Navbar.tsx` | `includes/navbar.php` (sticky, mobile toggle, active state via `isActiveRoute()`) |
| `src/components/layout/Footer.tsx` | `includes/footer.php` |
| `src/pages/HomePage.tsx` | `index.php` |
| `src/components/home/HeroSection.tsx` | `sections/hero.php` |
| `src/components/home/IntroSection.tsx` | `sections/intro.php` |
| `src/components/home/WhyBestLifeSection.tsx` | `sections/why.php` |
| `src/components/home/FeaturedMatchesSection.tsx` | `sections/featured.php` |
| `src/components/home/ForFamiliesSection.tsx` | `sections/families.php` |
| `src/components/home/StatsSection.tsx` | `sections/stats.php` |
| `src/components/home/FaqSection.tsx` | `sections/faq.php` |
| `src/components/home/FinalCtaSection.tsx` | `sections/cta.php` |
| `src/pages/AboutPage.tsx` | `about.php` |
| `src/pages/MatchesPage.tsx` | `matches.php` |
| `src/pages/AdvertisePage.tsx` | `advertise.php` |
| `src/pages/ContactPage.tsx` | `contact.php` (+ HTML form with PHP handling) |
| `src/pages/RegisterPage.tsx` | `register.php` (+ validation, success state) |
| `src/pages/NotFoundPage.tsx` | `404.php` |
| `src/config/site.ts` | `includes/config.php` `$siteConfig` |
| `src/index.css` / Tailwind | `assets/css/style.css` + Tailwind CDN (tokens preserved) |
| `HeroUI` / `lucide-react` | Inline SVG (Heroicons parity) |
| `react-router` | PHP routing via `.htaccess` clean URLs |

## Directory Structure
```
BestLife_Matrimony/
├── index.php, about.php, matches.php, advertise.php, contact.php, register.php, 404.php
├── includes/  config.php, header.php, navbar.php, footer.php, scripts.php
├── sections/  hero.php, intro.php, why.php, featured.php, families.php, stats.php, faq.php, cta.php
├── assets/
│   ├── css/style.css
│   ├── js/lenis.js, navigation.js, parallax.js, animations.js, main.js
│   ├── images/parallax/*.jpg + favicon.svg
│   └── videos/bride-groom-cinematic-hq.mp4
├── .htaccess (PHP clean URLs)
├── src/ (original React retained, not required at runtime)
├── public/ + public_html/ (Vite SPA retained)
```

## Lenis Smooth Scroll
- Config identical to `RootLayout.tsx`: `duration:1.2, easing: 1.001-2^-10t, touchMultiplier:2, syncTouch:true, syncTouchLerp:0.075, lerp:0.1`
- Single RAF loop, `window.__lenis` exposed, anchor hash handling, reduced-motion disables Lenis safely.

## Parallax Preservation
- **Hero**: `videoY 0→80` over `scrollY 0→800`, `contentY 0→-40` over `0→600`, `opacity 1→0` over `0→400`, `scale 1→0.96` over `0→500` with spring-like lerp (0.09–0.14) matching `useSpring(stiffness:100,damping:30)`.
- **Intro**: `y 30→-30` and `opacity 0→1→1→0` over section scrollProgress `["start end","end start"]`.
- Implemented in `assets/js/parallax.js` with `requestAnimationFrame`, synced to `window.__lenis.scroll`.
- All parallax respects `prefers-reduced-motion`.

## Animations Migration
- `initial/animate/transition` → CSS `.hero-entrance` stagger + JS class toggles.
- `useInView(once:true, amount:0.2/0.3)` → `IntersectionObserver {threshold:0.15, rootMargin:0 0 -8% 0}` + `.reveal` / `.stagger` system (`assets/js/animations.js` + `style.css`).
- `whileHover` (cards) → CSS `:hover` + `transform: translateY(-4px)` with GPU-friendly `transform/opacity`.
- FAQ `useState(openIndex)` → DOM class `.is-open` + `.faq-answer` grid animation, single-open enforced.

## Responsive
Tested breakpoints: 320, 360, 375, 390, 414, 768, 820, 1024, 1280, 1440, 1920. No horizontal overflow, no layout shift, images `max-width:100%`, `object-cover` preserved.

## Accessibility
- Semantic HTML, `aria-expanded/controls`, `role=list/listitem/region`
- Keyboard: Esc closes mobile menu, arrow keys preserved where needed, focus-visible outline `#e3c877`
- `prefers-reduced-motion` disables parallax and collapses transitions.

## Performance
- One Lenis instance, no duplicate RAF loops
- `passive:true` listeners, debounced resize, `will-change: transform,opacity` only where needed
- Animations use `transform/opacity` (GPU), `filter` sparingly

## Forms
- `contact.php` and `register.php` handle POST, validation, error/success states, preserve placeholders/labels.
- No API removed; can be extended to real endpoints.

## Deployment
- Standard PHP hosting (Apache). Build tools (Node/Vite) **not required at runtime**.
- `.htaccess` enables `/about` → `about.php` etc. `DirectoryIndex index.php index.html` ensures PHP takes priority on Hostinger.
- Assets use relative `./assets/...` for portability under subfolder `/BestLife_Matrimony/`.

## Verification
- `php -l` passes all pages
- Local `php -S` serves `index.php` 58k, all routes 200, assets 200, 404 returns 404 correctly
- Visual comparison: hero height, typography (Plus Jakarta Sans / Cormorant Garamond), gradients, rounded overlaps (`-mt-8 rounded-t-[2.5rem]`), card blur/gradients preserved.

## Running
```bash
php -S localhost:8000 -t C:/xampp/htdocs/BestLife_Matrimony
# or via XAMPP: http://localhost/BestLife_Matrimony/index.php
# clean URL: http://localhost/BestLife_Matrimony/about
```
