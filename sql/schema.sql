-- =====================================================
-- Grand TAR ABC Student Hostel Booking System
-- MySQL Schema for AWS / Local
-- =====================================================

CREATE DATABASE IF NOT EXISTS grand_tar_abc
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE grand_tar_abc;

-- -----------------------------------------------------
-- Users (Students + Admins)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    student_id VARCHAR(30),
    role ENUM('admin', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Rooms (Hostel Accommodation)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    room_type ENUM('single', 'twin', '4-bed', '6-bed') NOT NULL DEFAULT 'twin',
    capacity INT NOT NULL DEFAULT 2,
    price_per_month DECIMAL(10,2) NOT NULL,
    size_sqm INT DEFAULT 20,
    bed_type VARCHAR(50) DEFAULT 'Single Bed',
    facilities TEXT,
    image_url VARCHAR(255),
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Bookings (Student Hostel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) NOT NULL UNIQUE,
    room_id INT NOT NULL,
    user_id INT NULL,
    student_name VARCHAR(100) NOT NULL,
    student_email VARCHAR(100) NOT NULL,
    student_phone VARCHAR(30),
    student_id_number VARCHAR(30),
    move_in DATE NOT NULL,
    move_out DATE NOT NULL,
    number_of_students INT DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_dates (move_in, move_out),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Facilities (Student Hostel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    image_url VARCHAR(255)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Contact Messages
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Sample Data
-- -----------------------------------------------------

-- Admin user (password: admin123)
-- Hash generated with password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@grandtarabc.edu.my', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hostel Administrator', 'admin');

-- Sample student (password: student123)
INSERT INTO users (username, email, password, full_name, phone, student_id, role) VALUES
('ali.ahmad', 'ali.ahmad@student.edu.my', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ali Ahmad', '012-3456789', 'TAR2024001', 'student');

-- Rooms (Student Hostel Accommodation)
INSERT INTO rooms (room_number, name, slug, description, room_type, capacity, price_per_month, size_sqm, bed_type, facilities, image_url) VALUES
('S101', 'Single Room', 'single-room',
 'Private single room with study desk, wardrobe and personal space. Ideal for students who prefer privacy and focus while studying.',
 'single', 1, 450.00, 12, '1 Single Bed', 'Free Wi-Fi, Study Desk, Wardrobe, Air Conditioning, Shared Bathroom',
 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800'),

('T201', 'Twin Sharing Room', 'twin-sharing',
 'Comfortable shared room with two single beds, individual study desks and storage space. Suitable for two students looking for affordable accommodation.',
 'twin', 2, 320.00, 18, '2 Single Beds', 'Free Wi-Fi, Study Desks, Wardrobes, Air Conditioning, Shared Bathroom, Lockers',
 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800'),

('F301', '4-Bed Shared Room', '4-bed-shared',
 'Affordable shared accommodation with four single beds, individual lockers and study facilities. Popular choice among students seeking value.',
 '4-bed', 4, 250.00, 25, '4 Single Beds', 'Free Wi-Fi, Study Desks, Lockers, Air Conditioning, Shared Bathroom',
 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?w=800'),

('D401', '6-Bed Dormitory', '6-bed-dormitory',
 'Spacious and budget-friendly dormitory suitable for students looking for the most affordable hostel living experience with essential facilities.',
 '6-bed', 6, 180.00, 32, '6 Single Beds', 'Free Wi-Fi, Lockers, Shared Study Area, Air Conditioning, Shared Bathroom',
 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800'),

('T202', 'Twin Sharing Room (Premium)', 'twin-premium',
 'Upgraded twin sharing room with better furniture, larger study space and quieter environment. Perfect for students who need a productive study setting.',
 'twin', 2, 380.00, 20, '2 Single Beds', 'Free Wi-Fi, Study Desks, Wardrobes, Air Conditioning, Shared Bathroom, Reading Lamp',
 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800');

-- Facilities (Student Hostel)
INSERT INTO facilities (name, description, icon, image_url) VALUES
('High-Speed Wi-Fi',
 'Campus-wide high-speed wireless internet available 24/7 in rooms and common areas to support online learning and research.',
 'fas fa-wifi',
 'https://images.unsplash.com/photo-1544197150-b99a580885a7?w=800'),

('Study Area',
 'Quiet study rooms and common study spaces equipped with tables, chairs and power outlets — ideal for group projects and exam preparation.',
 'fas fa-book-open',
 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800'),

('Laundry Room',
 'Self-service laundry facilities with washing machines and dryers available for all residents at affordable rates.',
 'fas fa-tshirt',
 'https://images.unsplash.com/photo-1517677208171-0bc6725a3e60?w=800'),

('Pantry / Kitchen',
 'Shared pantry with refrigerators, microwave ovens and basic cooking facilities so students can prepare simple meals.',
 'fas fa-utensils',
 'https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800'),

('24/7 Security',
 'Round-the-clock security with CCTV coverage, controlled access and on-site wardens to ensure a safe living environment.',
 'fas fa-shield-alt',
 'https://images.unsplash.com/photo-1557597774-9d273605dfa9?w=800'),

('Recreation Area',
 'Common recreation space with TV, seating and indoor games for students to relax and socialise after classes.',
 'fas fa-gamepad',
 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800');
