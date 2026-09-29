# Royal Education Center Management System

A comprehensive, production-ready education management system built with PHP 8+, MySQL, and Bootstrap 5. Designed for universities, tuition centers, academies, and training institutes.

## Features

### Role-Based Access Control (RBAC)
- **Administrator** — Full system access, user management, course/batch management, payments, reports, settings, backup/restore
- **Manager** — Operations management, approvals, timetables, reports
- **Teacher** — Course management, attendance marking, assignments, exams, grade entry
- **Student** — Course enrollment, class access, assignments, exams, results, payments
- **Receptionist** — Student registration, enrollments, inquiries
- **Cashier** — Payment collection, receipts, financial reports

### Core Modules
- User Management with role assignment
- Student, Teacher, Staff profiles
- Course, Subject, Batch management
- Timetable and class scheduling
- Assignment creation and submission
- Online examination with auto-grading
- Attendance tracking
- Payment processing and receipts
- Notice/announcement system
- Activity logging
- Report generation

### Security Features
- Password hashing (bcrypt)
- Login attempt protection
- Session timeout
- CSRF protection
- SQL injection prevention (PDO prepared statements)
- XSS prevention
- Input sanitization
- Role-based permissions

## Technology Stack

- **Backend:** PHP 8+
- **Database:** MySQL 8+ / MariaDB 10.4+
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Architecture:** MVC-inspired
- **Server:** XAMPP-compatible

## Project Structure

```
royal-edu-center/
├── app/
│   ├── config/          # Configuration files
│   ├── core/            # Core classes (Database, Controller, Model, View)
│   ├── controllers/     # Application controllers
│   ├── models/          # Data models
│   ├── views/           # View templates
│   │   ├── layouts/     # Main layout files
│   │   ├── partials/    # Reusable partials
│   │   ├── auth/        # Authentication views
│   │   ├── admin/       # Admin views
│   │   ├── manager/     # Manager views
│   │   ├── teacher/     # Teacher views
│   │   ├── student/     # Student views
│   │   ├── receptionist/# Receptionist views
│   │   └── cashier/     # Cashier views
│   └── helpers/         # Helper functions
├── database/
│   ├── schema.sql       # Database schema
│   └── seed.sql         # Sample data
├── public/
│   ├── assets/          # CSS, JS, images
│   ├── uploads/         # File uploads
│   └── index.php        # Front controller
├── .htaccess            # URL rewriting
└── README.md            # This file
```

## Installation

### Prerequisites
- XAMPP (or equivalent PHP/MySQL stack)
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache web server with mod_rewrite enabled

### Setup Instructions

1. **Clone/Download the project**
   - Copy the project folder to `htdocs` in XAMPP (e.g., `C:\xampp\htdocs\royal-edu-center`)

2. **Configure Database**
   - Open `app/config/config.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'royal_edu_center');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

3. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `royal_edu_center`

4. **Import Schema**
   - Import `database/schema.sql` into the database
   - Import `database/seed.sql` for sample data

5. **Update Base URL**
   - In `app/config/config.php`, update:
     ```php
     define('BASE_URL', 'http://localhost/royal-edu-center/public');
     ```

6. **Set Permissions**
   - Ensure `public/uploads/` directory is writable

7. **Access the Application**
   - Open browser: `http://localhost/royal-edu-center/public`
   - Default Admin Login:
     - Username: `admin`
     - Password: `admin123`

## Default Users

| Role | Username | Password |
|------|----------|----------|
| Administrator | admin | admin123 |
| Manager | manager | manager123 |
| Receptionist | reception | reception123 |
| Cashier | cashier | cashier123 |

## Database Design

The database is fully normalized (3NF) with the following key tables:
- `users` — User accounts with role assignment
- `roles` — User roles
- `permissions` — System permissions
- `students` — Student profiles
- `teachers` — Teacher profiles
- `staff` — Staff profiles
- `courses` — Course information
- `subjects` — Subject information
- `batches` — Course batches/intakes
- `enrollments` — Student enrollments
- `timetable_slots` — Class schedules
- `online_classes` — Online class sessions
- `assignments` — Assignments
- `assignment_submissions` — Student submissions
- `exams` — Examinations
- `exam_questions` — Exam questions
- `exam_results` — Exam results
- `payments` — Payment records
- `receipts` — Payment receipts
- `notices` — Notices/announcements
- `activity_logs` — Audit trail

See `DATABASE.md` for detailed ERD specification.

## Security Considerations

- All passwords are hashed using `password_hash()` (bcrypt)
- SQL injection prevented using PDO prepared statements
- CSRF tokens on all forms
- Session timeout after 30 minutes of inactivity
- Login attempt limitation (5 attempts, 15-minute lockout)
- Input sanitization and validation
- File upload validation

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Edge
- Safari

## Support

For issues, questions, or contributions, please contact the development team.

## License

This project is developed for educational purposes as a university final-year project.

---

**Version:** 1.0.0  
**Last Updated:** <?= date('Y-m-d') ?>
