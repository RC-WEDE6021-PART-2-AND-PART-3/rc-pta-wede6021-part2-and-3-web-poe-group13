=============================================
PAST TIMES - Second Hand Clothing Store
PHP Web Application
=============================================

PROJECT STRUCTURE:
==================

clothing-store/
├── admin/
│   ├── dashboard.php      - Admin dashboard for user management
│   ├── login.php          - Admin login page
│   └── logout.php         - Admin logout
├── css/
│   └── style.css          - Main stylesheet
├── data/
│   ├── userData.txt       - Sample user data
│   ├── adminData.txt      - Admin user data
│   └── clothesData.txt    - Clothing items data
├── database/
│   ├── clothingstore.sql  - Database creation script
│   └── myClothingStore.sql - Complete DDL with 30 entries per table
├── images/                 - Product images folder (create as needed)
├── includes/
│   ├── DBConn.php         - Database connection file
│   ├── header.php         - Common header include
│   └── footer.php         - Common footer include
├── index.php              - Homepage
├── women.php              - Women's clothing section
├── men.php                - Men's clothing section
├── children.php           - Children's clothing (Girls & Boys)
├── cart.php               - Shopping cart
├── wishlist.php           - User wishlist
├── contact.php            - Contact us page
├── seller.php             - Seller submission page
├── login.php              - User login page
├── signup.php             - User registration page
├── logout.php             - User logout
├── createTable.php        - Script to create tblUser and load data
└── loadClothingStore.php  - Script to load entire database


SETUP INSTRUCTIONS:
===================

1. XAMPP/WAMP Setup:
   - Place the 'clothing-store' folder in your htdocs/www directory
   - Start Apache and MySQL services

2. Database Setup (Option 1 - PHPMyAdmin):
   - Open PHPMyAdmin (http://localhost/phpmyadmin)
   - Import the file: database/myClothingStore.sql
   - This will create the database with all tables and 30 sample entries each

3. Database Setup (Option 2 - PHP Script):
   - Navigate to: http://localhost/clothing-store/loadClothingStore.php
   - This will automatically create the database and load all data

4. Access the Application:
   - Homepage: http://localhost/clothing-store/index.php
   - Admin Panel: http://localhost/clothing-store/admin/login.php


LOGIN CREDENTIALS:
==================

Admin Login:
- Username: admin
- Password: password123

Sample Users (all have password: password123):
- john_doe / john@example.com (verified)
- jane_smith / jane@example.com (verified)
- mike_wilson / mike@example.com (pending verification)
- sarah_jones / sarah@example.com (verified)
- david_brown / david@example.com (verified)


FEATURES:
=========

Homepage:
- Welcome message
- Sale items display
- "Who are we" section

Women/Men/Children Sections:
- Product grid display
- Add to cart functionality
- Add to wishlist functionality
- Price display in Rands (R)

Cart:
- View cart items
- Remove items
- Promo code input
- Order summary
- Checkout functionality

Wishlist:
- View saved items
- Remove items
- Add to cart from wishlist
- Date added display

Seller Page:
- Submit items for sale
- Image upload
- Category selection

Authentication:
- User registration with email verification requirement
- Login with username/email and password
- Password hashing (bcrypt)
- Remember me functionality

Admin Dashboard:
- View all users
- Verify pending users
- Edit user details
- Delete users
- View statistics


DATABASE TABLES:
================

tblAdmin - Admin users
tblUser - Customer accounts
tblCategory - Product categories
tblClothes - Clothing items
tblCart - Shopping cart items
tblWishlist - Wishlist items
tblOrder - Customer orders
tblOrderItems - Order line items
tblContact - Contact form submissions


NOTES:
======

- Passwords are hashed using PHP's password_hash() function
- New user registrations require admin verification
- The application uses MySQLi with prepared statements for security
- Sessions are used for user authentication
- Font Awesome icons are used for visual elements


=============================================
Created for: Past Times Clothing Store
=============================================
