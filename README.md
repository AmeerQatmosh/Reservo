# Reservo

Lightweight **room reservation** system built with **Laravel**. Users browse spaces, book time slots with **overlap protection**; admins manage rooms and reservations; super admins can assign admin access.

## Quick start (local)

```bash
cp .env.example .env
php artisan key:generate
# Configure DB_* in .env (MySQL recommended for production)

composer install
npm install
npm run build

php artisan migrate
php artisan db:seed
```

Visit the app URL. The **landing page** is `/`; **rooms** are at `/rooms`.

### Seeded users (local development)

After `db:seed`, the database may contain users for each role. **Do not publish real passwords** in this README or in a public repository.

- Set your own passwords with `php artisan tinker`, the database, or by customizing `Database/Seeders` before deploying anywhere reachable from the internet.
- For **public demos**, prefer **`RESERVO_DEMO_ENABLED=true`** (see below) so visitors can try **`/demo`** without signing in—data stays in **session only**, not the database.

### Guest demo (“Free room”, portfolio / try-it)

- **Local:** demo is **on by default** when `APP_ENV=local` and `RESERVO_DEMO_ENABLED` is **not** set. You get **Free room** in the nav and **Free room (demo)** on the home hero (guests only see the hero button; everyone sees **Free room** in the nav).
- **Production:** set `RESERVO_DEMO_ENABLED=true` on the host where you want the public sandbox; otherwise set `RESERVO_DEMO_ENABLED=false` (or rely on `APP_ENV=production` with the variable unset).

Open **`/demo`**: choose **User**, **Admin**, or **Super admin**—session-only rooms and bookings (same overlap rules as production).

**Leave `RESERVO_DEMO_ENABLED=false`** on private production installs where you only want real accounts.

### Presentation seed data

`RoomSeeder` fills **six demo rooms** with realistic descriptions and **Unsplash** image URLs for portfolio demos. See [Unsplash License](https://unsplash.com/license) for reuse terms.

## Documentation

Product and technical specs live in [`docs/`](docs/README.md).

## Tests

```bash
php artisan test
```

## Stack

- Laravel, Fortify (auth), Blade (booking UI), Inertia (profile/security settings)
- Tailwind CSS (Vite)

## Deployment notes

- Run `npm run build` in CI or on the server before going live.
- Set `APP_ENV=production`, `APP_DEBUG=false`, and a strong `APP_KEY`.
- **Never deploy** with default seeded passwords unchanged on a public URL. Rotate or remove seeded users, or rely on **guest demo mode** instead of sharing login credentials.
- Enable **`RESERVO_DEMO_ENABLED=true`** only on hosts where you intend to offer the public sandbox; keep it **false** for internal production.
- Configure queue/mail only if you add notifications later.
