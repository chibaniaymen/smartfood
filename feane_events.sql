-- Database schema for feane_events
CREATE DATABASE IF NOT EXISTS feane_events;
USE feane_events;

-- Locations table
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(500) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    capacity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATETIME NOT NULL,
    location_id INT,
    price DECIMAL(10,2),
    max_attendees INT,
    status ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
);

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE events;
TRUNCATE TABLE locations;
SET FOREIGN_KEY_CHECKS = 1;

-- Sample Data
INSERT INTO locations (id, name, address, city, country, capacity) VALUES
(1, 'Main Hall', '123 Main Street', 'Paris', 'France', 200),
(2, 'Conference Room A', '456 Business Ave', 'Lyon', 'France', 50);

INSERT INTO events (title, description, event_date, location_id, price, max_attendees, status) VALUES
('Tech Conference 2026', 'Future of Tech', '2026-05-20 09:00:00', 1, 150.00, 200, 'active'),
('PHP Workshop', 'Advanced PHP OOP', '2026-06-15 14:00:00', 2, 75.00, 30, 'active');
