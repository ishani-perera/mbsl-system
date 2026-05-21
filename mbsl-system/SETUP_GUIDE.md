# MBSL Insurance System - Setup & Troubleshooting Guide

## Quick Start - After System Import

After downloading and setting up the MBSL Insurance system, follow these steps:

### Step 1: Database Setup
1. Import `database.sql` into your MySQL database:
   - Database Name: `mbsl_new_db`
   - Charset: `utf8mb4`

### Step 2: Fix Demo Credentials
**This step is CRITICAL - run this FIRST before attempting to log in:**

1. Open your browser and navigate to:
   ```
   http://localhost/mbsl-fixed/fix-demo-credentials.php
   ```
   (Replace `localhost` with your actual server domain)

2. You should see a success message confirming the credentials are fixed

3. **DELETE** the `fix-demo-credentials.php` file after running it

### Step 3: Log In
Use the demo credentials:
- **Admin Login:**
  - Email: `admin@insurance.com`
  - Password: `admin123`

- **User Login:**
  - Email: `user@insurance.com`
  - Password: `user123`

---

## Issues Fixed in This Version

### ✓ Authentication Issue
**Problem:** "Authentication Failed" error even with correct credentials
**Root Cause:** Password hashes in database.sql did not match demo passwords
**Fix:** Created `fix-demo-credentials.php` script that regenerates correct password hashes

### ✓ Policy Creation Issue
**Problem:** "Data Saved Successfully" message but policy doesn't appear in list
**Root Cause:** 
1. API response structure was incomplete
2. Session validation was missing on policy creation
3. User ID wasn't being properly assigned

**Fixes Applied:**
- Enhanced Auth.php to ensure session persistence
- Updated Policy.php 'create' action to require user authentication
- Improved API response structure with better error messages

### ✓ Policy Display Issue
**Problem:** "Network Error – Could not load policies from database"
**Root Cause:**
1. API.js had weak JSON parsing that could fail on whitespace
2. Response structure was inconsistent
3. Error messages were not descriptive enough

**Fixes Applied:**
- Improved API.js request() method with robust JSON extraction
- Added console logging for debugging
- Enhanced error messages with specific failure reasons
- Fixed response structure in Policy.php 'list' action

---

## Database Configuration

The system uses these credentials (in `api/config/Database.php`):
```
Host: localhost
Database: mbsl_new_db
User: root
Password: (empty)
```

If your database credentials are different, update them in:
- `api/config/Database.php` - Database connection
- `database.sql` - May need to be re-imported if already created

---

## System Architecture

### Frontend Files
- `index.html` - Login page
- `dashboard.html` - Main dashboard
- `policies.html` - Policy list/inventory
- `add-policy.html` - Create new policy
- `js/api.js` - API client with MBSLApi class
- `js/app.js` - Global utilities and helper functions

### Backend Files
- `api/controllers/Auth.php` - Authentication (login, logout, check-auth)
- `api/controllers/Policy.php` - Policy management (CRUD operations)
- `api/config/Database.php` - Database connection singleton

### Database Tables
- `users` - User accounts (Admin, User roles)
- `policies` - Insurance policies
- `policy_images` - Policy attachments/images

---

## Testing the System

### Test 1: Authentication
1. Go to login page
2. Enter: admin@insurance.com / admin123
3. Should redirect to dashboard

### Test 2: Policy Creation
1. Click "New Policy" button
2. Fill in policy details
3. Click "Create Policy"
4. Should see "Policy Created!" message
5. Should redirect to policies page with new policy visible

### Test 3: Policy Listing
1. Go to "Policies" page
2. Should see all policies in table format
3. Can search and filter by category
4. Can export as CSV/PDF

---

## Troubleshooting

### Problem: "Invalid server response" or "Network Error"
**Solutions:**
1. Check browser console (F12) for detailed error messages
2. Verify database connection in `api/config/Database.php`
3. Check PHP error logs
4. Run `fix-demo-credentials.php` again

### Problem: Can't log in
**Solutions:**
1. Run `fix-demo-credentials.php` to ensure passwords are correct
2. Clear browser cookies/cache
3. Check database users table: `SELECT email, role FROM users;`

### Problem: Policies save but don't appear
**Solutions:**
1. Check browser console for API errors
2. Verify user is logged in (check session)
3. Check `policies` table: `SELECT * FROM policies;`
4. Refresh page after creating policy

### Problem: "User not authenticated" error on policy creation
**Solutions:**
1. Make sure you're logged in
2. Check if session is persisting
3. Run `fix-demo-credentials.php` and log in again

---

## File Cleanup

After setup, delete these temporary files:
- ✗ `fix-demo-credentials.php` - MUST DELETE after running
- ✗ `setup_admin.php` - (optional, if used)
- ✗ `setup_users.php` - (optional, if used)

These should NOT be left on production servers for security reasons.

---

## API Endpoints Reference

### Authentication
- `POST /api/controllers/Auth.php?action=login` - Login
- `POST /api/controllers/Auth.php?action=logout` - Logout
- `GET /api/controllers/Auth.php?action=check` - Check authentication status

### Policies
- `GET /api/controllers/Policy.php?action=list` - Get policies list
- `GET /api/controllers/Policy.php?action=get&id=N` - Get single policy
- `POST /api/controllers/Policy.php?action=create` - Create policy
- `POST /api/controllers/Policy.php?action=update&id=N` - Update policy
- `DELETE /api/controllers/Policy.php?action=delete&id=N` - Delete policy
- `GET /api/controllers/Policy.php?action=stats` - Get dashboard stats

---

## Security Notes

- All passwords are hashed using bcrypt (cost 12)
- CSRF protection via session tokens
- SQL injection protection via prepared statements
- XSS protection via output escaping
- CORS headers properly configured
- Session cookies are HTTP-only and secure

---

## Support & Contact

For issues or questions about this MBSL Insurance Management System, refer to:
1. Browser console (F12) for error details
2. PHP error logs in server
3. Check database tables directly for data verification
4. Review API response in Network tab (F12)

---

Generated: 2024
Version: 1.0.1
