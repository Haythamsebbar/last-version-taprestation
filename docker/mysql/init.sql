-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS taprestation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges to the user
GRANT ALL PRIVILEGES ON taprestation.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;