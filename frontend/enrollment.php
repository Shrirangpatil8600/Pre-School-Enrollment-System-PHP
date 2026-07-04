<?php
require_once 'api_helper.php';

$message = '';
$message_class = '';

// Handle POST Form Submission to enroll student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enroll_data = [
        'studentId' => (int)($_POST['student_id'] ?? 0),
        'programId' => (int)($_POST['program_id'] ?? 0),
        'enrollmentDate' => $_POST['enrollment_date'] ?? date('Y-m-d'),
        'status' => 'Active'
    ];

    $response = call_api('POST', '/enroll', $enroll_data);
    if ($response['status'] == 201) {
        $message = "Student enrolled successfully!";
        $message_class = "alert-success";
    } else {
        $error = $response['data']['error'] ?? 'API connection failure.';
        $message = "Failed to complete enrollment: " . $error;
        $message_class = "alert-danger";
    }
}

// Fetch all enrollments
$enroll_response = call_api('GET', '/enroll');
$enrollments = [];
if ($enroll_response['status'] == 200 && is_array($enroll_response['data'])) {
    $enrollments = $enroll_response['data'];
}

// Fetch students for dropdown
$student_response = call_api('GET', '/students');
$students = [];
if ($student_response['status'] == 200 && is_array($student_response['data'])) {
    $students = $student_response['data'];
}

// Fetch programs for dropdown
$program_response = call_api('GET', '/programs');
$programs = [];
if ($program_response['status'] == 200 && is_array($program_response['data'])) {
    $programs = $program_response['data'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollments - Pre-School Enrollment System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { background-color: #2c3e50; min-height: 100vh; }
        .sidebar a { color: #ecf0f1; text-decoration: none; padding: 12px 15px; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #34495e; border-left: 4px solid #3498db; }
        .card-form { border-radius: 12px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-2 sidebar p-0 d-flex flex-column">
            <div class="p-3 text-center text-white border-bottom border-secondary">
                <i class="fa-solid fa-graduation-cap fa-2x mb-2 text-warning"></i>
                <h5 class="m-0"><?php echo SCHOOL_NAME; ?></h5>
            </div>
            <div class="flex-grow-1 py-3">
                <a href="index.php"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
                <a href="students.php"><i class="fa-solid fa-child me-2"></i> Students</a>
                <a href="enrollment.php" class="active"><i class="fa-solid fa-file-signature me-2"></i> Enrollments</a>
                <a href="payments.php"><i class="fa-solid fa-indian-rupee-sign me-2"></i> Fee Payments</a>
                <a href="attendance.php"><i class="fa-solid fa-calendar-check me-2"></i> Attendance</a>
                <a href="notices.php"><i class="fa-solid fa-bullhorn me-2"></i> Notice Board</a>
            </div>
            <div class="p-3 text-center text-white-50 border-top border-secondary">
                <small>PHP & Java Hybrid System</small>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            <h2 class="mb-4">Enrollment Management</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Enrollment Form Card -->
                <div class="col-md-4">
                    <div class="card card-form border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-file-signature me-2 text-primary"></i>New Enrollment</h5>
                        <form action="enrollment.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Select Student</label>
                                <select name="student_id" class="form-select" required>
                                    <option value="" disabled selected>Select Student</option>
                                    <?php foreach ($students as $s): ?>
                                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?> (ID: #<?php echo $s['id']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Program / Grade</label>
                                <select name="program_id" class="form-select" required>
                                    <option value="" disabled selected>Select Grade</option>
                                    <?php foreach ($programs as $p): ?>
                                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?> - ₹<?php echo number_format($p['fee'], 2); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Enrollment Date</label>
                                <input type="date" name="enrollment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Enroll Child</button>
                        </form>
                    </div>

                    <!-- Available Program Display Card -->
                    <div class="card card-form border-0 shadow-sm p-4 bg-white mt-4">
                        <h6 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-tags me-2 text-warning"></i>Fee Structures</h6>
                        <ul class="list-group list-group-flush small">
                            <?php foreach ($programs as $p): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <?php echo htmlspecialchars($p['name']); ?>
                                    <span class="badge bg-light text-dark border">₹<?php echo number_format($p['fee'], 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Enrollments List Card -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-list-check me-2 text-success"></i>Enrollment Records</h5>
                        <?php if (empty($enrollments)): ?>
                            <p class="text-muted">No enrollment records found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Enroll ID</th>
                                            <th>Student Name</th>
                                            <th>Program / Grade</th>
                                            <th>Base Tuition Fee</th>
                                            <th>Enrollment Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrollments as $e): ?>
                                            <tr>
                                                <td><strong>#<?php echo $e['id']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($e['studentName']); ?></td>
                                                <td><span class="badge bg-info text-white"><?php echo htmlspecialchars($e['programName']); ?></span></td>
                                                <td>₹<?php echo number_format($e['fee'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($e['enrollmentDate']); ?></td>
                                                <td>
                                                    <span class="badge bg-success"><?php echo htmlspecialchars($e['status']); ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
