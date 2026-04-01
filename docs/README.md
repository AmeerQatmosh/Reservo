# Reservo documentation

This folder is the product and technical source of truth for Reservo. The **running application** is authoritative; these files describe what is implemented.

**Implementation summary (aligned with code):** Reservo uses **three roles** (`user`, `admin`, `super_admin`). Public registration creates **user** only. **Admins** manage rooms and all reservations, and may **view** the user directory read-only. **Super admins** may change roles between **user** and **admin** from user management; super admin accounts are not demoted from that screen. **Rooms** use **soft deletes** (`deleted_at`): deleted rooms disappear from public listings but **reservations remain** for history; admins can **restore** rooms. Foreign keys still **CASCADE** when a user or room row is **hard-deleted**. Reservation overlap prevention follows `(new.start < existing.end) AND (new.end > existing.start)` with transactions on create/update. Authentication is provided by the Laravel starter kit (Fortify); booking routes expect **verified** users when email verification is enabled. The **marketing landing** is `/`; **room catalog** is `/rooms`. Rooms may include an optional **`image_url`** for presentation. **Settings** (`/settings`) is a Blade hub linking to Inertia **Profile** and **Security** pages.

| Document | Purpose |
|----------|---------|
| [prd.md](prd.md) | Product requirements and MVP scope |
| [roadmap.md](roadmap.md) | Phased delivery plan and definition of done |
| [database-design.md](database-design.md) | Schema, relationships, indexes, constraints |
| [bussiness-rules.md](bussiness-rules.md) | Authorization and booking business rules |
| [routes-structure.md](routes-structure.md) | URLs, middleware, controller responsibilities |
| [edge-cases.md](edge-cases.md) | Validation, overlap, and integrity edge cases |

When you change behavior in code, update the relevant doc in the same change when possible so this set stays accurate.
