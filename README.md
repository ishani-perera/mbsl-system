# 🛡️ MBSL Insurance Policy Management System

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-Server-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-Frontend-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

A modern, responsive, and fully functional Insurance Policy Management System developed using **HTML, CSS, JavaScript, PHP, and MySQL**.

</div>

---

## 📌 Overview

The **MBSL Insurance Policy Management System** is a complete web-based platform designed to manage insurance policies efficiently through a clean and responsive interface.

The system includes policy management, dashboard analytics, profile management, export reports, activity tracking, responsive design, and dark mode UI.

---

## ✨ Features

- Secure Login System
- Admin and Normal User Roles
- Dashboard Analytics
- Add, Edit, Delete, and View Policies
- Search and Filter Policies
- Export Policies to Excel
- Export Policies to PDF
- Profile Management
- Activity Tracking
- Dark Mode Dashboard
- Responsive Desktop and Mobile UI

---

## 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Frontend Structure |
| CSS3 | Styling and Responsive Design |
| JavaScript | Frontend Functionality |
| PHP | Backend Development |
| MySQL | Database Management |
| XAMPP | Local Server Environment |
| Chart.js | Dashboard Charts |

---

## 📂 Project Structure

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
└── .gitignore
```

---

## 🔐 Login Credentials

### Admin Account

```text
Email: admin@insurance.com
Password: admin123
```

### Normal User Account

```text
Email: user@insurance.com
Password: admin123
```

---

## 📱 Screenshots

### 1. Dashboard - Dark Mode

![Dashboard Dark](mbsl-system/screenshots/dashboard-dark.png)

---

### 2. Dashboard - Light Mode

![Dashboard Light](mbsl-system/screenshots/dashboard-light.png)

---

### 3. Policies Management Page

![Policies Page](mbsl-system/screenshots/policies-page.png)

---

### 4. Profile Page

![Profile Page](mbsl-system/screenshots/profile-page.png)

---

### 5. Profile Page - Dark Mode

![Profile Dark](mbsl-system/screenshots/profile-dark.png)

---

### 6. Export Policies to Excel

![Export Excel](mbsl-system/screenshots/export-excel.png)

---

### 7. Export Policies to PDF

![Export PDF](mbsl-system/screenshots/export.pdf.png)

---

### 8. Database ER Diagram

![ER Diagram](mbsl-system/screenshots/er-diagram.png)

---

## ⚙️ Installation & Setup Guide

### Prerequisites

Install the following software before running the project:

- XAMPP
- PHP 8+
- MySQL
- VS Code
- Modern Web Browser

---

### Step 1 — Clone Repository

```bash
git clone https://github.com/ishani-perera/mbsl-system.git
```

---

### Step 2 — Move Project to XAMPP

Move the project folder into:

```text
C:\xampp\htdocs\
```

Final path example:

```text
C:\xampp\htdocs\mbsl-system\mbsl-system
```

---

### Step 3 — Start Apache and MySQL

Open **XAMPP Control Panel** and start:

- Apache
- MySQL

---

### Step 4 — Import Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Create database:

```text
mbsl_insurance
```

Import this file:

```text
database.sql
```

---

### Step 5 — Run the Project

Open the project in browser:

```text
http://localhost/mbsl-system/mbsl-system/index.html
```

---

## 🔌 API Endpoints

### Authentication APIs

| Method | Endpoint | Description |
|---|---|---|
| POST | /api/login.php | User Login |
| POST | /api/register.php | User Registration |

### Policy APIs

| Method | Endpoint | Description |
|---|---|---|
| GET | /api/controllers/Policy.php?action=list | Get All Policies |
| GET | /api/controllers/Policy.php?action=stats | Get Dashboard Statistics |
| POST | /api/controllers/Policy.php?action=create | Create New Policy |
| PUT | /api/controllers/Policy.php?action=update | Update Policy |
| DELETE | /api/controllers/Policy.php?action=delete | Delete Policy |

---

## 🗂️ ER Diagram

The ER Diagram below represents the database structure and relationships used in the system.

![ER Diagram](mbsl-system/screenshots/er-diagram.png)

---

## 🧪 Testing

Tested modules:

- Login Authentication
- Policy CRUD Operations
- Search and Filtering
- Dashboard Analytics
- Excel Export
- PDF Export
- Responsive UI

---

## 🐞 Troubleshooting

### 404 Not Found Error

Make sure the project folder is inside:

```text
htdocs/
```

Correct URL example:

```text
http://localhost/mbsl-system/mbsl-system/index.html
```

### Database Connection Error

Check:

- MySQL is running
- Database is created
- `database.sql` is imported
- Database credentials are correct

---

## 📈 Future Improvements

- JWT Authentication
- Email Notifications
- Cloud Deployment
- Advanced Reports
- Role-Based Access Control

---

## 👩‍💻 Developer

**Ishani Perera**

GitHub: https://github.com/ishani-perera

---

## 📜 License

This project is licensed under the MIT License.

---

<div align="center">

Made with ❤️ using PHP, MySQL, JavaScript and CSS

</div>
