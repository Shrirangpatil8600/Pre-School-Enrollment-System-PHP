<?php
require_once 'api_helper.php';

$message = '';
$message_class = '';

// Handle POST Form Submission to register teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_data = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'contact' => $_POST['contact'] ?? '',
        'specialization' => $_POST['specialization'] ?? ''
    ];

    $response = call_api('POST', '/teachers', $teacher_data);
    if ($response['status'] == 201) {
        $message = "Teacher registered successfully!";
        $message_class = "alert-success";
    } else {
        $error = $response['data']['error'] ?? 'API connection failure.';
        $message = "Failed to register teacher: " . $error;
        $message_class = "alert-danger";
    }
}

// Fetch all registered teachers
$api_response = call_api('GET', '/teachers');
$teachers = [];
if ($api_response['status'] == 200 && is_array($api_response['data'])) {
    $teachers = $api_response['data'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teachers - Pre-School Enrollment System</title>
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
                <a href="teachers.php" class="active"><i class="fa-solid fa-chalkboard-user me-2"></i> Teachers</a>
                <a href="schedules.php"><i class="fa-solid fa-calendar-days me-2"></i> Class Schedules</a>
                <a href="notices.php"><i class="fa-solid fa-bullhorn me-2"></i> Notice Board</a>
            </div>
            <div class="p-3 text-center text-white-50 border-top border-secondary">
                <small>PHP & Java Hybrid System</small>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            <h2 class="mb-4">Teacher & Staff Management</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Registration Form Card -->
                <div class="col-md-4">
                    <div class="card card-form border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Register Teacher</h5>
                        <form action="teachers.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Priya Deshmukh" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="e.g. priya@tenderflow.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" name="contact" class="form-control" pattern="[0-9]{10}" placeholder="10 digit number" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Specialization / Role</label>
                                <input type="text" name="specialization" class="form-control" placeholder="e.g. Nursery Coordinator" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Submit Registration</button>
                        </form>
                    </div>
                </div>

                <!-- Directory Grid Card -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-chalkboard-user me-2 text-success"></i>Staff Directory</h5>
                        <?php if (empty($teachers)): ?>
                            <p class="text-muted">No teacher profiles recorded yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Teacher ID</th>
                                            <th>Name</th>
                                            <th>Email Address</th>
                                            <th>Contact</th>
                                            <th>Specialization / Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($teachers as $t): ?>
                                            <tr>
                                                <td><strong>#TCH-<?php echo $t['id']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($t['name']); ?></td>
                                                <td><?php echo htmlspecialchars($t['email']); ?></td>
                                                <td><?php echo htmlspecialchars($t['contact']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($t['specialization']); ?></span></td>
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
