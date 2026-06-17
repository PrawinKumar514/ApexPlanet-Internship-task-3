<?php
/**
 * Profile Page - View and update own profile
 */

require_once '../config/database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, profile_image, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update via AJAX? but we'll use update_profile.php for POST
$message = '';
if (isset($_SESSION['profile_updated'])) {
    $message = $_SESSION['profile_updated'];
    unset($_SESSION['profile_updated']);
}
if (isset($_SESSION['profile_error'])) {
    $error = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - User Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Include Sidebar -->
    <?php include '../dashboard/sidebar.php'; ?>

    <div class="main-content">
        <!-- Top Navbar -->
        <?php include '../dashboard/navbar.php'; ?>

        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card glass-card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4><i class="bi bi-person-gear"></i> My Profile</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>

                            <div class="text-center mb-4">
                                <img src="../assets/uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                     alt="Profile" class="rounded-circle" width="150" height="150" style="object-fit: cover;">
                                <h4 class="mt-2"><?php echo htmlspecialchars($user['name']); ?></h4>
                                <p class="text-muted"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                                <span class="badge bg-primary"><?php echo ucfirst($user['role']); ?></span>
                                <p class="mt-2"><small>Joined: <?php echo date('F d, Y', strtotime($user['created_at'])); ?></small></p>
                            </div>

                            <hr>

                            <!-- Update Form -->
                            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Profile Image (JPG, PNG, max 2MB)</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                    <div class="form-text">Leave empty to keep current image.</div>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Profile</button>
                            </form>
                        </div>
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