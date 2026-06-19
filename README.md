# Catalog

A boutique e-commerce starter template for the DevDojo platform — a small, opinionated shop
selling books, vinyl records, and board games. Built with Laravel 13, Livewire 4, Folio,
Alpine, and Tailwind CSS 4 on top of `devdojo/foundation` (auth enabled; other features
toggled off in `config/foundation.php`).

## What's included

- **Marketing home page** — editorial hero mosaic, category tiles, staff picks, new-arrivals rail
- **Shop** — `/shop` plus per-category pages with Livewire sorting, search, and sale filtering
- **Product pages** — generated SVG artwork, specs, low-stock indicators, related items
- **Cart slide-out** — session-based cart with free-shipping progress meter and quantity steppers
- **Checkout** — auth-gated, demo-mode payment (swap in Stripe/Paddle where marked), stock-aware
- **My Purchases** — order history with status timeline and order detail pages
- **Auth** — login, registration, 2FA, and social providers via `devdojo/auth`
- **39 seeded products**, each with a deterministic SVG "product photo" — staged scenes with standing books, leaning records, and upright game boxes (no stock photos needed)

## Setup

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan vendor:publish --tag=auth:migrations
php artisan migrate --seed
npm install && npm run build
```

Served by Laravel Herd at [https://catalog.test](https://catalog.test).

## Demo account

- **Email:** `demo@catalog.test` · **Password:** `password`
- Locally, visit `/_demo-login` to sign in instantly (local environment only).

## Where things live

| Piece | Location |
| --- | --- |
| Pages (Folio) | `resources/views/pages` |
| Livewire components | `resources/views/components/{cart,shop,products,checkout}` |
| Cart service | `app/Services/Cart.php` |
| Models | `app/Models/{Product,Order,OrderItem}.php` |
| Design tokens | `resources/css/app.css` |
| Demo catalog + SVG art generator | `database/seeders` |

## Tests

```bash
php artisan test --compact
```
