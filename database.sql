-- =====================================================================
-- Food Ordering System - Database Schema (v2)
-- =====================================================================
-- Import this file in phpMyAdmin (or run via MySQL CLI / `mysql -u root -p < database.sql`)
-- It will create the database and all tables required by the PHP code,
-- including OTP verification, payments, and the admin-managed menu.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS food_ordering
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE food_ordering;

-- ---------------------------------------------------------------------
-- Table: users
-- Used by: signup.php, login.php, forgot.php, reset_password.php
-- is_verified: 0 until the signup OTP is confirmed in verify_otp.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: otps
-- Used by: includes/otp.php (signup verification + forgot password)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS otps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  otp_hash VARCHAR(255) NOT NULL,
  purpose ENUM('signup','reset') NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email_purpose (email, purpose)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: menu_items
-- Used by: menu.php, orders.php, home.php, admin/menu.php
-- Dishes now live in the database so the admin panel can add/edit/
-- delete them without touching any code.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) NOT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: orders
-- Used by: orders.php, payment.php, order_success.php, admin/orders.php
-- payment_method: COD / Card / UPI
-- payment_status: Pending / Paid / Pending Verification / Failed
-- order_status:   Placed / Preparing / Out for Delivery / Delivered / Cancelled
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  dish_name VARCHAR(150) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  customer_name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  address TEXT NOT NULL,
  payment_method VARCHAR(20) NOT NULL DEFAULT 'COD',
  payment_status VARCHAR(30) NOT NULL DEFAULT 'Pending',
  transaction_ref VARCHAR(150) DEFAULT NULL,
  order_status VARCHAR(30) NOT NULL DEFAULT 'Placed',
  notified TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- If you already imported an older version of this database, run these
-- once instead of re-importing everything:
--   ALTER TABLE orders ADD COLUMN user_id INT DEFAULT NULL AFTER id, ADD INDEX idx_user_id (user_id);
--   ALTER TABLE orders ADD COLUMN notified TINYINT(1) NOT NULL DEFAULT 1 AFTER order_status;


-- ---------------------------------------------------------------------
-- Table: reviews
-- Used by: review.php, admin/reviews.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  review TEXT NOT NULL,
  stars TINYINT NOT NULL DEFAULT 5,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: contact_messages
-- Used by: contact.php, admin/messages.php
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Sample data (optional - safe to delete if you don't want demo rows)
-- ---------------------------------------------------------------------
INSERT INTO reviews (name, review, stars) VALUES
('Kamal Perera', 'Amazing food and super fast delivery. Loved the biryani!', 5),
('Nimasha Silva', 'Good taste, but delivery was a bit late.', 4);

INSERT INTO menu_items (name, description, price, image, is_featured) VALUES
('Cheese Burger', 'Juicy grilled patty with melted cheese, lettuce & tomato.', 1200.00, 'IMAGES/Burger.jpg', 1),
('Veg Loaded Pizza', 'Loaded with cheese, capsicum, onion and olives.', 1800.00, 'IMAGES/Pizza.jpg', 1),
('Chicken Biryani', 'Aromatic rice cooked with tender chicken and spices.', 1500.00, 'IMAGES/Chicken Biryani.jpg', 1),
('Chicken Shawarma', 'Stuffed with spicy chicken and creamy garlic mayo.', 900.00, 'IMAGES/Chicken Shawarma.jpg', 0),
('Veg Noodles', 'Hot & spicy noodles with crunchy vegetables.', 850.00, 'IMAGES/Veg Noodles.jpg', 0),
('Fresh Orange Juice', '100% pure and freshly squeezed orange juice.', 400.00, 'IMAGES/Fresh Orange juice.jpg', 0);

-- ---------------------------------------------------------------------
-- NOTE ON ADMIN LOGIN
-- ---------------------------------------------------------------------
-- The admin account (username: Farhan / password: Farhan1234) is NOT
-- stored in the database - it lives in config.php as ADMIN_USERNAME /
-- ADMIN_PASSWORD so you can change it instantly without touching SQL.
-- See admin/login.php.
