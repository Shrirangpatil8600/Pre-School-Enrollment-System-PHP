<?php
require_once 'api_helper.php';

$message = '';
$message_class = '';

// Handle POST Form Submission to register student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_data = [
        'name' => $_POST['name'] ?? '',
        'age' => (int)($_POST['age'] ?? 0),
        'dob' => $_POST['dob'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'guardianName' => $_POST['guardian_name'] ?? '',
        'contact' => $_POST['contact'] ?? '',
        'address' => $_POST['address'] ?? ''
    ];

    $response = call_api('POST', '/students', $student_data);
    if ($response['status'] == 201) {
        $message = "Student registered successfully!";
        $message_class = "alert-success";
    } else {
        $error = $response['data']['error'] ?? 'API connection failure.';
        $message = "Failed to register student: " . $error;
        $message_class = "alert-danger";
    }
}

// Fetch all registered students
$api_response = call_api('GET', '/students');
$students = [];
if ($api_response['status'] == 200 && is_array($api_response['data'])) {
    $students = $api_response['data'];
} else if ($api_response['status'] == 500) {
    $message = $api_response['error'] ?? 'Java Backend is offline.';
    $message_class = "alert-warning";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Directory - Pre-School Enrollment System</title>
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
                <a href="students.php" class="active"><i class="fa-solid fa-child me-2"></i> Students</a>
                <a href="enrollment.php"><i class="fa-solid fa-file-signature me-2"></i> Enrollments</a>
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
            <h2 class="mb-4">Student Management</h2>

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
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Register Student</h5>
                        <form action="students.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Aarav Patil" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="age" class="form-control" min="2" max="6" placeholder="e.g. 4" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="" disabled selected>Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Guardian Name</label>
                                <input type="text" name="guardian_name" class="form-control" placeholder="e.g. Shrirang Patil" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" name="contact" class="form-control" pattern="[0-9]{10}" placeholder="10 digit number" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Residential Address" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Submit Registration</button>
                        </form>
                    </div>
                </div>

                <!-- Directory Grid Card -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-address-book me-2 text-success"></i>Student Directory</h5>
                        <?php if (empty($students)): ?>
                            <p class="text-muted">No student records found. Check if the Java backend API is running on port 8085.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Age / Gender</th>
                                            <th>DOB</th>
                                            <th>Guardian</th>
                                            <th>Contact</th>
                                            <th>Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $s): ?>
                                            <tr>
                                                <td><strong>#<?php echo $s['id']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($s['name']); ?></td>
                                                <td><?php echo $s['age']; ?> yrs / <?php echo htmlspecialchars($s['gender']); ?></td>
                                                <td><?php echo htmlspecialchars($s['dob']); ?></td>
                                                <td><?php echo htmlspecialchars($s['guardianName']); ?></td>
                                                <td><?php echo htmlspecialchars($s['contact']); ?></td>
                                                <td><small class="text-muted"><?php echo htmlspecialchars($s['address']); ?></small></td>
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
