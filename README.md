# 🎟️ Event Booking API (Laravel 12)

This is a Laravel-based **Event Booking API** that supports user authentication, role-based access, event management, ticketing, bookings, and payments.

---

## 🚀 Features
- User authentication with **Laravel Sanctum**
- Role-based access control (`customer`, `organizer`, `admin`)
- Event management (create, update, delete, view)
- Ticket management (organizers/admins only)
- Booking system with double-booking prevention
- Payment handling for bookings

---

## 🛠️ Requirements
- **PHP** >= 8.2  
- **Composer** >= 2.x  
- **MySQL/MariaDB** (or any database supported by Laravel)  
- **Laravel** 12.x  
- **Git** (recommended)

---

## ⚙️ Installation

Clone the repository:
```bash
# clone repo
git clone https://github.com/Busuyem/Events-Management-App
cd blogapi

# install dependencies
composer install

# copy env and configure database
cp .env.example .env


# run migrations
php artisan migrate

# start server
php artisan serve
```

## 📡 API Endpoints Documentation

Kindly check the Post export inside docs/EventBooking.postman_collection.json




