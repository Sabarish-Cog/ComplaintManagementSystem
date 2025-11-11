-- Test database setup for complaint_db_test
-- Run this SQL to create a test database

CREATE DATABASE IF NOT EXISTS complaint_db_test;
USE complaint_db_test;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Complaints table
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert test users
INSERT INTO users (name, email, password) VALUES 
('Test User 1', 'test1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password
('Test User 2', 'test2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password
('Test User 3', 'test3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- Insert test complaints
INSERT INTO complaints (user_id, title, description) VALUES 
(1, 'Test Complaint 1 by User 1', 'This is a test complaint created by user 1'),
(2, 'Test Complaint 2 by User 2', 'This is a test complaint created by user 2'),
(1, 'Another Complaint by User 1', 'User 1 has multiple complaints'),
(3, 'Test Complaint by User 3', 'This is a test complaint created by user 3');
