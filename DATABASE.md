# Royal Education Center Management System — Database Documentation

## Entity Relationship Diagram (ERD)

### Core Entities

```
┌─────────────┐
│    Roles    │
├─────────────┤
│ id (PK)     │
│ name        │
│ display_name│
│ description │
└──────┬──────┘
       │
       │ 1
       │
┌──────▼──────┐       ┌──────────────┐
│    Users    │──────►│ Permissions  │
├─────────────┤ 1:N  ├──────────────┤
│ id (PK)     │       │ id (PK)      │
│ role_id (FK)│       │ name         │
│ username    │◄──────│ display_name │
│ email       │ N:1   │ module       │
│ password_hash│      └──────────────┘
│ full_name   │
│ status      │
└──────┬──────┘
       │
       │ 1
       │
   ┌───▼──────────────────────────────────────┐
   │                                          │
┌──▼──────┐  ┌──────────┐  ┌──────────┐      │
│ Students │  │ Teachers │  │  Staff   │      │
├─────────┤  ├──────────┤  ├──────────┤      │
│id (PK)   │  │id (PK)   │  │id (PK)   │      │
│user_id  │  │user_id   │  │user_id   │      │
│code     │  │code      │  │code      │      │
│...      │  │...       │  │...       │      │
└─────────┘  └──────────┘  └──────────┘      │
   │              │              │            │
   │              │              │            │
   └──────────────┴──────────────┴────────────┘
                  │
                  │
         ┌────────▼─────────┐
         │   Enrollments    │
         ├──────────────────┤
         │ id (PK)          │
         │ student_id (FK)  │◄────┐
         │ batch_id (FK)    │     │
         │ enrolled_date    │     │
         └──────────────────┘     │
                                  │
┌─────────────┐      ┌──────────▼────────┐
│   Courses   │─────►│      Batches      │
├─────────────┤ 1:N ├───────────────────┤
│ id (PK)     │      │ id (PK)           │
│ code        │      │ course_id (FK)    │
│ name        │      │ batch_name        │
│ course_fee  │      │ start_date        │
│ ...         │      │ end_date          │
└──────┬──────┘      │ status            │
       │             └───────────────────┘
       │ 1
       │
┌──────▼────────────────────┐
│     Course_Subjects       │
├───────────────────────────┤
│ course_id (FK)            │
│ subject_id (FK)           │◄────┐
│ teacher_id (FK)           │     │
└───────────────────────────┘     │
                                     │
                              ┌──────▼──────┐
                              │  Subjects   │
                              ├─────────────┤
                              │ id (PK)     │
                              │ code        │
                              │ name        │
                              │ description │
                              └─────────────┘
```

## Table Descriptions

### Users
Base table for all system users with authentication credentials.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | User ID |
| role_id | INT | FOREIGN KEY → roles(id) | User role |
| username | VARCHAR(50) | UNIQUE | Login username |
| email | VARCHAR(150) | UNIQUE | Email address |
| password_hash | VARCHAR(255) | NOT NULL | Bcrypt hashed password |
| full_name | VARCHAR(150) | NOT NULL | Full name |
| phone | VARCHAR(20) | NULL | Phone number |
| status | ENUM | 'active','inactive','suspended' | Account status |
| login_attempts | INT | DEFAULT 0 | Failed login count |
| locked_until | DATETIME | NULL | Account lock expiry |
| last_login | DATETIME | NULL | Last login timestamp |

### Roles
User roles for RBAC system.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Role ID |
| name | VARCHAR(50) | UNIQUE | Role identifier |
| display_name | VARCHAR(100) | NOT NULL | Display name |
| description | TEXT | NULL | Role description |

### Students
Student profile data linked to users.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Student ID |
| user_id | INT | FOREIGN KEY → users(id), UNIQUE | Linked user |
| student_code | VARCHAR(20) | UNIQUE | Student code |
| first_name | VARCHAR(60) | NOT NULL | First name |
| last_name | VARCHAR(60) | NOT NULL | Last name |
| gender | ENUM | 'male','female','other' | Gender |
| date_of_birth | DATE | NULL | Date of birth |
| address | TEXT | NULL | Address |
| guardian_name | VARCHAR(150) | NULL | Guardian name |
| guardian_phone | VARCHAR(20) | NULL | Guardian phone |

