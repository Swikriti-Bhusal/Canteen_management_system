## Database Setup
Below is the SQL code to create the tables used in the Canteen Management System:
```sql
CREATE DATABASE IF NOT EXISTS Food_menu;
USE Food_menu;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,          -- Not unique (allows duplicates)
    `email` VARCHAR(100) NOT NULL UNIQUE,     -- Only unique email
    `phone` VARCHAR(20) NOT NULL,             -- Not unique (shared numbers allowed)
    `address` TEXT NOT NULL,                  -- Default delivery address
    `password` VARCHAR(255) NOT NULL,
    `level` ENUM('1', '2') DEFAULT '2' COMMENT '1=admin, 2=user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `food_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `image` VARCHAR(255),
    `category` VARCHAR(50),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `cart` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `food_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`food_id`) REFERENCES `food_items`(`id`),
    UNIQUE (`user_id`, `food_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--4. ORDERS TABLE (completed purchases)
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending','preparing','ready','delivered','cancelled') DEFAULT 'pending',
    `payment_status` ENUM('unpaid', 'paid', 'failed') DEFAULT 'unpaid',
    `payment_method` VARCHAR(20) DEFAULT NULL,
    `payment_id` VARCHAR(100) DEFAULT NULL COMMENT 'Khalti transaction ID',
    `payment_date` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE orders
ADD COLUMN order_reference VARCHAR(100) NOT NULL AFTER user_id;


order_items table 
Columns: id, order_id, food_id, quantity, price.


DESCRIBE order_items;
id	int(11)	NO	PRI	NULL	auto_increment	
order_id	int(11)	NO	MUL	NULL		
food_id	int(11)	NO	MUL	NULL		
quantity	int(11)	NO		1		
price	decimal(10,2)	NO		NULL		



