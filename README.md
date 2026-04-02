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

### Demo accounts (after `db:seed`)

> **Security — read before hosting**  
> These emails and passwords are **documented in public** (this README and often the same values in other Laravel demos). **Anyone on the internet can sign in as admin or super admin** if your deployed database still contains these users—same as leaving a published username/password list on your site.
>
> **Local / private learning:** fine to use as-is.  
> **Public or shared hosting:** either **do not** run `php artisan db:seed` (or strip user seeders from production), or **immediately** change every demo password, remove demo users, and create real accounts. Treat the seeded users as **compromised by definition** once the app is reachable outside your machine.

| Role        | Email               | Password   |
|------------|---------------------|------------|
| User       | `test@example.com`  | `password` |
| Admin      | `admin@example.com` | `password` |
| Super admin| `owner@reservo.com` | `password` |

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
- **Never deploy with seeded demo users unchanged** (see warning above). Use migrations only, or seed only non-auth data, or rotate/remove demo accounts before going public.
- Configure queue/mail only if you add notifications later.
