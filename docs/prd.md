Product Requirements Document (PRD)
Project: Reservo

1. Overview
Reservo is a lightweight reservation management system that allows users to book rooms for specific dates and time slots. The system focuses on simplicity, usability, and core booking functionality, avoiding unnecessary complexity.
It is designed as a practical project to demonstrate real-world backend development using Laravel, including database design, authentication, and business logic.

2. Objectives
Provide a simple and intuitive room booking system
Prevent double bookings through strict validation logic
Allow users to manage their own reservations
Enable admins to manage rooms and oversee all reservations
Showcase backend development skills using Laravel and MySQL

3. Target Users
3.1 Regular Users
Individuals who want to book rooms
Can register, log in, and manage their reservations
Public registration always creates a normal user account (role: user).
3.2 Admin Users
System managers
Responsible for managing rooms and monitoring all reservations
Can view the user directory in read-only mode (no role changes)
3.3 Super Admin Users
Highest-privilege operators (created via seeding or direct database setup, not public registration)
Can change user roles between user and admin from the user management screen
Super admin accounts are not demoted from that screen (managed outside the app)

4. Features & Requirements
4.1 Authentication
User registration
User login/logout
Secure password handling
Role-based access: user, admin, super_admin
4.2 Room Management
View list of available rooms (non-deleted rooms only for public browsing)
Room details:
Name
Capacity
Description
Admin capabilities:
Add rooms
Edit rooms
Soft-delete rooms (rooms are hidden from public listing; reservation history is preserved)
Restore soft-deleted rooms from the admin room list
4.3 Reservation System
Create reservations by selecting:
Room
Date
Start time
End time
View personal reservations
Edit or cancel reservations
4.4 Booking Validation
Prevent overlapping reservations for the same room
Validate:
Time ranges
Date correctness
Ensure data integrity at the database level where possible
4.5 Admin Controls
View all reservations
Manage rooms (create, update, soft delete, restore)
Cancel any reservation if needed
Admins and super admins: full room and reservation management as above
Super admins only: change user roles (user ↔ admin) from the user management screen
Admins: view users list only (no role changes)

5. User Flows
5.1 User Booking Flow
User registers/logs in
User browses available rooms
User selects a room
User chooses date and time range
System validates availability
Reservation is created
5.2 Admin Flow
Admin or super admin logs in
Manages rooms (add/edit/soft delete/restore)
Views all reservations
Super admin additionally opens user management to assign admin access to trusted accounts

6. Non-Functional Requirements
Performance: Fast response for booking operations
Scalability: Structured for future feature expansion
Security: Secure authentication and validation
Usability: Simple and clean UI using Blade templates
Reliability: No double bookings under any condition

7. Tech Stack
Backend: PHP (Laravel)
Database: MySQL
Frontend: Blade templates (HTML, CSS, JavaScript)


8. Success Metrics
Users can successfully create and manage reservations
No overlapping bookings occur
Admin can manage rooms without errors
Application runs smoothly with minimal bugs

MVP (Minimum Viable Product)
1. MVP Goal
Deliver a functional reservation system with core booking and management features, focusing on correctness and simplicity.

2. MVP Features
Must-Have
User authentication (register/login/logout)
Room listing with basic details
Create reservation
View user reservations
Prevent overlapping bookings
Admin:
Add/edit/delete rooms
View all reservations

Nice-to-Have (if time permits)
Edit reservations
Cancel reservations
Basic UI improvements
Flash messages (success/error feedback)

3. MVP User Stories
User
I can register and log in
I can see available rooms
I can book a room for a specific time
I can view my reservations
Admin
I can add and manage rooms (including soft delete and restore)
I can see all reservations
I can open the user directory to view accounts (read-only)
Super Admin (operational)
I can promote trusted accounts to admin via user management

4. MVP Constraints
No advanced UI frameworks
No payment integration
No notifications (email/SMS)
Single timezone support

5. MVP Timeline (Suggested)
Day 1–2: Setup Laravel + authentication
Day 3: Room management
Day 4–5: Reservation system
Day 6: Validation (no overlaps)
Day 7: Testing and cleanup

6. MVP Definition of Done
Users can register, log in, and book rooms
Overlapping bookings are prevented
Admin can manage rooms
Core flows work without errors

7. Future Enhancements
Calendar view for reservations
Notifications (email reminders)
Finer-grained permissions (beyond user / admin / super_admin)
API support
Advanced filtering/search

Summary:
Reservo MVP delivers a clean, functional booking system with strong backend logic and a simple interface, making it an excellent foundation for future expansion or portfolio demonstration.

