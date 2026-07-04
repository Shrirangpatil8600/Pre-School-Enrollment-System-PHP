<?php
require_once 'api_helper.php';

$message = '';
$message_class = '';
$selected_date = $_GET['date'] ?? date('Y-m-d');

// Fetch students to list for attendance marking
$student_response = call_api('GET', '/students');
$students = [];
if ($student_response['status'] == 200 && is_array($student_response['data'])) {
    $students = $student_response['data'];
}

// Handle POST to save attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendance_logs = [];
    $statuses = $_POST['status'] ?? []; // Map of studentId => status (Present/Absent)

    foreach ($students as $s) {
        $sid = $s['id'];
        $status = $statuses[$sid] ?? 'Absent'; // Default to Absent if unchecked/unselected
        
        $attendance_logs[] = [
            'studentId' => (int)$sid,
            'date' => $selected_date,
            'status' => $status
        ];
    }

    $response = call_api('POST', '/attendance', $attendance_logs);
    if ($response['status'] == 200) {
        $message = "Attendance marked successfully!";
        $message_class = "alert-success";
    } else {
        $error = $response['data']['error'] ?? 'API connection failure.';
        $message = "Failed to mark attendance: " . $error;
        $message_class = "alert-danger";
    }
}

// Fetch attendance for the selected date to display pre-selected radio checks
$attendance_response = call_api('GET', '/attendance', ['date' => $selected_date]);
$recorded_attendance = [];
if ($attendance_response['status'] == 200 && is_array($attendance_response['data'])) {
    foreach ($attendance_response['data'] as $att) {
        $recorded_attendance[$att['studentId']] = $att['status'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Tracking - Pre-School Enrollment System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { background-color: #2c3e50; min-height: 100vh; }
        .sidebar a { color: #ecf0f1; text-decoration: none; padding: 12px 15px; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #34495e; border-left: 4px solid #3498db; }
        .card-container { border-radius: 12px; }
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
                <a href="enrollment.php"><i class="fa-solid fa-file-signature me-2"></i> Enrollments</a>
                <a href="payments.php"><i class="fa-solid fa-indian-rupee-sign me-2"></i> Fee Payments</a>
                <a href="attendance.php" class="active"><i class="fa-solid fa-calendar-check me-2"></i> Attendance</a>
                <a href="notices.php"><i class="fa-solid fa-bullhorn me-2"></i> Notice Board</a>
            </div>
            <div class="p-3 text-center text-white-50 border-top border-secondary">
                <small>PHP & Java Hybrid System</small>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            <h2 class="mb-4">Daily Attendance Register</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card card-container border-0 shadow-sm p-4 bg-white mb-4">
                <div class="row align-items-center justify-content-between g-3 mb-3 border-bottom pb-3">
                    <div class="col-md-6">
                        <h5 class="m-0 text-secondary"><i class="fa-solid fa-calendar-days text-primary me-2"></i>Mark Attendance Roll</h5>
                    </div>
                    <div class="col-md-4">
                        <form action="attendance.php" method="GET" class="d-flex align-items-center gap-2">
                            <label class="form-label text-nowrap m-0 small fw-bold">Select Date:</label>
                            <input type="date" name="date" class="form-control" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
                        </form>
                    </div>
                </div>

                <?php if (empty($students)): ?>
                    <p class="text-muted">No student records found to mark attendance. Ensure you have registered students first.</p>
                <?php else: ?>
                    <form action="attendance.php?date=<?php echo $selected_date; ?>" method="POST">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Guardian</th>
                                        <th class="text-center" style="width: 250px;">Attendance Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $s): 
                                        $sid = $s['id'];
                                        $status = $recorded_attendance[$sid] ?? 'Present'; // default to Present in checkboxes
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $sid; ?></strong></td>
                                            <td><?php echo htmlspecialchars($s['name']); ?></td>
                                            <td><?php echo htmlspecialchars($s['guardianName']); ?></td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="Status Toggle">
                                                    <input type="radio" class="btn-check" name="status[<?php echo $sid; ?>]" id="pres_<?php echo $sid; ?>" value="Present" <?php echo $status == 'Present' ? 'checked' : ''; ?>>
                                                    <label class="btn btn-sm btn-outline-success px-3" for="pres_<?php echo $sid; ?>"><i class="fa-solid fa-circle-check me-1"></i>Present</label>

                                                    <input type="radio" class="btn-check" name="status[<?php echo $sid; ?>]" id="abs_<?php echo $sid; ?>" value="Absent" <?php echo $status == 'Absent' ? 'checked' : ''; ?>>
                                                    <label class="btn btn-sm btn-outline-danger px-3" for="abs_<?php echo $sid; ?>"><i class="fa-solid fa-circle-xmark me-1"></i>Absent</label>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end border-top pt-3">
                            <button type="submit" class="btn btn-primary px-5"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Save Attendance Records</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
