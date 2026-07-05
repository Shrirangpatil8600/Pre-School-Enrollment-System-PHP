# Project Guide: Pre-School Enrollment System (PHP & Java Hybrid Combo)

Welcome! This document is a comprehensive guide to understanding, setting up, and deploying the **Pre-School Enrollment System** project. This is a hybrid web application that demonstrates a decoupled full-stack architecture by integration of a **PHP frontend client** and a **Java REST API backend microservice**.

---

## 1. Project Overview & Architecture

The **Pre-School Enrollment System** is designed to digitize and streamline registrations, enrollment programs, fee records, attendance cards, faculty directories, timetables, and notices for preschools.

Instead of a monolithic layout, the project is structured as a **decoupled Client-Server application**:
1.  **Frontend Client (PHP)**: Responsible only for the User Interface. It runs on a local web server (Apache/XAMPP), styled with Bootstrap 5, and queries the backend via JSON over HTTP.
2.  **REST API Service (Java)**: A Maven-based microservice that runs a standalone HTTP server. It processes incoming JSON requests, executes business logic, maps data to Java entity models, and coordinates with MySQL.
3.  **Database (MySQL)**: Stores relational data of students, enrollments, notices, payments, attendance, teachers, and schedules.

```text
    +-----------------------+
    |      Browser UI       |
    +-----------+-----------+
                | HTTP
                v
    +-----------------------+
    |   PHP Web Client      |  (Runs on Apache - e.g. Port 80 / 8000)
    |  (api_helper via cURL)|
    +-----------+-----------+
                | REST JSON HTTP Calls
                v
    +-----------------------+
    |   Java REST API Srv   |  (Runs as Standalone - Port 8085)
    |  (com.sun.net.Http)   |
    +-----------+-----------+
                | JDBC
                v
    +-----------------------+
    |     MySQL Server      |  (Database: preschool_db)
    +-----------------------+
```

---

## 2. Seven Integrated System Modules

1.  **Student Management (`students.php`)**: Form to register student info (Name, DOB, Age, Gender, Guardian Name, Contact, Address) and a database directory list of all students.
2.  **Program & Enrollment (`enrollment.php`)**: Grade/program listings (Playgroup, Nursery, LKG, UKG) and student assignment processing, maintaining database constraints.
3.  **Tuition Fee Payments (`payments.php`)**: Logs tuition fee payments (cash, card, online, cheque), displaying a transaction ledger.
4.  **Attendance Tracker (`attendance.php`)**: A daily roll register allowing admins to mark students 'Present' or 'Absent' on any chosen calendar date using SQL batch updates.
5.  **Teacher/Faculty Management (`teachers.php`)**: Form to register faculty members (Name, Email, Contact, Specialization) and displays a list of all active staff.
6.  **Class Schedules / Timetable (`schedules.php`)**: Links programs and teachers together to construct class schedules (Room details, Day of week, Time slots) for parent/staff access.
7.  **Notice Board (`notices.php` & `index.php`)**: Announcement board to post notifications, which automatically feed into the dashboard.

---

## 3. Directory & File Structure

Here is a guide to the project layout and the role of key files:

```text
Pre-School-Enrollment-System-PHP/
├── database.sql                    # MySQL script containing DDL schemas and seed data
├── PROJECT_GUIDE.md                # System architectural documentation
├── backend/                        # Java REST API Service (Maven Project)
│   ├── pom.xml                     # Maven dependencies (Gson, MySQL connector)
│   └── src/
│       ├── main/
│       │   ├── resources/
│       │   │   └── application.properties # Stores MySQL connection URL & credentials
│       │   └── java/com/preschool/
│       │       ├── BackendServer.java # Standalone HttpServer router & endpoint handlers
│       │       ├── utility/
│       │       │   └── DBUtil.java # Singleton JDBC connector class
│       │       └── entity/         # Data Models (POJOs)
│       │           ├── Student.java, Program.java, Enrollment.java, Payment.java, Attendance.java, Notice.java, Teacher.java, Schedule.java
└── frontend/                       # PHP Front-end Portal Client
    ├── config.php                  # Stores API endpoints and school settings
    ├── api_helper.php              # Shared library managing HTTP cURL queries
    ├── index.php                   # Dashboard main page (aggregates statistics)
    ├── students.php                # Student profiles register
    ├── enrollment.php              # Program enrollment register
    ├── payments.php                # Billing fee ledger register
    ├── attendance.php              # Attendance roll register
    ├── teachers.php                # Faculty database register
    ├── schedules.php               # Timetable class scheduler
    └── notices.php                 # notice publisher
```

---

## 4. Database Schema & Tables

The MySQL database schema (`preschool_db`) handles all data tracking with relational integrity:

