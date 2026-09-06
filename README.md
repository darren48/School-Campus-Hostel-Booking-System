# Grand TAR ABC — Student Hostel Booking System

PHP + MySQL student hostel accommodation booking system, adapted from a hotel-style design for **Grand TAR ABC** student hostel.

## Features

### User (Student) Mode
- Browse accommodation (Single, Twin, 4-Bed, 6-Bed)
- View facilities
- Book by **Move-in / Move-out** dates
- Bed-capacity aware availability check
- Register / Login
- View & cancel own bookings

### Admin Mode
- Dashboard (students, rooms, beds free, pending/confirmed bookings)
- Manage bookings (confirm / cancel / complete)
- Manage rooms (CRUD, toggle availability)
- Manage students

## Tech Stack
- PHP 8+ (PDO)
- MySQL / MariaDB
- Bootstrap 5
- Font Awesome 6

## Setup

1. **Create database**
   ```bash
   mysql -u root -p < sql/schema.sql
   ```

2. **Configure**
   Edit `config/database.php`:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `SITE_URL` (e.g. `http://localhost/grand-tar-abc`)

3. **Run**
   Place project under your web root (XAMPP/WAMP/Apache/Nginx) or deploy to AWS (EC2 + RDS).

## Default Accounts

| Role    | Username / Email              | Password   |
|---------|-------------------------------|------------|
| Admin   | `admin`                       | `admin123` |
| Student | `ali.ahmad@student.edu.my`    | `student123` |

*(Demo login also accepts plain `admin123` / `student123` if hash verification fails on some environments.)*

## Project Structure

```
grand-tar-abc/
├── admin/           # Admin panel
│   ├── index.php    # Dashboard
│   ├── bookings.php
│   ├── rooms.php
│   ├── students.php
│   ├── login.php
│   └── logout.php
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── config/database.php
├── includes/header.php, footer.php
├── sql/schema.sql
├── index.php
├── rooms.php
├── room-details.php
├── booking.php
├── process-booking.php
├── facilities.php
├── about.php
├── contact.php
├── login.php
├── register.php
├── my-bookings.php
└── logout.php
```

## Booking Logic (Hostel)

- Pricing is **per month × number of students**
- Availability checks **occupied beds** vs room capacity for overlapping date ranges
- Status flow: `pending` → `confirmed` → `completed` (or `cancelled`)

## AWS Deployment Notes

- Point `DB_HOST` to your RDS endpoint
- Use strong passwords and restricted security groups
- Serve via Apache/Nginx on EC2 or Elastic Beanstalk
- Ensure PHP PDO MySQL extension is enabled
