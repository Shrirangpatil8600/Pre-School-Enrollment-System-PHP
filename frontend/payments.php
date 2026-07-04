<?php
require_once 'api_helper.php';

$message = '';
$message_class = '';

// Handle POST Form Submission to log payment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_data = [
        'studentId' => (int)($_POST['student_id'] ?? 0),
        'amountPaid' => (double)($_POST['amount_paid'] ?? 0.0),
        'paymentDate' => $_POST['payment_date'] ?? date('Y-m-d'),
        'paymentMethod' => $_POST['payment_method'] ?? ''
    ];

    $response = call_api('POST', '/payments', $payment_data);
    if ($response['status'] == 201) {
        $message = "Payment logged successfully!";
        $message_class = "alert-success";
    } else {
        $error = $response['data']['error'] ?? 'API connection failure.';
        $message = "Failed to log payment: " . $error;
        $message_class = "alert-danger";
    }
}

// Fetch all payment logs
$payment_response = call_api('GET', '/payments');
$payments = [];
if ($payment_response['status'] == 200 && is_array($payment_response['data'])) {
    $payments = $payment_response['data'];
}

// Fetch students for dropdown list
$student_response = call_api('GET', '/students');
$students = [];
if ($student_response['status'] == 200 && is_array($student_response['data'])) {
    $students = $student_response['data'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Payments - Pre-School Enrollment System</title>
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
                <a href="payments.php" class="active"><i class="fa-solid fa-indian-rupee-sign me-2"></i> Fee Payments</a>
                <a href="attendance.php"><i class="fa-solid fa-calendar-check me-2"></i> Attendance</a>
                <a href="notices.php"><i class="fa-solid fa-bullhorn me-2"></i> Notice Board</a>
            </div>
            <div class="p-3 text-center text-white-50 border-top border-secondary">
                <small>PHP & Java Hybrid System</small>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            <h2 class="mb-4">Fee Payments Collection</h2>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $message_class; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Log Payment Form Card -->
                <div class="col-md-4">
                    <div class="card card-form border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Collect Tuition Fee</h5>
                        <form action="payments.php" method="POST">
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
                                <label class="form-label">Amount Paid (INR)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="amount_paid" class="form-control" min="500" step="100" placeholder="e.g. 5000" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="" disabled selected>Select Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Online (UPI/NetBanking)">Online (UPI/NetBanking)</option>
                                    <option value="Credit/Debit Card">Credit/Debit Card</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Log Transaction</button>
                        </form>
                    </div>
                </div>

                <!-- Payment Logs Table Card -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="fa-solid fa-list me-2 text-success"></i>Transaction Ledger</h5>
                        <?php if (empty($payments)): ?>
                            <p class="text-muted">No transactions recorded yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Receipt ID</th>
                                            <th>Student Name</th>
                                            <th>Amount Paid</th>
                                            <th>Date</th>
                                            <th>Method</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $p): ?>
                                            <tr>
                                                <td><strong>#REC-<?php echo $p['id']; ?></strong></td>
                                                <td><?php echo htmlspecialchars($p['studentName']); ?></td>
                                                <td class="text-success font-monospace fw-bold">₹<?php echo number_format($p['amountPaid'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($p['paymentDate']); ?></td>
                                                <td>
                                                    <span class="badge bg-light text-dark border"><i class="fa-solid fa-credit-card me-1"></i><?php echo htmlspecialchars($p['paymentMethod']); ?></span>
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
