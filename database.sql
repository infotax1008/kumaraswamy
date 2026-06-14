CREATE DATABASE IF NOT EXISTS kumaraswamy_tax_portal
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE kumaraswamy_tax_portal;

CREATE TABLE clients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  mobile VARCHAR(30) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('client', 'admin') NOT NULL DEFAULT 'client',
  remember_token_hash CHAR(64) NULL,
  reset_token_hash CHAR(64) NULL,
  reset_token_expires_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_clients_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE client_files (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_id INT UNSIGNED NOT NULL,
  uploaded_by ENUM('client', 'admin') NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  stored_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  file_size INT UNSIGNED NOT NULL,
  mime_type VARCHAR(120) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_client_files_client
    FOREIGN KEY (client_id) REFERENCES clients(id)
    ON DELETE CASCADE,
  INDEX idx_client_files_client (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_id INT UNSIGNED NULL,
  title VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notifications_client
    FOREIGN KEY (client_id) REFERENCES clients(id)
    ON DELETE CASCADE,
  INDEX idx_notifications_client (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Starter admin account:
-- Email: admin@ktc.local
-- Password: password
-- Change this password immediately after first login.
INSERT INTO clients (name, mobile, email, password_hash, role)
VALUES (
  'E. Kumaraswamy',
  '+91 9494990637',
  'admin@ktc.local',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi',
  'admin'
)
ON DUPLICATE KEY UPDATE email = email;

-- Installation:
-- 1. Create a MySQL database by importing this file in phpMyAdmin or with:
--    mysql -u root -p < database.sql
-- 2. Edit config.php and set DB_HOST, DB_NAME, DB_USER and DB_PASS.
-- 3. Upload these PHP files to a PHP-enabled hosting account.
-- 4. Make sure the web server can write to the uploads/ folder. The app creates client folders automatically.
-- 5. Open index.php in the browser.
-- 6. Login as admin@ktc.local / password, then change the password from Admin Panel.
