Routes & Controller Structure Plan
Project: Reservo

1. Overview
This document defines the routing structure and controller organization for the Reservo application. It ensures clean architecture, maintainability, and clear separation of responsibilities.

2. Route Structure
Routes are grouped based on access level:
Public routes
Authenticated user routes (verified email where configured)
Admin routes (admin or super_admin)
Super-admin-only routes (role changes)

3. Public Routes
Accessible without authentication.
Method
URI
Controller
Description
GET
/
RoomController@index
List all rooms
GET
/rooms
RoomController@index
List all rooms
GET
/rooms/{id}
RoomController@show
View room details


4. Authentication Routes
Handled by Laravel Fortify / starter kit (or similar):
/login
/register
/logout

5. Authenticated User Routes
Require auth (and verified email if enabled).
Method
URI
Controller
Description
GET
/dashboard
DashboardController@index
User dashboard
GET
/my-reservations
ReservationController@my
List user's reservations
POST
/reservations
ReservationController@store
Create reservation
GET
/reservations/{id}/edit
ReservationController@edit
Edit own reservation (form)
PUT
/reservations/{id}
ReservationController@update
Update own reservation
DELETE
/reservations/{id}
ReservationController@destroy
Cancel own reservation


6. Admin Routes
Require auth + admin middleware (user must be admin or super_admin).
Prefix: /admin
Method
URI
Controller
Description
GET
/admin/rooms
RoomController@adminIndex
List rooms (admin view; includes filters and soft-deleted)
GET
/admin/rooms/create
RoomController@create
Show create form
POST
/admin/rooms
RoomController@store
Store new room
GET
/admin/rooms/{id}/edit
RoomController@edit
Edit room
PUT
/admin/rooms/{id}
RoomController@update
Update room
DELETE
/admin/rooms/{id}
RoomController@destroy
Soft-delete room
PATCH
/admin/rooms/{id}/restore
RoomController@restore
Restore soft-deleted room
GET
/admin/reservations
ReservationController@index
View all reservations (filters)
GET
/admin/reservations/{id}/edit
ReservationController@adminEdit
Edit any reservation (form)
PUT
/admin/reservations/{id}
ReservationController@adminUpdate
Update any reservation
DELETE
/admin/reservations/{id}
ReservationController@destroy
Cancel reservation
GET
/admin/users
UserManagementController@index
User directory (read-only for admin; super_admin sees role controls)


7. Super-Admin-Only Routes
Require auth + super_admin middleware.
Prefix: /admin (same prefix, separate middleware group)
Method
URI
Controller
Description
PUT
/admin/users/{id}/role
UserManagementController@updateRole
Change role between user and admin (super_admin only)


8. Controller Structure
8.1 RoomController
Handles room-related logic.
Methods:
index(), show() → Public list and detail
adminIndex(), create(), store(), edit(), update(), destroy(), restore() → Admin

8.2 ReservationController
Handles reservation-related logic.
Methods:
my(), edit(), store(), update(), destroy() → Authenticated user (own reservations)
index(), adminEdit(), adminUpdate(), destroy() → Admin (any reservation)

8.3 UserManagementController
index() → User directory (super_admin can change roles; admin is read-only)
updateRole() → Super_admin only

8.4 DashboardController
Methods:
index() → Show dashboard

9. Middleware Structure
9.1 Auth (and Verified if enabled)
Applied to authenticated booking and dashboard routes
9.2 Admin Middleware
Ensures the user is admin or super_admin (for example isAdmin()).
Applied to: /admin rooms, reservations, and GET /admin/users
9.3 Super Admin Middleware
Ensures the user is super_admin only.
Applied to: PUT /admin/users/{id}/role

10. Route Grouping Example (Illustrative)
Public: GET /, /rooms, /rooms/{id}
Auth + verified: dashboard, my-reservations, user reservation create/edit/cancel
Auth + admin: /admin/rooms/*, /admin/reservations/*, GET /admin/users
Auth + super_admin: PUT /admin/users/{id}/role

11. Naming Conventions
Controllers: PascalCase (RoomController)
Methods: camelCase (storeReservation → better: store)
Routes: kebab-case (/my-reservations)

12. Key Design Decisions
Separate admin routes using prefix
Reuse controllers where possible
Keep controllers focused (no heavy logic inside → move to services later if needed)

13. Summary
This structure ensures:
Clean and scalable architecture
Easy navigation of code
Clear separation between user and admin functionality
Smooth development experience

