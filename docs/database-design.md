Database Design Document
Project: Reservo

1. Overview
This document defines the database structure for the Reservo system. It includes tables, fields, relationships, and constraints required to support the reservation functionality while ensuring data integrity and preventing overlapping bookings.

2. Entities
The system consists of three core entities:
Users
Rooms
Reservations

3. Tables & Fields
3.1 Users Table
Stores all registered users.
Field
Type
Description
id
BIGINT (PK)
Unique identifier
name
VARCHAR
User's name
email
VARCHAR
Unique email address
password
VARCHAR
Hashed password
role
ENUM
'user', 'admin', or 'super_admin'
created_at
TIMESTAMP
Created timestamp
updated_at
TIMESTAMP
Updated timestamp


3.2 Rooms Table
Stores available rooms for booking.
Field
Type
Description
id
BIGINT (PK)
Unique identifier
name
VARCHAR
Room name
capacity
INT
Number of people
description
TEXT
Room details
image_url
VARCHAR (nullable, long URL)
Optional hero/cover image for listings (e.g. CDN or Unsplash)
deleted_at
TIMESTAMP (nullable)
Soft delete timestamp; when set, the room is hidden from public listings but historical reservations remain
created_at
TIMESTAMP
Created timestamp
updated_at
TIMESTAMP
Updated timestamp


3.3 Reservations Table
Stores booking records.
Field
Type
Description
id
BIGINT (PK)
Unique identifier
user_id
BIGINT (FK)
References users.id
room_id
BIGINT (FK)
References rooms.id
date
DATE
Reservation date
start_time
TIME
Start time
end_time
TIME
End time
created_at
TIMESTAMP
Created timestamp
updated_at
TIMESTAMP
Updated timestamp


4. Relationships
A user can have many reservations
A room can have many reservations
A reservation belongs to one user
A reservation belongs to one room
Relationship Summary:
users (1) → (many) reservations
rooms (1) → (many) reservations

5. Constraints & Rules
5.1 Foreign Key Constraints
reservations.user_id → users.id (ON DELETE CASCADE)
reservations.room_id → rooms.id (ON DELETE CASCADE)

Note: Rooms use soft deletes at the application level. Soft-deleting a room does not remove its row, so existing reservations keep a valid room_id. If a room row is hard-deleted from the database, related reservations are removed by CASCADE.

5.2 Unique & Validation Rules
email must be unique in users table
reservation must satisfy:
start_time < end_time
no overlapping reservations for the same room

5.3 Overlapping Rule (Critical)
A reservation is invalid if:
It has the same room_id
AND the same date
AND time overlaps with an existing reservation
Overlap condition:
New reservation conflicts if:
(start_time < existing_end_time) AND (end_time > existing_start_time)

6. Indexing (Performance Optimization)
Recommended indexes:
Index on reservations.room_id
Index on reservations.date
Composite index:
(room_id, date, start_time, end_time)

7. ERD (Text Representation)
Users
id (PK)
name
email
password
role
Rooms
id (PK)
name
capacity
description
deleted_at (nullable)
Reservations
id (PK)
user_id (FK)
room_id (FK)
date
start_time
end_time
Relationships:
Users → Reservations (1:N)
Rooms → Reservations (1:N)

8. Notes
Timezone handling is not included in MVP
Rooms use soft deletes (implemented)
Future improvements may include:
status field (confirmed/cancelled)
recurring bookings

9. Summary
This schema ensures:
Clear relationships between users, rooms, and reservations
Prevention of double bookings through application logic
Scalability for future enhancements

