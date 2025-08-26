# 🚀 Laravel Project

[![Laravel](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)](https://laravel.com)

## 📌 About This Project
This project is built with **Laravel**, the most popular PHP framework for modern web applications.  
It comes with a clean structure, powerful features, and scalability support for small to enterprise-level applications.

🔗 **Demo Video:** [Watch Here](https://drive.google.com/file/d/1u7dShgHqpLohbTM3vXD65h0bIF81Ye5y/view?usp=sharing)  
🗄️ **Database (SQL File):** [Download Here](https://drive.google.com/file/d/12w4xbwNNj1mQQb7gXQYcKb7R4yT0VRSf/view?usp=sharing)

---

## ✨ Features
- 🔥 Simple & fast **routing system**
- 🛠️ Powerful **Eloquent ORM** for database management
- 📦 Database migrations & seeders
- ⚡ Robust **authentication & authorization**
- 📡 Real-time event broadcasting
- 🔄 Queue & job processing
- 🎨 Clean, modern project structure

---

## 🛠️ Installation

Follow the steps below to set up the project on your local machine:

```bash
# Clone the repository
git clone <repo-link>

# Go into the project folder
cd project-folder

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations (with SQL file if needed)
php artisan migrate --seed

# Start development server
php artisan serve
