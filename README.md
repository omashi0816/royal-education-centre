\# Royal Education Centre – Hybrid Education Management System



A web-based \*\*Hybrid Education Management System (HEMS)\*\* developed for \*\*Royal Education Centre\*\* as a final-year academic software engineering project.



The system brings together academic, administrative, attendance, examination, payment, and online learning activities into a centralized platform.



\## 📌 Project Overview



The Royal Education Centre HEMS is designed to reduce manual work and improve the management of students, teachers, courses, attendance, examinations, payments, and educational activities.



The system supports both \*\*physical and online education environments\*\* and provides different features based on user roles.



\## ✨ Key Features



\* Secure user authentication

\* Role-based access control

\* Student management

\* Teacher and staff management

\* Course and subject management

\* Batch and enrollment management

\* Timetable management

\* Student attendance tracking

\* Assignment management

\* Examination and result management

\* Payment and due-payment management

\* Online class management

\* Study materials and notes

\* Notices and announcements

\* Reports

\* Activity logging

\* Database backup support

\* File upload validation

\* CSRF protection

\* Password hashing

\* Input validation and security controls



\## 👥 User Roles



The system provides role-based functionality for:



\* \*\*Administrator\*\* – System and user management

\* \*\*Manager\*\* – Operational management and approvals

\* \*\*Teacher\*\* – Courses, students, attendance, assignments, examinations and results

\* \*\*Student\*\* – Courses, timetable, attendance, assignments, examinations, results and payments

\* \*\*Receptionist\*\* – Student registration, enrollment and inquiries

\* \*\*Cashier\*\* – Payment collection, payment records and reports



\## 🧩 Main Modules



\### Administration



\* Dashboard

\* Users

\* Students

\* Teachers

\* Staff

\* Courses

\* Subjects

\* Batches

\* Timetable

\* Attendance

\* Assignments

\* Examinations

\* Results

\* Payments

\* Reports

\* Notices

\* Activity Logs

\* Backup

\* Settings



\### Academic Management



\* Student enrollment

\* Course management

\* Subject management

\* Batch management

\* Timetable management

\* Attendance

\* Assignments

\* Examinations

\* Results

\* Study materials



\### Financial Management



\* Payment collection

\* Payment records

\* Due payments

\* Payment reports



\### Online Learning



\* Online classes

\* Study materials

\* Assignments

\* Examination activities



\## 🛠️ Technology Stack



\### Frontend



\* HTML5

\* CSS3

\* Bootstrap 5

\* JavaScript



\### Backend



\* PHP

\* PHP MVC-inspired architecture



\### Database



\* MySQL

\* phpMyAdmin



\### Development Tools



\* Visual Studio Code

\* XAMPP

\* Google Chrome

\* Git

\* GitHub



\## 🏗️ System Architecture



The system follows a three-tier architecture:



```text

Presentation Layer

&#x20;       ↓

Application Layer

&#x20;       ↓

Database Layer

```



The PHP application handles business logic and communicates with the MySQL database, while the presentation layer provides the user interface.



\## 🔐 Security Features



Security was considered throughout the development of the system.



\* Password hashing using PHP `password\_hash()`

\* Role-based access control

\* Session management

\* CSRF protection

\* PDO prepared statements

\* Input validation and sanitization

\* File upload validation

\* Login attempt protection

\* Activity logging

\* Access restrictions for unauthorized users



\## 📁 Project Structure



```text

royal-education-centre/

│

├── app/

│   ├── config/

│   ├── core/

│   ├── controllers/

│   ├── models/

│   ├── views/

│   └── helpers/

│

├── database/

│   └── schema.sql

│

├── public/

│   ├── assets/

│   ├── uploads/

│   └── index.php

│

├── .gitignore

├── .htaccess

├── DATABASE.md

├── SETUP.md

├── USER\_MANUAL.md

└── README.md

```



\## 🗄️ Database



The system uses a MySQL relational database to manage the main application data.



Key database areas include:



\* Users

\* Students

\* Teachers

\* Parents

\* Courses

\* Subjects

\* Batches

\* Enrollments

\* Timetables

\* Attendance

\* Assignments

\* Examinations

\* Results

\* Payments

\* Notices

\* Activity Logs



The database schema is available in:



```text

database/schema.sql

```



\## ⚙️ Installation



\### Prerequisites



\* XAMPP

\* PHP 8.0 or higher

\* MySQL

\* Apache

\* phpMyAdmin

\* Modern web browser



\### Setup



1\. Clone or download this repository.



2\. Place the project inside the XAMPP `htdocs` directory.



```text

C:\\xampp\\htdocs\\royal-edu-center

```



3\. Start \*\*Apache\*\* and \*\*MySQL\*\* using XAMPP.



4\. Create a MySQL database named:



```text

royal\_edu\_center

```



5\. Import:



```text

database/schema.sql

```



into the database using phpMyAdmin.



6\. Configure the local database settings in:



```text

app/config/config.php

```



7\. Make sure the database connection details match your local XAMPP environment.



8\. Open the application:



```text

http://localhost/royal-edu-center/public

```



> This project is configured for a local XAMPP development environment. Login credentials and sample data are intentionally not published in this public repository.



\## 📸 Screenshots



Screenshots of the system interface can be added here, including:



\* Login page

\* Admin dashboard

\* Receptionist dashboard

\* Cashier dashboard

\* Teacher dashboard

\* Student dashboard

\* Student management

\* Attendance management

\* Examination and results

\* Payment management



\## 🧪 Testing



The system was tested using functional and security-oriented test cases covering areas such as:



\* User login

\* Student registration

\* Payment processing

\* Role-based access

\* CSRF protection

\* Activity logging

\* File upload validation

\* Unauthorized page access



\## 🎓 Academic Project



\*\*Project:\*\* Royal Education Centre – Hybrid Education Management System



\*\*Module:\*\* Computer Project



\*\*Institution:\*\* ICBT



\*\*University:\*\* Cardiff Metropolitan University



\*\*Lecturer:\*\* Mr. Deloosha Abeysooriya



\*\*Team Members:\*\*



\* Omashi

\* Kavishka

\* Sandaru

\* Nethsara



\## 🚀 Future Improvements



Possible future improvements include:



\* Mobile application enhancements

\* Online payment gateway integration

\* Advanced reporting and analytics

\* Automated notifications

\* Cloud deployment

\* Improved backup and recovery mechanisms

\* Additional online learning features



\## 📚 Project Purpose



This project was developed as an academic software engineering project to apply practical knowledge in:



\* Web development

\* Object-oriented programming

\* Database management

\* Software engineering

\* System analysis and design

\* Software testing

\* Security

\* Version control



\## 👨‍💻 Author



\*\*Omashi Sellahewa\*\*



Software Engineering Student



GitHub: \[omashi0816](https://github.com/omashi0816)



LinkedIn: \[Omashi Sellahewa](https://www.linkedin.com/in/omashisellahewa9769213a/)



\---



\*\*Note:\*\* This project is developed for educational and academic purposes.