### Teachers
Teacher profile data linked to users.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Teacher ID |
| user_id | INT | FOREIGN KEY → users(id), UNIQUE | Linked user |
| teacher_code | VARCHAR(20) | UNIQUE | Teacher code |
| qualification | VARCHAR(200) | NULL | Qualification |
| specialization | VARCHAR(200) | NULL | Specialization |
| experience_years | INT | DEFAULT 0 | Years of experience |
| monthly_salary | DECIMAL(10,2) | DEFAULT 0.00 | Monthly salary |

### Courses
Course information.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Course ID |
| code | VARCHAR(20) | UNIQUE | Course code |
| name | VARCHAR(200) | NOT NULL | Course name |
| course_fee | DECIMAL(10,2) | NOT NULL | Course fee |
| registration_fee | DECIMAL(10,2) | NOT NULL | Registration fee |
| monthly_fee | DECIMAL(10,2) | NOT NULL | Monthly fee |
| duration_months | INT | DEFAULT 1 | Duration in months |
| status | ENUM | 'pending','approved','active','completed','cancelled' | Course status |

### Batches
Course batches/intakes.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Batch ID |
| course_id | INT | FOREIGN KEY → courses(id) | Parent course |
| batch_name | VARCHAR(100) | NOT NULL | Batch name |
| start_date | DATE | NOT NULL | Start date |
| end_date | DATE | NOT NULL | End date |
| max_students | INT | DEFAULT 50 | Maximum students |
| status | ENUM | 'pending','approved','active','completed','cancelled' | Batch status |

### Enrollments
Student enrollments in batches.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Enrollment ID |
| student_id | INT | FOREIGN KEY → students(id) | Student |
| batch_id | INT | FOREIGN KEY → batches(id) | Batch |
| enrolled_date | DATE | NOT NULL | Enrollment date |
| status | ENUM | 'pending','active','completed','dropped' | Enrollment status |
| final_grade | VARCHAR(5) | NULL | Final grade |

### Payments
Payment records.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Payment ID |
| student_id | INT | FOREIGN KEY → students(id) | Student |
| enrollment_id | INT | FOREIGN KEY → enrollments(id) | Enrollment |
| payment_type | ENUM | 'registration','course_fee','monthly_fee','exam_fee','other' | Payment type |
| amount | DECIMAL(10,2) | NOT NULL | Amount |
| discount | DECIMAL(10,2) | DEFAULT 0.00 | Discount |
| final_amount | DECIMAL(10,2) | NOT NULL | Final amount |
| payment_method | ENUM | 'cash','online','bank_transfer','card' | Payment method |
| payment_date | DATE | NOT NULL | Payment date |
| status | ENUM | 'pending','completed','failed','refunded' | Status |

### Activity Logs
Audit trail for all system actions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Log ID |
| user_id | INT | FOREIGN KEY → users(id) | User (nullable) |
| action | VARCHAR(100) | NOT NULL | Action performed |
| module | VARCHAR(50) | NOT NULL | Module affected |
| description | TEXT | NULL | Description |
| ip_address | VARCHAR(45) | NULL | IP address |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp |

## Normalization

The database follows Third Normal Form (3NF):
- All tables have a primary key
- All non-key attributes depend on the primary key
- No transitive dependencies
- Foreign keys ensure referential integrity

## Indexes

Key indexes for performance:
- `users.username`, `users.email`, `users.status`
- `students.student_code`
- `teachers.teacher_code`
- `courses.code`, `courses.status`
- `batches.course_id`, `batches.status`
- `enrollments.student_id`, `enrollments.batch_id`
- `payments.student_id`, `payments.payment_date`, `payments.status`
- `activity_logs.user_id`, `activity_logs.created_at`

## Constraints

- All foreign keys have CASCADE or RESTRICT rules
- Unique constraints on codes, usernames, emails
- ENUM constraints for status fields
- NOT NULL on critical fields
- Default values where appropriate

---

**Version:** 1.0.0
