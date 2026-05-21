# 🛡️ MBSL Insurance Policy Management System

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-Server-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-Frontend-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

# MBSL Insurance Policy Management System

A modern and responsive Insurance Policy Management System developed using HTML, CSS, JavaScript, PHP, and MySQL.

</div>

---

# 📌 Overview

The **MBSL Insurance Policy Management System** is a fully functional web-based application designed to manage insurance policies efficiently through a modern and responsive interface.

The system allows administrators and users to:

- Manage insurance policies
- Track activities
- View dashboard analytics
- Export reports
- Maintain user profiles
- Use responsive mobile-friendly layouts
- Access dark mode interfaces

This project was developed using frontend and backend technologies with MySQL database integration.

---

# ✨ Features

## 🔐 Authentication System

- Secure Login System
- Admin & User Roles
- Session Handling
- Protected Pages

---

## 📋 Policy Management

- Add Policies
- Edit Policies
- Delete Policies
- View Policy Details
- Search Policies
- Filter Policies

---

## 📊 Dashboard Analytics

- Total Policies Counter
- Total Categories Counter
- Active Users Counter
- Premium Volume Statistics
- Interactive Charts
- Recent Alerts & Notifications

---

## 📄 Export Features

- Export Policy Data to Excel
- Export Policy Reports to PDF

---

## 👤 User Profile Management

- Update User Information
- Security Settings
- Password Management
- Activity Tracking

---

## 🌙 UI Features

- Responsive Design
- Mobile Friendly Layout
- Dark Mode Dashboard
- Modern User Interface

---

# 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Frontend Structure |
| CSS3 | Styling & Responsive Design |
| JavaScript | Frontend Functionality |
| PHP | Backend Development |
| MySQL | Database Management |
| XAMPP | Local Development Environment |
| Chart.js | Dashboard Charts |

---

# 📂 Project Structure

```text
mbsl-system/
│
├── api/
│   ├── config/
│   ├── controllers/
│   ├── uploads/
│   └── seed.php
│
├── screenshots/
│   ├── dashboard-dark.png
│   ├── dashboard-light.png
│   ├── er-diagram.png
│   ├── export-excel.png
│   ├── export.pdf.png
│   ├── policies-page.png
│   ├── profile-dark.png
│   └── profile-page.png
│
├── add-policy.html
├── dashboard.html
├── policies.html
├── profile.html
├── index.html
├── database.sql
├── README.md
└── .gitignore
```

---

# 🔐 Login Credentials

## 👨‍💼 Admin Account

```text
Email: admin@insurance.com
Password: admin123
```

---

## 👤 Normal User Account

```text
Email: user@insurance.com
Password: admin123
```

---

# 📱 Screenshots

---

# 1️⃣ Dashboard - Dark Mode

![Dashboard Dark](screenshots/dashboard-dark.png)

---

# 2️⃣ Dashboard - Light Mode

![Dashboard Light](screenshots/dashboard-light.png)

---

# 3️⃣ Policies Management Page

![Policies Page](screenshots/policies-page.png)

---

# 4️⃣ Profile Page

![Profile Page](screenshots/profile-page.png)

---

# 5️⃣ Profile Page - Dark Mode

![Profile Dark](screenshots/profile-dark.png)

---

# 6️⃣ Export Policies to Excel

![Export Excel](screenshots/export-excel.png)

---

# 7️⃣ Export Policies to PDF

![Export PDF](screenshots/export.pdf.png)

---

# 8️⃣ Database ER Diagram

![ER Diagram](screenshots/er-diagram.png)

---

# ⚙️ Installation & Setup Guide

## 📌 Prerequisites

Before running the project, install:

- XAMPP
- PHP 8+
- MySQL
- Modern Web Browser
- VS Code (Recommended)

---

# 🚀 Step 1 — Clone Repository

```bash
git clone https://github.com/ishani-perera/mbsl-system.git
```

---

# 🚀 Step 2 — Move Project to XAMPP

Move the project folder into:

```text
C:\xampp\htdocs\
```

Final path should be:

```text
C:\xampp\htdocs\mbsl-system-fixed\mbsl-system
```

---

# 🚀 Step 3 — Start Apache & MySQL

Open XAMPP Control Panel

Start:
- Apache
- MySQL

---

# 🚀 Step 4 — Import Database

## Open phpMyAdmin

```text
http://localhost/phpmyadmin
```

---

## Create Database

```text
mbsl_insurance
```

---

## Import SQL File

1. Click Import
2. Select:

```text
database.sql
```

3. Click Go

---

# 🚀 Step 5 — Run the Project

Open browser:

```text
http://localhost/mbsl-system-fixed/mbsl-system/index.html
```

---

# 🔌 API Endpoints

## 🔐 Authentication APIs

| Method | Endpoint | Description |
|---|---|---|
| POST | /api/login.php | User Login |
| POST | /api/register.php | User Registration |

---

## 📋 Policy APIs

| Method | Endpoint | Description |
|---|---|---|
| GET | /api/controllers/Policy.php?action=list | Get All Policies |
| GET | /api/controllers/Policy.php?action=stats | Get Dashboard Statistics |
| POST | /api/controllers/Policy.php?action=create | Create New Policy |
| PUT | /api/controllers/Policy.php?action=update | Update Policy |
| DELETE | /api/controllers/Policy.php?action=delete | Delete Policy |

---

# 📊 Dashboard Features

- Policy Performance Analytics
- Premium Volume Tracking
- Active User Monitoring
- Category Distribution
- Dashboard Statistics
- Recent Alerts

---

# 📄 Export Features

## Excel Export

Generate downloadable Excel reports for policy records.

---

## PDF Export

Generate professional PDF reports.

---

# 📱 Responsive Design

Optimized for:

- Desktop Devices
- Tablets
- Mobile Devices

---

# 🔒 Security Features

- Secure Authentication
- Session Management
- Input Validation
- Protected API Requests

---

# 🧪 Testing

## Tested Modules

- Login Authentication
- Policy CRUD Operations
- Search & Filtering
- Dashboard Analytics
- Export Features
- Responsive UI

---

# 🗂️ ER Diagram

The ER Diagram below represents the database structure and relationships used in the system.

![ER Diagram](screenshots/er-diagram.png)

---

# 🐞 Troubleshooting

## Apache Not Starting

- Close Skype / IIS
- Change Apache Port if required

---

## Database Connection Error

Ensure:
- MySQL is running
- Database credentials are correct
- SQL file is imported properly

---

## 404 Not Found Error

Ensure the project folder exists inside:

```text
htdocs/
```

Correct URL:

```text
http://localhost/mbsl-system-fixed/mbsl-system/index.html
```

---

# 📈 Future Improvements

- JWT Authentication
- Email Notifications
- Cloud Deployment
- Advanced Reports
- Role-Based Access Control

---

# 👩‍💻 Developer

## Ishani Perera

🔗 GitHub: https://github.com/ishani-perera

---

# 📜 License

This project is licensed under the MIT License.

---

# ⭐ Show Your Support

If you like this project, give it a ⭐ on GitHub.

---

# ❤️ Thank You

Developed for academic and educational purposes.

<div align="center">

Made with ❤️ using PHP, MySQL, JavaScript & CSS

</div>