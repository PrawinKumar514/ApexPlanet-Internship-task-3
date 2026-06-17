<?php
/**
 * Dashboard - Main landing after login
 * Displays user information and quick stats
 */

require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, profile_image, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Count total users (admin only)
$total_users = 0;
if ($user_role === 'admin') {
    $result = $conn->query("SELECT COUNT(*) AS total FROM users");
    $total_users = $result->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Top Navbar -->
        <?php include 'navbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-md-12">
                    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                    <p class="text-muted">Here's an overview of your account.</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card glass-card text-center">
                        <div class="card-body">
                            <i class="bi bi-person-circle display-1"></i>
                            <h5 class="card-title mt-2">Your Role</h5>
                            <p class="card-text"><span class="badge bg-primary"><?php echo ucfirst($user['role']); ?></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card glass-card text-center">
                        <div class="card-body">
                            <i class="bi bi-calendar-date display-1"></i>
                            <h5 class="card-title mt-2">Joined</h5>
                            <p class="card-text"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
                <?php if ($user_role === 'admin'): ?>
                <div class="col-md-4">
                    <div class="card glass-card text-center">
                        <div class="card-body">
                            <i class="bi bi-people display-1"></i>
                            <h5 class="card-title mt-2">Total Users</h5>
                            <p class="card-text"><?php echo $total_users; ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4>Quick Actions</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="view_users.php" class="btn btn-outline-primary"><i class="bi bi-people"></i> View Users</a>
                        <?php if ($user_role === 'admin'): ?>
                            <a href="add_user.php" class="btn btn-outline-success"><i class="bi bi-person-plus"></i> Add User</a>
                        <?php endif; ?>
                        <a href="../profile/profile.php" class="btn btn-outline-info"><i class="bi bi-person-gear"></i> My Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/script.js"></script>
</body>
</html>