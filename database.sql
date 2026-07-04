-- Create Pre-School Enrollment System Database
CREATE DATABASE IF NOT EXISTS preschool_db;
USE preschool_db;

-- 1. Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    guardian_name VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    address TEXT NOT NULL
);

-- 2. Programs Table
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    fee DOUBLE(10,2) NOT NULL
);

-- Seed default programs
INSERT INTO programs (name, fee) VALUES 
('Playgroup', 15000.00),
('Nursery', 18000.00),
('LKG', 20000.00),
('UKG', 22000.00)
ON DUPLICATE KEY UPDATE fee = VALUES(fee);

-- 3. Teachers Table (New Module 6)
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact VARCHAR(20) NOT NULL,
    specialization VARCHAR(100) NOT NULL
);

-- Seed default teachers
INSERT INTO teachers (name, email, contact, specialization) VALUES
('Priya Deshmukh', 'priya@tenderflow.com', '9812345670', 'Early Child Care & Playgroup'),
('Snehal More', 'snehal@tenderflow.com', '9812345671', 'Nursery Education'),
('Rohan Joshi', 'rohan@tenderflow.com', '9812345672', 'Kindergarten (LKG/UKG)');

-- 4. Enrollments Table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    program_id INT NOT NULL,
    enrollment_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
);

-- 5. Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    amount_paid DOUBLE(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method VARCHAR(30) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 6. Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(15) NOT NULL, -- 'Present' or 'Absent'
    UNIQUE KEY student_date (student_id, date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 7. Notices Table
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. Class Schedules Table (New Module 7)
CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    teacher_id INT NOT NULL,
    day_of_week VARCHAR(20) NOT NULL,
    time_slot VARCHAR(30) NOT NULL,
    room_no VARCHAR(20) NOT NULL,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);

-- Seed default schedules
INSERT INTO schedules (program_id, teacher_id, day_of_week, time_slot, room_no) VALUES
(1, 1, 'Monday', '09:00 AM - 11:30 AM', 'Room A - Playgroup'),
(2, 2, 'Monday', '09:00 AM - 12:00 PM', 'Room B - Nursery'),
(3, 3, 'Tuesday', '08:30 AM - 12:30 PM', 'Room C - LKG'),
(4, 3, 'Tuesday', '08:30 AM - 12:30 PM', 'Room D - UKG');

-- Seed some mock data for initial verification
INSERT INTO students (name, age, dob, gender, guardian_name, contact, address) VALUES
('Aarav Patil', 4, '2022-03-12', 'Male', 'Shrirang Patil', '9876543210', '123, Model Colony, Pune'),
('Ananya Sharma', 5, '2021-07-25', 'Female', 'Rahul Sharma', '9123456789', '45, Shivajinagar, Pune');

INSERT INTO enrollments (student_id, program_id, enrollment_date, status) VALUES
(1, 2, '2026-06-01', 'Active'),
(2, 3, '2026-06-05', 'Active');

INSERT INTO payments (student_id, amount_paid, payment_date, payment_method) VALUES
(1, 5000.00, '2026-06-01', 'Cash'),
(2, 10000.00, '2026-06-05', 'Online');

INSERT INTO attendance (student_id, date, status) VALUES
(1, '2026-07-01', 'Present'),
(2, '2026-07-01', 'Present'),
(1, '2026-07-02', 'Present'),
(2, '2026-07-02', 'Absent');

INSERT INTO notices (title, content) VALUES
('Annual Day Meeting', 'Dear parents, please attend the Annual Day preparation meeting scheduled for this Saturday at 10:00 AM in the school hall.'),
('Monsoon Health Guidelines', 'Please ensure children carry raincoats. Health checkups will be conducted next Wednesday.');