```sql
CREATE DATABASE preschool_db;
USE preschool_db;

-- 1. Students Table
CREATE TABLE students (
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
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    fee DOUBLE(10,2) NOT NULL
);

-- 3. Teachers Table (New Module 6)
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact VARCHAR(20) NOT NULL,
    specialization VARCHAR(100) NOT NULL
);

-- 4. Enrollments Table
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    program_id INT NOT NULL,
    enrollment_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
);

-- 5. Payments Table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    amount_paid DOUBLE(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method VARCHAR(30) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 6. Attendance Table (Composite Unique Key prevents duplicate daily roll marks)
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(15) NOT NULL,
    UNIQUE KEY student_date (student_id, date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 7. Notices Table
CREATE TABLE notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. Class Schedules Table (New Module 7)
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    teacher_id INT NOT NULL,
    day_of_week VARCHAR(20) NOT NULL,
    time_slot VARCHAR(30) NOT NULL,
    room_no VARCHAR(20) NOT NULL,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);
```

---

## 5. How to Set Up the Project

Follow these steps to deploy and run both components locally.

### Step 1: Set Up MySQL Database
1.  Open your MySQL terminal or GUI manager (e.g. phpMyAdmin, Workbench).
2.  Import and run the [database.sql](file:///c:/Users/Shrirang%20Patil/Documents/GitHub/Pre-School-Enrollment-System-PHP/database.sql) script.

### Step 2: Configure and Run the Java Backend REST Server
1.  Open [application.properties](file:///c:/Users/Shrirang%20Patil/Documents/GitHub/Pre-School-Enrollment-System-PHP/backend/src/main/resources/application.properties) and update the database password:
    ```properties
    jdbc.password=your_mysql_password
    ```
2.  Open your terminal inside the `backend/` folder and build the Java application:
    ```bash
    mvn clean install
    ```
3.  Launch the standalone Java API Server:
    ```bash
    mvn exec:java
    ```
    You will see the message: `Java Backend REST API Server is starting on port 8085...`

### Step 3: Run the PHP Frontend Client
1.  Copy the `frontend/` folder to your local web server root directory (e.g., `C:/xampp/htdocs/preschool/`).
2.  Ensure cURL is enabled in your `php.ini` configuration.
3.  Start Apache via your XAMPP/Wamp control panel.
4.  Open your browser and navigate to the application: `http://localhost/preschool/index.php`.

---

## 6. Technical FAQ & Deep-Dive

This section provides detailed answers to key technical design and operational questions about the system:

### Q1: Why did you use this hybrid PHP & Java design instead of a standard monolith?
**Answer**:
> "This architecture demonstrates a modern **decoupled microservice architecture**. 
>
> In production, monolithic systems often suffer from scaling bottlenecks. By splitting the PHP presentation layer from the Java business logic:
> 1. We achieve technology diversity: PHP is excellent for fast, lightweight web page rendering, while Java provides robust backend processing, compile-time safety, and multithreading stability.
> 2. We expose a reusable REST API. The Java backend does not care about the client. Tomorrow, we could easily build a mobile app in Android/iOS or a React SPA, and connect it directly to the exact same Java port endpoints without rewriting any backend logic."

### Q2: How does communication work between PHP and Java?
**Answer**:
> "The communication is handled through REST endpoints exchanging JSON. 
> 
> In PHP, [api_helper.php](file:///c:/Users/Shrirang%20Patil/Documents/GitHub/Pre-School-Enrollment-System-PHP/frontend/api_helper.php) uses **cURL** to establish HTTP sockets to the Java server running on `http://localhost:8085/api`. When a form is submitted (e.g., registering a student), PHP serializes the post data into JSON (`json_encode()`) and sends a POST request.
>
> On the Java side, the `BackendServer` reads the JSON payload from the request stream, parses it into a Java object using the **Gson** library, and executes the database insert query. Java then returns a JSON response (like `{"message":"Student registered successfully"}`) along with the appropriate HTTP status code (201 Created), which PHP parses and displays to the user."

### Q3: How is the standalone Java server implemented without using Spring Boot or Apache Tomcat?
**Answer**:
> "To keep the backend lightweight and showcase standard JDK capabilities, the application uses **`com.sun.net.httpserver.HttpServer`** rather than a heavy Tomcat container or Spring Boot framework. 
>
> We bind the server socket to port `8085` and map request context routing paths:
> `server.createContext("/api/students", new StudentsHandler());`
> Each handler implements `HttpHandler` and processes incoming `HttpExchange` objects. This lightweight HTTP stack is extremely fast, uses minimal memory, and makes it easy to run the backend as a standalone service."

### Q4: How did you implement daily attendance roll marks for multiple students in a single request?
**Answer**:
> "Marking attendance for a class requires saving the status of multiple students simultaneously. Doing individual SQL inserts for each student would cause excessive database round-trips.
>
> To optimize this, the system uses **SQL Batching**:
> 1. In PHP (`attendance.php`), we pack all student statuses into a single JSON array and POST it to `/api/attendance`.
> 2. In Java (`BackendServer$AttendanceHandler`), we parse this JSON into an array of `Attendance` objects.
> 3. We use JDBC's batch features:
>    ```java
>    for (Attendance a : logs) {
>        ps.setInt(1, a.getStudentId());
>        ps.setString(2, a.getDate());
>        ps.setString(3, a.getStatus());
>        ps.addBatch();
>    }
>    ps.executeBatch();
>    ```
> This sends all attendance marks to MySQL in a single database round-trip, maximizing execution speed. The SQL statement also uses `ON DUPLICATE KEY UPDATE` to support edits to attendance marks on the same date."

---
*Created by Shrirang Patil.*
