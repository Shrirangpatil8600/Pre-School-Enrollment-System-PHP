# Pre-School Enrollment System

A decoupled full-stack hybrid web application designed to digitize and manage preschool operations, including registrations, program enrollments, billing payments, attendance logs, and notices.

## Technical Stack
*   **Frontend Client:** PHP, cURL, HTML5, CSS3, Bootstrap 5, FontAwesome
*   **Backend REST APIs:** Java 11, JDK HttpServer, Gson, JDBC, Maven
*   **Database:** MySQL

## System Features
*   **Student Directory:** Full CRUD module to register and manage child profile details.
*   **Program Enrollments:** Manage active grade assignments (Playgroup, Nursery, LKG, UKG) and fees.
*   **Billing Ledger:** Log and audit tuition fee payments (Cash, Cards, UPI, Cheques).
*   **Daily Attendance Marker:** Calendar roll tracker with optimized SQL Prepared Statement batch updates.
*   **Notice Board:** Renders announcements instantly across the system admin dashboard.
*   **Decoupled Microservice Design:** Standalone Java server exposes reusable REST endpoints, making it easily adaptable for web or mobile frontends.

---

## How to Set Up and Run

For complete setup guides, step-by-step instructions, schema diagrams, and developer Q&A, please refer to the **[PROJECT_GUIDE.md](PROJECT_GUIDE.md)** file.

### 1. Database Setup
1. Create a MySQL database named `preschool_db`.
2. Import and run the `database.sql` script.

### 2. Run Java REST Backend
1. Configure credentials in `backend/src/main/resources/application.properties`.
2. Build the project:
   ```bash
   mvn clean install
   ```
3. Start the standalone JDK HttpServer on port `8085`:
   ```bash
   mvn exec:java
   ```

### 3. Run PHP Web Frontend
1. Deploy the `frontend/` folder to your local Apache server root directory (e.g. XAMPP's `htdocs/`).
2. Ensure cURL is enabled in `php.ini`.
3. Open `http://localhost/preschool/index.php` in your browser.

---

## Developer
- **Created by:** Shrirang Patil
- **GitHub:** [Shrirangpatil8600](https://github.com/Shrirangpatil8600)
