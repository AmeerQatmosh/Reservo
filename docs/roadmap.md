Development Roadmap
Project: Reservo

1. Overview
This roadmap outlines the step-by-step development plan for building the Reservo system. It is structured in phases to ensure incremental progress and working features at each stage.
The goal is to build a fully functional MVP in a logical and efficient order.

2. Development Strategy
Build one feature at a time
Always keep the app in a working state
Start with core foundations, then move to features
Test each feature before moving to the next

3. Phase 1: Project Setup
Objectives:
Prepare the development environment and basic Laravel setup.
Tasks:
Create Laravel project
Setup database connection (.env)
Run initial migrations
Install authentication (Laravel Breeze recommended)
Test register/login
Deliverable:
Working authentication system

4. Phase 2: Database Implementation
Objectives:
Translate the database design into actual migrations.
Tasks:
Create migrations:
users.role (user, admin, super_admin)
rooms table (including soft deletes: deleted_at)
reservations table
Add fields according to schema
Add foreign keys (reservations cascade on user/room hard delete)
Run migrations
Create models:
Room (SoftDeletes)
Reservation
User (relationships, role helpers)
Deliverable:
Database fully structured and connected

5. Phase 3: Room Management
Objectives:
Allow admin to manage rooms.
Tasks:
Create RoomController
Implement:
Create room
Edit room
Soft delete room (and restore)
List rooms (public: active only; admin: filters including deleted)
Add admin middleware (admin or super_admin)
Create Blade views for:
Room list
Create/edit form
Deliverable:
Admin can fully manage rooms including soft delete and restore

6. Phase 4: Public Room Listing
Objectives:
Allow users to browse rooms.
Tasks:
Create public route for rooms
Display:
Name
Capacity
Description
Add simple UI
Deliverable:
Users can view available rooms

7. Phase 5: Reservation System (Core Feature)
Objectives:
Implement booking functionality.
Tasks:
Create ReservationController
Create reservation form:
Select room
Date
Start time
End time
Save reservation to database
Deliverable:
Users can create reservations

8. Phase 6: Overlap Validation (Critical Phase 🔥)
Objectives:
Prevent double bookings.
Tasks:
Implement overlap check logic:
Query existing reservations
Compare time ranges
Return error if conflict exists
Test edge cases:
Exact match
Partial overlap
Boundary times
Deliverable:
No overlapping bookings possible

9. Phase 7: User Reservation Management
Objectives:
Allow users to manage their bookings.
Tasks:
Create "My Reservations" page
Display user's reservations
Add cancel/delete functionality
Ensure authorization:
Users can only access their own data
Deliverable:
Users can view and manage their reservations

10. Phase 8: Admin Reservation Management
Objectives:
Give admin full visibility.
Tasks:
Create admin view for all reservations
Add filters (optional)
Allow admin to delete reservations
Deliverable:
Admin can manage all reservations

11. Phase 9: Validation & Error Handling
Objectives:
Improve system reliability.
Tasks:
Add form validation:
Required fields
Time validation
Add user-friendly error messages
Handle edge cases
Deliverable:
Stable and user-friendly system

12. Phase 10: UI Improvements
Objectives:
Make the system usable and clean.
Tasks:
Improve layout (basic CSS)
Add navigation menu
Add success/error flash messages
Improve forms UX
Deliverable:
Clean and usable interface

13. Phase 11: Testing & Debugging
Objectives:
Ensure system correctness.
Tasks:
Test all flows:
Registration
Booking
Overlap prevention
Admin actions
Automated feature tests where practical (reservation rules, role access)
Fix bugs
Clean code
Deliverable:
Bug-free MVP

14. Phase 12 (Optional Extension): Roles & User Directory
Objectives:
Separate day-to-day admins from top-level operators.
Tasks:
Super admin role and seed accounts
User management screen: admins read-only, super admins change user ↔ admin
Navigation entry points for /admin/users
Deliverable:
Documented three-tier access (user, admin, super_admin)

15. Suggested Timeline
Phase
Duration
Setup
1–2 days
Database
1 day
Rooms
1 day
Reservations
2 days
Validation
1 day
UI & Testing
1–2 days


16. Definition of Done
The project is complete when:
Users can:
Register and log in
View rooms
Book rooms
Manage their reservations
Admin (or super admin) can:
Manage rooms (including soft delete/restore as designed)
View all reservations
Super admin can (when used):
Assign admin access via user management
System prevents overlapping bookings

17. Key Advice
Do NOT jump between features
Finish each phase completely
Test before moving forward
Focus especially on:
→ Overlap logic (most important part)

18. Summary
This roadmap ensures:
Structured development
Reduced confusion
Faster progress
A working product at every stage

