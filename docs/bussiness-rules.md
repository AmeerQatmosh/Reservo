Business Logic Rules Document
Project: Reservo

1. Overview
This document defines the core business rules governing the Reservo system. These rules ensure correct behavior, data integrity, and prevent invalid operations such as double bookings.
All application logic must follow these rules.

2. User Rules
2.1 Registration & Authentication
A user must register with a unique email
Password must be securely hashed
Users must be authenticated to access booking features

2.2 Authorization
Regular users can:
View rooms (only rooms that are not soft-deleted)
Create reservations
View their own reservations
Edit and cancel their own reservations
Regular users cannot:
Access admin features
Modify other users' reservations
Admin users (role: admin) can:
Manage rooms (create, update, soft delete, restore)
View all reservations
Cancel any reservation
View the user directory (read-only; cannot change roles)
Admin users cannot:
Change user roles or access super-admin-only actions
Super admin users (role: super_admin) can:
Everything an admin can do
Change another user's role between user and admin from the user management screen
Super admin accounts are not modified from that screen (managed outside the app)

3. Room Rules
Rooms must have:
Name
Capacity
Room names do not have to be unique (optional decision)
Soft delete: An admin may soft-delete a room even when reservations exist
Soft-deleted rooms are hidden from public room listing but reservations remain for history
Restore: Admins can restore a soft-deleted room from the admin room list
Hard delete: If a room row is permanently removed from the database, related reservations are removed by foreign key CASCADE (same as deleting a user)

4. Reservation Rules
4.1 Required Fields
A reservation must include:
user_id
room_id
date
start_time
end_time

4.2 Time Validation Rules
start_time must be before end_time
Reservations must be within the same day (no overnight bookings)
Time format must be valid

4.3 Overlapping Rule (Critical Core Logic)
A reservation is NOT allowed if it overlaps with another reservation for the same room on the same date.

4.4 Overlap Condition
A new reservation conflicts if:
new.start_time < existing.end_time
AND
new.end_time > existing.start_time

4.5 Valid Examples
Existing Booking
New Booking
Result
10:00–11:00
11:00–12:00
✅ Allowed
10:00–11:00
09:00–10:00
✅ Allowed


4.6 Invalid Examples
Existing Booking
New Booking
Result
10:00–11:00
10:30–11:30
❌ Overlap
10:00–11:00
09:30–10:30
❌ Overlap
10:00–11:00
10:00–11:00
❌ Exact match


5. Reservation Ownership Rules
A user can only:
View their own reservations
Edit and delete their own reservations
A user cannot:
Modify or delete another user's reservation

6. Cancellation Rules
A reservation can be canceled:
By the owner (user)
By admin
Once canceled:
It is removed (or optionally marked as canceled in future versions)

7. Admin Rules
Admin and super admin have full operational visibility for rooms and reservations
They can:
Manage all rooms (including soft delete and restore)
View all reservations
Delete any reservation
Super admin additionally manages user roles (user ↔ admin) as described in section 2.2

8. Data Integrity Rules
Foreign keys must always be valid
No orphan reservations: every reservation references a valid user and a valid room row
Deleting a user removes their reservations (CASCADE)
Soft-deleting a room does not remove reservations; the room row still exists
Hard-deleting a room row removes related reservations (CASCADE)

9. Concurrency Rule (Important for Real-World Behavior)
When two users attempt to book the same room at the same time:
The system must ensure:
Only one booking succeeds
The other fails due to overlap
This should be handled using:
Database transactions
Proper validation before insert

10. Error Handling Rules
The system should return clear errors for:
Overlapping booking → "This time slot is already booked"
Invalid time range → "End time must be after start time"
Unauthorized action → "You are not allowed to perform this action"

11. Future Enhancements (Optional Rules)
Reservation status (pending, confirmed, canceled)
Booking limits per user
Time buffer between bookings (e.g., 10 min gap)
Recurring reservations

12. Summary
This document ensures:
No double bookings
Proper user permissions
Clean and predictable system behavior
These rules must be strictly enforced in:
Controllers
Services (if used)
Database queries

