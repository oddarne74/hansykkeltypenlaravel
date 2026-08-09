# Han Sykkeltypen — Laravel 13

Komplett, database-drevet nettside med norsk innhold, responsivt Tailwind-design, sykkeloversikt, én detaljside per sykkel, før/etter-galleri, kontaktskjema, e-postkø, demo-data og tester.

## Krav

- PHP 8.3 eller nyere
- Composer
- Node.js 22+ og npm
- SQLite (standard), MySQL eller PostgreSQL

## Start lokalt

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm run build
composer run dev
```

Åpne `http://localhost:8000`.

## Legg inn egne bilder

Legg bilder under `storage/app/public/images/bikes/{slug}/` og oppdater radene i `bike_images`. Kjør `php artisan storage:link` én gang. Demo-seederen viser den anbefalte datastrukturen for hovedbilder og før/etter-bilder.

## Kontaktskjema og e-post

Alle henvendelser lagres i `contact_requests`. Sett `CONTACT_EMAIL` og vanlig Laravel `MAIL_*`-konfigurasjon i `.env`. Lokalt er `MAIL_MAILER=log`, så e-post skrives til `storage/logs/laravel.log`. I produksjon kan du bruke SMTP, Resend, Postmark eller en annen Laravel mail-driver. Skjemaet har validering, CSRF, honeypot og enkel ratebegrensning.

## Produksjon

Kjør `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`, `php artisan migrate --force`, `php artisan storage:link` og `php artisan optimize`. Webroten må peke på `public/`. Kjør en køarbeider for utsending av kontakt-e-post.

Cloudflare Pages/Workers kjører ikke et vanlig PHP/Laravel-program direkte. Bruk en PHP-vert (for eksempel Laravel Cloud, Forge-kompatibel VPS eller delt hosting) og legg eventuelt Cloudflare foran domenet for DNS, cache og beskyttelse.
