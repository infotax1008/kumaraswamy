CREATE DATABASE IF NOT EXISTS kumaraswamy_tax CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kumaraswamy_tax;

CREATE TABLE clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    mobile VARCHAR(30) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('client', 'admin') NOT NULL DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    uploaded_by ENUM('client', 'admin') NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_documents_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Default admin login:
-- Email: admin@kumaraswamytax.local
-- Password: Admin@12345
-- The application repairs this starter hash with PHP password_hash() on first run.
INSERT INTO clients (name, mobile, email, password_hash, role)
VALUES (
    'Kumaraswamy Admin',
    '+91 9494990637',
    'admin@kumaraswamytax.local',
    '$2y$10$sKlm/lvwwsS2RXbz9x6f.uWk0jvrlwR.6nmlZE1Er61KJ7YMfT1cq',
    'admin'
);
