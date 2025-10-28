# 🍽️ Grilli Restaurant Management System

A comprehensive restaurant management system built with PHP and MySQL for handling reservations, menu management, customer data, and administrative tasks.

## 📋 Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Database Structure](#database-structure)
- [Usage](#usage)
- [Default Credentials](#default-credentials)
- [File Structure](#file-structure)
- [API Endpoints](#api-endpoints)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### Customer Management
- Customer registration and login
- Customer profile management
- Visit tracking
- Secure password authentication

### Reservation System
- Table reservation management
- Real-time table availability
- Multiple time slots
- Reservation status tracking (pending, confirmed, cancelled, completed)
- Customer messaging system

### Menu Management
- Menu item creation and management
- Menu categorization (breakfast, lunch, dinner)
- Calorie and pricing information
- Spice level indicators
- Cooking time estimates
- Rating system
- Menu image support

### Table Management
- Table capacity management
- Table status tracking (available, occupied, reserved, maintenance)
- Automatic table assignment
- Person-to-table mapping

### Administrative Features
- Admin dashboard
- Reservation management
- Menu administration
- Customer management
- Analytics and reporting

## 🔧 System Requirements

- **Web Server**: Apache/Nginx
- **PHP**: Version 7.4 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **Extensions**: 
  - PDO MySQL
  - mysqli
  - JSON support

## 🚀 Installation

### Method 1: One-Click Installation

1. **Download the Files**
   ```bash
   git clone https://github.com/yourusername/grilli-restaurant.git
   cd grilli-restaurant
   ```

2. **Upload to Web Server**
   - Upload all files to your web server directory
   - Ensure proper file permissions (755 for directories, 644 for files)

3. **Run the Installer**
   - Navigate to `http://yoursite.com/install.php`
   - Fill in your database credentials
   - Click "Install Database & Tables"
   - Wait for the success message

4. **Security Step**
   - Delete or move `install.php` after installation
   - Change default admin password immediately

### Method 2: Manual Installation

1. **Create Database**
   ```sql
   CREATE DATABASE grilli CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

2. **Import Database**
   ```bash
   mysql -u username -p grilli < grilli.sql
   ```

3. **Configure Database Connection**
   - Update database connection settings in your configuration files
   - Ensure proper credentials are set

## 🗄️ Database Structure

### Tables Overview

| Table | Purpose | Records |
|-------|---------|---------|
| `admin` | Administrator accounts | Admin users |
| `customer` | Customer accounts | Registered customers |
| `menu` | Menu items | Restaurant menu |
| `reservations` | Table reservations | Booking records |
| `table_persons_mapping` | Table configurations | Table capacity info |

### Key Relationships

```
customer (1) ──── (many) reservations
table_persons_mapping (1) ──── (many) reservations
```

### Field Details

#### Admin Table
- `id` - Primary key
- `adm_name` - Administrator name
- `adm_email` - Administrator email
- `adm_ph` - Administrator phone
- `password` - Administrator password

#### Customer Table
- `id` - Primary key
- `cus_name` - Customer name
- `cus_email` - Customer email (unique)
- `cus_ph` - Customer phone
- `total_visit` - Visit counter
- `password` - Customer password
- `created_at` - Registration timestamp
- `updated_at` - Last update timestamp

#### Menu Table
- `id` - Primary key
- `menu_name` - Menu item name
- `menu_type` - Category (breakfast/lunch/dinner)
- `calories` - Calorie count
- `menu_price` - Item price
- `cooking_time` - Preparation time
- `spice_level` - Spice level (low/medium/high)
- `menu_img` - Image path
- `rating` - Average rating
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

#### Reservations Table
- `id` - Primary key
- `name` - Customer name
- `phone` - Customer phone
- `persons` - Number of people
- `reservation_date` - Reservation date
- `time_slot` - Time slot
- `message` - Special requests
- `table_no` - Assigned table
- `status` - Reservation status
- `user_id` - Foreign key to customer
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

#### Table Persons Mapping
- `id` - Primary key
- `table_no` - Table number (unique)
- `max_persons` - Maximum capacity
- `status` - Table status
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## 🎯 Usage

### For Customers
1. **Registration**: Create account with email and password
2. **Login**: Access customer dashboard
3. **Browse Menu**: View available menu items
4. **Make Reservation**: Book tables for specific dates/times
5. **Manage Reservations**: View and modify existing bookings

### For Administrators
1. **Admin Login**: Access admin dashboard
2. **Manage Reservations**: Confirm, cancel, or modify bookings
3. **Menu Management**: Add, edit, or remove menu items
4. **Customer Management**: View customer information and history
5. **Table Management**: Configure table capacity and status

## 🔑 Default Credentials

### Administrator Access
- **Email**: `admin@gmail.com`
- **Password**: `admin123`

> ⚠️ **Important**: Change these credentials immediately after installation!

### Sample Data
- **Tables**: 5 tables with capacities of 2, 4, 6, 8, and 10 persons
- **Status**: All tables initially set to "available"


## 🔌 API Endpoints

### Authentication
- `POST /api/login.php` - User login
- `POST /api/register.php` - User registration
- `POST /api/logout.php` - User logout

### Reservations
- `GET /api/reservations.php` - Get reservations
- `POST /api/reservations.php` - Create reservation
- `PUT /api/reservations.php` - Update reservation
- `DELETE /api/reservations.php` - Cancel reservation

### Menu
- `GET /api/menu.php` - Get menu items
- `POST /api/menu.php` - Add menu item (admin)
- `PUT /api/menu.php` - Update menu item (admin)
- `DELETE /api/menu.php` - Delete menu item (admin)

### Tables
- `GET /api/tables.php` - Get available tables
- `POST /api/tables.php` - Update table status (admin)

## 🔒 Security

### Implemented Security Measures
- **Password Hashing**: Use PHP's `password_hash()` and `password_verify()`
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Implement CSRF tokens for forms
- **Session Management**: Secure session handling

### Security Best Practices
1. **Change Default Credentials**: Update admin password immediately
2. **File Permissions**: Set proper file permissions (755/644)
3. **Remove Installer**: Delete `install.php` after installation
4. **Regular Updates**: Keep system updated
5. **Database Security**: Use strong database passwords
6. **SSL/TLS**: Implement HTTPS for production

### Recommended Security Headers
```apache
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
```

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork the Repository**
   ```bash
   git fork https://github.com/yourusername/grilli-restaurant.git
   ```

2. **Create Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make Changes**
   - Follow coding standards
   - Add comments for complex logic
   - Test thoroughly

4. **Commit Changes**
   ```bash
   git commit -m "Add: your feature description"
   ```

5. **Push to Branch**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create Pull Request**
   - Provide clear description
   - Include screenshots if UI changes
   - Reference related issues

### Coding Standards
- **PHP**: Follow PSR-12 coding standards
- **SQL**: Use consistent naming conventions
- **HTML/CSS**: Use semantic HTML and organized CSS
- **JavaScript**: Use modern ES6+ features

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2025 Grilli Restaurant Management System

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## 📞 Support

For support, please contact:
- **Email**: support@grilli-restaurant.com
- **Documentation**: [Wiki](https://github.com/yourusername/grilli-restaurant/wiki)
- **Issues**: [GitHub Issues](https://github.com/yourusername/grilli-restaurant/issues)

## 🎉 Acknowledgments

- Thanks to all contributors
- Inspired by modern restaurant management needs
- Built with love for the hospitality industry

---

**Happy Coding! 🚀**

*Made with ❤️ for restaurants worldwide*