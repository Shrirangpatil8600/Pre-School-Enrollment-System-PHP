<?php
require_once 'api_helper.php';

$message = '';
$message_class = '';

// Handle POST Form Submission to add schedule
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $schedule_data = [
        'programId' => (int)($_POST['program_id'] ?? 0),
        'teacherId' => (int)($_POST['teacher_id'] ?? 0),
        'dayOfWeek' => $_POST['day_of_week'] ?? '',
        'timeSlot' => $_POST['time_slot'] ?? '',
        'roomNo' => $_POST['room_no'] ?? ''
    ];

    $response = call_api('POST', '/schedules', $schedule_data);
    if ($response['status'] == 201) {
        $message = "Class schedule added successfully!";
        $message_class = "alert-success";
    } else {
        $error = $response['data']['error'] ?? 'API connection failure.';
        $message = "Failed to add class schedule: " . $error;
        $message_class = "alert-danger";
    }
}

// Fetch all schedules
$schedule_response = call_api('GET', '/schedules');
$schedules = [];
if ($schedule_response['status'] == 200 && is_array($schedule_response['data'])) {
    $schedules = $schedule_response['data'];
}

// Fetch programs for dropdown
$program_response = call_api('GET', '/programs');
$programs = [];
if ($program_response['status'] == 200 && is_array($program_response['data'])) {
    $programs = $program_response['data'];
}

// Fetch teachers for dropdown
$teacher_response = call_api('GET', '/teachers');
$teachers = [];
if ($teacher_response['status'] == 200 && is_array($teacher_response['data'])) {
    $teachers = $teacher_response['data'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Schedules - Pre-School Enrollment System</title>
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
                <a href="enrollment.php"><i class="fa-solid fa-file-signature me-2"></i> Enrollments</a>
                <a href="payments.php"><i class="fa-solid fa-indian-rupee-sign me-2"></i> Fee Payments</a>
                <a href="attendance.php"><i class="fa-solid fa-calendar-check me-2"></i> Attendance</a>
                <a href="teachers.php"><i class="fa-solid fa-chalkboard-user me-2"></i> Teachers</a>
                <a href="schedules.php" class="active"><i class="fa-solid fa-calendar-days me-2"></i> Class Schedules</a>
                <a href="notices.php"><i class="fa-solid fa-bullhorn me-2"></i> Notice Board</a>
            </div>
            <div class="p-3 text-center text-white-50 border-top border-secondary">
                <small>PHP & Java Hybrid System</small>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            <h2 class="mb-4">Class Schedules / Timetable</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Create Schedule Form Card -->
                <div class="col-md-4">
                    <div class="card card-form border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-clock me-2 text-primary"></i>Add Time Slot</h5>
                        <form action="schedules.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Select Program / Grade</label>
                                <select name="program_id" class="form-select" required>
                                    <option value="" disabled selected>Select Grade</option>
                                    <?php foreach ($programs as $p): ?>
                                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Assign Teacher</label>
                                <select name="teacher_id" class="form-select" required>
                                    <option value="" disabled selected>Select Teacher</option>
                                    <?php foreach ($teachers as $t): ?>
                                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?> (<?php echo htmlspecialchars($t['specialization']); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Day of Week</label>
                                <select name="day_of_week" class="form-select" required>
                                    <option value="" disabled selected>Select Day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Time Slot</label>
                                <input type="text" name="time_slot" class="form-control" placeholder="e.g. 09:00 AM - 11:30 AM" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Room Number / Name</label>
                                <input type="text" name="room_no" class="form-control" placeholder="e.g. Room A" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Schedule Class</button>
                        </form>
                    </div>
                </div>

                <!-- Schedules List Card -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-calendar-days me-2 text-success"></i>Timetable Matrix</h5>
                        <?php if (empty($schedules)): ?>
                            <p class="text-muted">No schedules structured yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Schedule ID</th>
                                            <th>Program / Grade</th>
                                            <th>Assigned Teacher</th>
                                            <th>Day of Week</th>
                                            <th>Time Slot</th>
                                            <th>Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($schedules as $s): ?>
                                            <tr>
                                                <td><strong>#SCH-<?php echo $s['id']; ?></strong></td>
                                                <td><span class="badge bg-info text-white"><?php echo htmlspecialchars($s['programName']); ?></span></td>
                                                <td><strong><?php echo htmlspecialchars($s['teacherName']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($s['dayOfWeek']); ?></td>
                                                <td class="font-monospace text-primary fw-bold"><?php echo htmlspecialchars($s['timeSlot']); ?></td>
                                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($s['roomNo']); ?></span></td>
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
