<?php
require_once 'api_helper.php';

// Fetch Dashboard Metrics
$dash_response = call_api('GET', '/dashboard');
$stats = [
    'totalStudents' => 0,
    'totalEnrollments' => 0,
    'totalPayments' => 0.0,
    'totalNotices' => 0,
    'totalTeachers' => 0,
    'totalSchedules' => 0
];
if ($dash_response['status'] == 200 && is_array($dash_response['data'])) {
    $stats = array_merge($stats, $dash_response['data']);
}

// Fetch Recent Notices
$notice_response = call_api('GET', '/notices');
$notices = [];
if ($notice_response['status'] == 200 && is_array($notice_response['data'])) {
    $notices = array_slice($notice_response['data'], 0, 5); // top 5 notices
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Pre-School Enrollment System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { background-color: #2c3e50; min-height: 100vh; }
        .sidebar a { color: #ecf0f1; text-decoration: none; padding: 12px 15px; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #34495e; border-left: 4px solid #3498db; }
        .card-stat { border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
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
                <a href="index.php" class="active"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
                <a href="students.php"><i class="fa-solid fa-child me-2"></i> Students</a>
                <a href="enrollment.php"><i class="fa-solid fa-file-signature me-2"></i> Enrollments</a>
                <a href="payments.php"><i class="fa-solid fa-indian-rupee-sign me-2"></i> Fee Payments</a>
                <a href="attendance.php"><i class="fa-solid fa-calendar-check me-2"></i> Attendance</a>
                <a href="teachers.php"><i class="fa-solid fa-chalkboard-user me-2"></i> Teachers</a>
                <a href="schedules.php"><i class="fa-solid fa-calendar-days me-2"></i> Class Schedules</a>
                <a href="notices.php"><i class="fa-solid fa-bullhorn me-2"></i> Notice Board</a>
            </div>
            <div class="p-3 text-center text-white-50 border-top border-secondary">
                <small>PHP & Java Hybrid System</small>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            <h2 class="mb-4">Dashboard</h2>

            <!-- Metric Summary Cards Row 1 -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card card-stat bg-white p-3 border-0 border-start border-4 border-primary">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 13px;">Total Students</h6>
                                <h3 class="m-0"><?php echo $stats['totalStudents']; ?></h3>
                            </div>
                            <i class="fa-solid fa-child fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stat bg-white p-3 border-0 border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 13px;">Active Enrollments</h6>
                                <h3 class="m-0"><?php echo $stats['totalEnrollments']; ?></h3>
                            </div>
                            <i class="fa-solid fa-user-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stat bg-white p-3 border-0 border-start border-4 border-warning">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 13px;">Fees Collected</h6>
                                <h3 class="m-0">₹<?php echo number_format($stats['totalPayments'], 2); ?></h3>
                            </div>
                            <i class="fa-solid fa-indian-rupee-sign fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stat bg-white p-3 border-0 border-start border-4 border-danger">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 13px;">Notices Posted</h6>
                                <h3 class="m-0"><?php echo $stats['totalNotices']; ?></h3>
                            </div>
                            <i class="fa-solid fa-bullhorn fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metric Summary Cards Row 2 (New Modules) -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card card-stat bg-white p-3 border-0 border-start border-4 border-info">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 13px;">Registered Faculty / Teachers</h6>
                                <h3 class="m-0"><?php echo $stats['totalTeachers']; ?></h3>
                            </div>
                            <i class="fa-solid fa-chalkboard-user fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-stat bg-white p-3 border-0 border-start border-4 border-secondary">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1" style="font-size: 13px;">Active Class Schedules</h6>
                                <h3 class="m-0"><?php echo $stats['totalSchedules']; ?></h3>
                            </div>
                            <i class="fa-solid fa-calendar-days fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Split Section -->
            <div class="row g-4">
                <!-- Recent Announcements Notice Board -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="m-0 text-secondary"><i class="fa-solid fa-bullhorn text-danger me-2"></i>Recent Announcements</h5>
                            <a href="notices.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <?php if (empty($notices)): ?>
                            <p class="text-muted">No announcements posted yet.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($notices as $n): ?>
                                    <div class="list-group-item px-0 py-3">
                                        <div class="d-flex w-100 justify-content-between mb-1">
                                            <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($n['title']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($n['createdAt']); ?></small>
                                        </div>
                                        <p class="mb-1 text-darkSmall text-secondary" style="font-size: 14px;">
                                            <?php echo nl2br(htmlspecialchars($n['content'])); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Setup Info Card -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 bg-light">
                        <h5 class="mb-3 text-secondary"><i class="fa-solid fa-circle-info text-info me-2"></i>System Architecture</h5>
                        <p class="small text-muted mb-2">This is a decoupled client-server application:</p>
                        <ul class="small text-secondary ps-3 mb-4">
                            <li><strong>Frontend:</strong> Executing in PHP (Web client)</li>
                            <li><strong>REST Gateway:</strong> Java Backend APIs running on port 8085</li>
                            <li><strong>Database:</strong> MySQL storage engine</li>
                        </ul>
                        <div class="text-center">
                            <a href="teachers.php" class="btn btn-primary w-100 mb-2"><i class="fa-solid fa-user-tie me-2"></i>Register New Teacher</a>
                            <a href="schedules.php" class="btn btn-success w-100"><i class="fa-solid fa-calendar-plus me-2"></i>Schedule Time Slot</a>
                        </div>
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
