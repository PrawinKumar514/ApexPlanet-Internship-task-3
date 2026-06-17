<?php
/**
 * Landing page - redirects to login if not logged in,
 * otherwise to dashboard
 */

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard/dashboard.php");
    exit();
} else {
    header("Location: auth/login.php");
    exit();
}
?>