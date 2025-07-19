<?php
session_start();
require_once 'db.php';

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect to login if not authenticated
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

// Redirect to login if not admin
function require_admin() {
    require_login();
    if (!is_admin()) {
        header('Location: index.php?error=access_denied');
        exit();
    }
}

// Get current user info
function get_logged_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    $query = "SELECT * FROM users WHERE id = ?";
    $result = execute_query($query, 'i', [$_SESSION['user_id']]);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Logout function
function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
