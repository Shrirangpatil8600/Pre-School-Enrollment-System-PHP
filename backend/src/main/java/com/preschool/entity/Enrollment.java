package com.preschool.entity;

public class Enrollment {
    private int id;
    private int studentId;
    private int programId;
    private String enrollmentDate;
    private String status;
    
    // Joint fields to make UI display easy
    private String studentName;
    private String programName;
    private double fee;

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public int getStudentId() { return studentId; }
    public void setStudentId(int studentId) { this.studentId = studentId; }

    public int getProgramId() { return programId; }
    public void setProgramId(int programId) { this.programId = programId; }

    public String getEnrollmentDate() { return enrollmentDate; }
    public void setEnrollmentDate(String enrollmentDate) { this.enrollmentDate = enrollmentDate; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public String getStudentName() { return studentName; }
    public void setStudentName(String studentName) { this.studentName = studentName; }

    public String getProgramName() { return programName; }
    public void setProgramName(String programName) { this.programName = programName; }

    public double getFee() { return fee; }
    public void setFee(double fee) { this.fee = fee; }
}
