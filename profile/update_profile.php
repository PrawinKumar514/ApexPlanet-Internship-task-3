<?php
/**
 * Update Profile - Process name, email, and image upload
 */

require_once '../config/database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch current user data
$stmt = $conn->prepare("SELECT name, email, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validate
    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email is taken by another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Email already taken by another user.";
        } else {
            // Handle image upload
            $profile_image = $user['profile_image']; // default to current
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png'];
                $file_name = $_FILES['profile_image']['name'];
                $file_tmp = $_FILES['profile_image']['tmp_name'];
                $file_size = $_FILES['profile_image']['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if (!in_array($file_ext, $allowed)) {
                    $error = "Only JPG, JPEG, and PNG files are allowed.";
                } elseif ($file_size > 2 * 1024 * 1024) {
                    $error = "File size must be less than 2MB.";
                } else {
                    // Generate unique file name
                    $new_name = uniqid() . '.' . $file_ext;
                    $upload_path = '../assets/uploads/' . $new_name;
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Delete old image if not default
                        if ($user['profile_image'] !== 'default.png') {
                            $old_path = '../assets/uploads/' . $user['profile_image'];
                            if (file_exists($old_path)) {
                                unlink($old_path);
                            }
                        }
                        $profile_image = $new_name;
                    } else {
                        $error = "Failed to upload image.";
                    }
                }
            }

            // If no error, update database
            if (empty($error)) {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, profile_image = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $profile_image, $user_id);
                if ($stmt->execute()) {
                    // Update session variables
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_image'] = $profile_image;
                    $success = "Profile updated successfully!";
                    // Redirect back to profile with success message
                    $_SESSION['profile_updated'] = $success;
                    header("Location: profile.php");
                    exit();
                } else {
                    $error = "Failed to update profile.";
                }
                $stmt->close();
            }
        }
    }

    // If error, redirect with error
    if (!empty($error)) {
        $_SESSION['profile_error'] = $error;
        header("Location: profile.php");
        exit();
    }
} else {
    // Not POST
    header("Location: profile.php");
    exit();
}
?>