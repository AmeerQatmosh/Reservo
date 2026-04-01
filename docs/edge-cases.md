Edge Cases & Validation Checklist
Project: Reservo

1. Overview
This document defines all edge cases and validation scenarios that must be handled to ensure system reliability, correctness, and a smooth user experience.
These cases should be tested and validated during development.

2. Reservation Edge Cases (Critical)
2.1 Time Overlap Scenarios
Ensure the system correctly handles:
Valid Cases ✅
10:00–11:00 and 11:00–12:00 → Allowed
09:00–10:00 and 10:00–11:00 → Allowed
Invalid Cases ❌
10:00–11:00 and 10:30–11:30 → Overlap
10:00–11:00 and 09:30–10:30 → Overlap
10:00–11:00 and 10:00–11:00 → Exact conflict

2.2 Same Time, Different Rooms
Same time slot is allowed if:
room_id is different

2.3 Boundary Conditions
End time equals next start time → Allowed
Start time equals existing start time → Not allowed
End time equals existing end time → Not allowed

2.4 Invalid Time Input
start_time >= end_time → Reject
Missing time values → Reject
Invalid format → Reject

2.5 Date Handling
Past dates → Rejected (after_or_equal today on the reservation date)
Very far future dates → Allowed

3. Concurrency Edge Cases (Advanced 🔥)
3.1 Simultaneous Booking
Scenario:
Two users try to book the same room at the same time
Expected behavior:
Only one succeeds
The other fails with overlap error
Solution:
Use database transactions
Re-check availability before saving

4. Authorization Edge Cases
4.1 Unauthorized Access
User tries to:
Access admin routes → Deny (403)
Delete another user’s reservation → Deny (403)
Admin tries to change user roles via PUT /admin/users/{id}/role → Deny (403); only super_admin may call that route

4.2 Direct URL Access
User manually enters URL:
/admin/rooms → Must be blocked for non-admins
GET /admin/users → Allowed for admin and super_admin (admin: read-only UI)
PUT /admin/users/{id}/role → Blocked unless super_admin
/reservations/{id}/edit → Must verify ownership (user can only edit own)

5. Data Integrity Edge Cases
5.1 Soft-deleting a Room
When an admin soft-deletes a room:
The room row remains in the database (deleted_at set)
The room no longer appears in public room listings
Existing reservations for that room remain for historical reporting
5.2 Restoring a Room
When an admin restores a soft-deleted room:
The room appears again in public listings
Past reservations tied to that room were never removed
5.3 Hard-deleting a Room or User
If a room or user row is permanently removed:
Related reservations are removed by foreign key ON DELETE CASCADE
5.4 Orphan Records
No reservation should exist without:
Valid user
Valid room row (soft-deleted room still counts as a valid row for FK purposes)

6. UI/UX Edge Cases
6.1 Empty States
No rooms available → Show message
No reservations → Show message

6.2 Form Errors
Show clear error messages:
"Time slot already booked"
"Invalid time range"
"All fields are required"

6.3 Duplicate Submission
User clicks submit multiple times:
Prevent duplicate bookings (disable button or use backend validation)

7. Validation Checklist
Reservation Creation
All required fields present
start_time < end_time
Valid date format
Date is not in the past
No overlapping booking exists
Room exists
User is authenticated

Room Management
Name is required
Capacity is a positive number
Only admin or super_admin can perform admin room actions

Authentication
Email is unique
Password is required
Secure login/logout flow

8. Error Messages (Standardization)
Use consistent messages:
Overlap → "This time slot is already booked"
Invalid time → "End time must be after start time"
Unauthorized → "You are not allowed to perform this action"
Not found → "Resource not found"

9. Future Edge Cases (Optional)
Timezone differences
Daylight saving changes
Booking limits per user
Buffer time between bookings

10. Summary
Handling these edge cases ensures:
No unexpected bugs
Reliable booking system
Better user experience
Strong backend logic
This is what separates a basic project from a production-ready system.

