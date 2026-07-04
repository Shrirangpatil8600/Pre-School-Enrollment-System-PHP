package com.preschool;

import com.google.gson.Gson;
import com.preschool.entity.*;
import com.preschool.utility.DBUtil;
import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpHandler;
import com.sun.net.httpserver.HttpServer;

import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.InetSocketAddress;
import java.nio.charset.StandardCharsets;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class BackendServer {

    private static final Gson gson = new Gson();

    public static void main(String[] args) {
        try {
            // Verify DB Connection
            Connection conn = DBUtil.getConnection();
            if (conn != null) {
                System.out.println("Database connection established successfully.");
            } else {
                System.err.println("CRITICAL: Failed to connect to MySQL database.");
            }

            // Create HTTP Server on port 8085
            HttpServer server = HttpServer.create(new InetSocketAddress(8085), 0);
            System.out.println("Java Backend REST API Server is starting on port 8085...");

            // Endpoints
            server.createContext("/api/students", new StudentsHandler());
            server.createContext("/api/programs", new ProgramsHandler());
            server.createContext("/api/enroll", new EnrollmentsHandler());
            server.createContext("/api/payments", new PaymentsHandler());
            server.createContext("/api/attendance", new AttendanceHandler());
            server.createContext("/api/notices", new NoticesHandler());
            server.createContext("/api/dashboard", new DashboardHandler());

            server.setExecutor(null); // default executor
            server.start();
            System.out.println("Server is running. Press Ctrl+C to terminate.");

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // Helper to send JSON Response
    private static void sendResponse(HttpExchange exchange, int statusCode, String responseText) throws IOException {
        exchange.getResponseHeaders().add("Content-Type", "application/json");
        exchange.getResponseHeaders().add("Access-Control-Allow-Origin", "*");
        exchange.getResponseHeaders().add("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
        exchange.getResponseHeaders().add("Access-Control-Allow-Headers", "Content-Type");

        byte[] bytes = responseText.getBytes(StandardCharsets.UTF_8);
        exchange.sendResponseHeaders(statusCode, bytes.length);
        try (OutputStream os = exchange.getResponseBody()) {
            os.write(bytes);
        }
    }

    // Helper to handle CORS Options Preflight
    private static boolean handleOptions(HttpExchange exchange) throws IOException {
        if ("OPTIONS".equalsIgnoreCase(exchange.getRequestMethod())) {
            exchange.getResponseHeaders().add("Access-Control-Allow-Origin", "*");
            exchange.getResponseHeaders().add("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
            exchange.getResponseHeaders().add("Access-Control-Allow-Headers", "Content-Type");
            exchange.sendResponseHeaders(200, -1);
            return true;
        }
        return false;
    }

    // 1. /api/students
    static class StudentsHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (handleOptions(exchange)) return;

            String method = exchange.getRequestMethod();
            Connection conn = DBUtil.getConnection();

            if ("GET".equalsIgnoreCase(method)) {
                List<Student> list = new ArrayList<>();
                String sql = "SELECT * FROM students ORDER BY id DESC";
                try (PreparedStatement ps = conn.prepareStatement(sql);
                     ResultSet rs = ps.executeQuery()) {
                    while (rs.next()) {
                        Student s = new Student();
                        s.setId(rs.getInt("id"));
                        s.setName(rs.getString("name"));
                        s.setAge(rs.getInt("age"));
                        s.setDob(rs.getString("dob"));
                        s.setGender(rs.getString("gender"));
                        s.setGuardianName(rs.getString("guardian_name"));
                        s.setContact(rs.getString("contact"));
                        s.setAddress(rs.getString("address"));
                        list.add(s);
                    }
                    sendResponse(exchange, 200, gson.toJson(list));
                } catch (Exception e) {
                    e.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + e.getMessage() + "\"}");
                }
            } else if ("POST".equalsIgnoreCase(method)) {
                try {
                    Student s = gson.fromJson(new InputStreamReader(exchange.getRequestBody(), StandardCharsets.UTF_8), Student.class);
                    String sql = "INSERT INTO students (name, age, dob, gender, guardian_name, contact, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    try (PreparedStatement ps = conn.prepareStatement(sql)) {
                        ps.setString(1, s.getName());
                        ps.setInt(2, s.getAge());
                        ps.setString(3, s.getDob());
                        ps.setString(4, s.getGender());
                        ps.setString(5, s.getGuardianName());
                        ps.setString(6, s.getContact());
                        ps.setString(7, s.getAddress());
                        int affected = ps.executeUpdate();
                        if (affected > 0) {
                            sendResponse(exchange, 201, "{\"message\":\"Student registered successfully\"}");
                        } else {
                            sendResponse(exchange, 400, "{\"error\":\"Unable to register student\"}");
                        }
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + e.getMessage() + "\"}");
                }
            }
        }
    }

    // 2. /api/programs
    static class ProgramsHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (handleOptions(exchange)) return;

            String method = exchange.getRequestMethod();
            Connection conn = DBUtil.getConnection();

            if ("GET".equalsIgnoreCase(method)) {
                List<Program> list = new ArrayList<>();
                String sql = "SELECT * FROM programs ORDER BY id ASC";
                try (PreparedStatement ps = conn.prepareStatement(sql);
                     ResultSet rs = ps.executeQuery()) {
                    while (rs.next()) {
                        Program p = new Program();
                        p.setId(rs.getInt("id"));
                        p.setName(rs.getString("name"));
                        p.setFee(rs.getDouble("fee"));
                        list.add(p);
                    }
                    sendResponse(exchange, 200, gson.toJson(list));
                } catch (Exception e) {
                    e.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + e.getMessage() + "\"}");
                }
            }
        }
    }

    // 3. /api/enroll
    static class EnrollmentsHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (handleOptions(exchange)) return;

            String method = exchange.getRequestMethod();
            Connection conn = DBUtil.getConnection();

            if ("GET".equalsIgnoreCase(method)) {
                List<Enrollment> list = new ArrayList<>();
                String sql = "SELECT e.*, s.name as studentName, p.name as programName, p.fee FROM enrollments e " +
                             "JOIN students s ON e.student_id = s.id " +
                             "JOIN programs p ON e.program_id = p.id ORDER BY e.id DESC";
                try (PreparedStatement ps = conn.prepareStatement(sql);
                     ResultSet rs = ps.executeQuery()) {
                    while (rs.next()) {
                        Enrollment e = new Enrollment();
                        e.setId(rs.getInt("id"));
                        e.setStudentId(rs.getInt("student_id"));
                        e.setProgramId(rs.getInt("program_id"));
                        e.setEnrollmentDate(rs.getString("enrollment_date"));
                        e.setStatus(rs.getString("status"));
                        e.setStudentName(rs.getString("studentName"));
                        e.setProgramName(rs.getString("programName"));
                        e.setFee(rs.getDouble("fee"));
                        list.add(e);
                    }
                    sendResponse(exchange, 200, gson.toJson(list));
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            } else if ("POST".equalsIgnoreCase(method)) {
                try {
                    Enrollment e = gson.fromJson(new InputStreamReader(exchange.getRequestBody(), StandardCharsets.UTF_8), Enrollment.class);
                    String sql = "INSERT INTO enrollments (student_id, program_id, enrollment_date, status) VALUES (?, ?, ?, ?)";
                    try (PreparedStatement ps = conn.prepareStatement(sql)) {
                        ps.setInt(1, e.getStudentId());
                        ps.setInt(2, e.getProgramId());
                        ps.setString(3, e.getEnrollmentDate());
                        ps.setString(4, e.getStatus() == null ? "Active" : e.getStatus());
                        int affected = ps.executeUpdate();
                        if (affected > 0) {
                            sendResponse(exchange, 201, "{\"message\":\"Enrollment completed successfully\"}");
                        } else {
                            sendResponse(exchange, 400, "{\"error\":\"Unable to complete enrollment\"}");
                        }
                    }
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            }
        }
    }

    // 4. /api/payments
    static class PaymentsHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (handleOptions(exchange)) return;

            String method = exchange.getRequestMethod();
            Connection conn = DBUtil.getConnection();

            if ("GET".equalsIgnoreCase(method)) {
                List<Payment> list = new ArrayList<>();
                String sql = "SELECT p.*, s.name as studentName FROM payments p " +
                             "JOIN students s ON p.student_id = s.id ORDER BY p.id DESC";
                try (PreparedStatement ps = conn.prepareStatement(sql);
                     ResultSet rs = ps.executeQuery()) {
                    while (rs.next()) {
                        Payment p = new Payment();
                        p.setId(rs.getInt("id"));
                        p.setStudentId(rs.getInt("student_id"));
                        p.setAmountPaid(rs.getDouble("amount_paid"));
                        p.setPaymentDate(rs.getString("payment_date"));
                        p.setPaymentMethod(rs.getString("payment_method"));
                        p.setStudentName(rs.getString("studentName"));
                        list.add(p);
                    }
                    sendResponse(exchange, 200, gson.toJson(list));
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            } else if ("POST".equalsIgnoreCase(method)) {
                try {
                    Payment p = gson.fromJson(new InputStreamReader(exchange.getRequestBody(), StandardCharsets.UTF_8), Payment.class);
                    String sql = "INSERT INTO payments (student_id, amount_paid, payment_date, payment_method) VALUES (?, ?, ?, ?)";
                    try (PreparedStatement ps = conn.prepareStatement(sql)) {
                        ps.setInt(1, p.getStudentId());
                        ps.setDouble(2, p.getAmountPaid());
                        ps.setString(3, p.getPaymentDate());
                        ps.setString(4, p.getPaymentMethod());
                        int affected = ps.executeUpdate();
                        if (affected > 0) {
                            sendResponse(exchange, 201, "{\"message\":\"Payment logged successfully\"}");
                        } else {
                            sendResponse(exchange, 400, "{\"error\":\"Unable to log payment\"}");
                        }
                    }
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            }
        }
    }

    // 5. /api/attendance
    static class AttendanceHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (handleOptions(exchange)) return;

            String method = exchange.getRequestMethod();
            Connection conn = DBUtil.getConnection();

            if ("GET".equalsIgnoreCase(method)) {
                List<Attendance> list = new ArrayList<>();
                // If a date is passed as query parameter, filter by it. Else return all logs.
                String query = exchange.getRequestURI().getQuery();
                String filterDate = null;
                if (query != null && query.contains("date=")) {
                    filterDate = query.split("date=")[1].split("&")[0];
                }

                String sql;
                if (filterDate != null) {
                    sql = "SELECT a.*, s.name as studentName FROM attendance a " +
                          "JOIN students s ON a.student_id = s.id WHERE a.date = ? ORDER BY s.name ASC";
                } else {
                    sql = "SELECT a.*, s.name as studentName FROM attendance a " +
                          "JOIN students s ON a.student_id = s.id ORDER BY a.date DESC, s.name ASC";
                }

                try (PreparedStatement ps = conn.prepareStatement(sql)) {
                    if (filterDate != null) {
                        ps.setString(1, filterDate);
                    }
                    try (ResultSet rs = ps.executeQuery()) {
                        while (rs.next()) {
                            Attendance a = new Attendance();
                            a.setId(rs.getInt("id"));
                            a.setStudentId(rs.getInt("student_id"));
                            a.setDate(rs.getString("date"));
                            a.setStatus(rs.getString("status"));
                            a.setStudentName(rs.getString("studentName"));
                            list.add(a);
                        }
                    }
                    sendResponse(exchange, 200, gson.toJson(list));
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            } else if ("POST".equalsIgnoreCase(method)) {
                try {
                    // Expecting a JSON array of attendance logs
                    Attendance[] logs = gson.fromJson(new InputStreamReader(exchange.getRequestBody(), StandardCharsets.UTF_8), Attendance[].class);
                    String sql = "INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?) " +
                                 "ON DUPLICATE KEY UPDATE status = VALUES(status)";
                    int count = 0;
                    try (PreparedStatement ps = conn.prepareStatement(sql)) {
                        for (Attendance a : logs) {
                            ps.setInt(1, a.getStudentId());
                            ps.setString(2, a.getDate());
                            ps.setString(3, a.getStatus());
                            ps.addBatch();
                        }
                        int[] results = ps.executeBatch();
                        count = results.length;
                    }
                    sendResponse(exchange, 200, "{\"message\":\"Attendance logs updated successfully\", \"count\":" + count + "}");
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            }
        }
    }

    // 6. /api/notices
    static class NoticesHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (handleOptions(exchange)) return;

            String method = exchange.getRequestMethod();
            Connection conn = DBUtil.getConnection();

            if ("GET".equalsIgnoreCase(method)) {
                List<Notice> list = new ArrayList<>();
                String sql = "SELECT * FROM notices ORDER BY id DESC";
                try (PreparedStatement ps = conn.prepareStatement(sql);
                     ResultSet rs = ps.executeQuery()) {
                    while (rs.next()) {
                        Notice n = new Notice();
                        n.setId(rs.getInt("id"));
                        n.setTitle(rs.getString("title"));
                        n.setContent(rs.getString("content"));
                        n.setCreatedAt(rs.getString("created_at"));
                        list.add(n);
                    }
                    sendResponse(exchange, 200, gson.toJson(list));
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            } else if ("POST".equalsIgnoreCase(method)) {
                try {
                    Notice n = gson.fromJson(new InputStreamReader(exchange.getRequestBody(), StandardCharsets.UTF_8), Notice.class);
                    String sql = "INSERT INTO notices (title, content) VALUES (?, ?)";
                    try (PreparedStatement ps = conn.prepareStatement(sql)) {
                        ps.setString(1, n.getTitle());
                        ps.setString(2, n.getContent());
                        int affected = ps.executeUpdate();
                        if (affected > 0) {
                            sendResponse(exchange, 201, "{\"message\":\"Notice posted successfully\"}");
                        } else {
                            sendResponse(exchange, 400, "{\"error\":\"Unable to post notice\"}");
                        }
                    }
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            }
        }
    }

    // 7. /api/dashboard
    static class DashboardHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            if (handleOptions(exchange)) return;

            String method = exchange.getRequestMethod();
            Connection conn = DBUtil.getConnection();

            if ("GET".equalsIgnoreCase(method)) {
                Map<String, Object> stats = new HashMap<>();
                try {
                    // Total Students
                    try (PreparedStatement ps = conn.prepareStatement("SELECT COUNT(*) FROM students");
                         ResultSet rs = ps.executeQuery()) {
                        if (rs.next()) stats.put("totalStudents", rs.getInt(1));
                    }
                    // Total Enrollments
                    try (PreparedStatement ps = conn.prepareStatement("SELECT COUNT(*) FROM enrollments");
                         ResultSet rs = ps.executeQuery()) {
                        if (rs.next()) stats.put("totalEnrollments", rs.getInt(1));
                    }
                    // Total Revenue Collected
                    try (PreparedStatement ps = conn.prepareStatement("SELECT SUM(amount_paid) FROM payments");
                         ResultSet rs = ps.executeQuery()) {
                        if (rs.next()) stats.put("totalPayments", rs.getDouble(1));
                    }
                    // Total Active Notices
                    try (PreparedStatement ps = conn.prepareStatement("SELECT COUNT(*) FROM notices");
                         ResultSet rs = ps.executeQuery()) {
                        if (rs.next()) stats.put("totalNotices", rs.getInt(1));
                    }

                    sendResponse(exchange, 200, gson.toJson(stats));
                } catch (Exception ex) {
                    ex.printStackTrace();
                    sendResponse(exchange, 500, "{\"error\":\"" + ex.getMessage() + "\"}");
                }
            }
        }
    }
}
