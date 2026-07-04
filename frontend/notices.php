<?php
require_once 'api_helper.php';

$message = '';
$message_class = '';

// Handle POST Form Submission to write new notice
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $notice_data = [
        'title' => $_POST['title'] ?? '',
        'content' => $_POST['content'] ?? ''
    ];

    $response = call_api('POST', '/notices', $notice_data);
    if ($response['status'] == 201) {
        $message = "Announcement posted successfully!";
        $message_class = "alert-success";
    } else {
        $error = $response['data']['error'] ?? 'API connection failure.';
        $message = "Failed to post announcement: " . $error;
        $message_class = "alert-danger";
    }
}

// Fetch all notices
$notice_response = call_api('GET', '/notices');
$notices = [];
if ($notice_response['status'] == 200 && is_array($notice_response['data'])) {
    $notices = $notice_response['data'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notice Board - Pre-School Enrollment System</title>
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
                <a href="notices.php" class="active"><i class="fa-solid fa-bullhorn me-2"></i> Notice Board</a>
            </div>
            <div class="p-3 text-center text-white-50 border-top border-secondary">
                <small>PHP & Java Hybrid System</small>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            <h2 class="mb-4">School Notice Board</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Create Notice Form Card -->
                <div class="col-md-4">
                    <div class="card card-form border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-circle-plus me-2 text-primary"></i>Post Announcement</h5>
                        <form action="notices.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Notice Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Monsoon Holidays" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notice Details / Content</label>
                                <textarea name="content" class="form-control" rows="5" placeholder="Write announcement text here..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100"><i class="fa-solid fa-bullhorn me-2"></i>Publish Notice</button>
                        </form>
                    </div>
                </div>

                <!-- Display Notices Card -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-clipboard-list me-2 text-success"></i>Announcement Feed</h5>
                        <?php if (empty($notices)): ?>
                            <p class="text-muted">No notices posted yet.</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($notices as $n): ?>
                                    <div class="col-12">
                                        <div class="card border border-light shadow-sm">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <h6 class="m-0 text-primary fw-bold"><?php echo htmlspecialchars($n['title']); ?></h6>
                                                <small class="text-muted"><i class="fa-regular fa-clock me-1"></i><?php echo htmlspecialchars($n['createdAt']); ?></small>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text text-secondary style-desc" style="font-size: 15px; white-space: pre-line;">
                                                    <?php echo htmlspecialchars($n['content']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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
