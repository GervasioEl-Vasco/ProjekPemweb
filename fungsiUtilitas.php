<?php
// Function 1: Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function 2: Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function 3: Check if user is admin
function isAdmin() {
    return isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'admin';
}

// Function 4: Check if user is moderator or admin
function isStaff() {
    return isset($_SESSION['account_type']) && 
          ($_SESSION['account_type'] == 'admin' || $_SESSION['account_type'] == 'moderator');
}

// Function 5: Format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Function 6: Get user role name
function getRoleName($role) {
    $roles = [
        'player' => 'Player',
        'moderator' => 'Moderator',
        'admin' => 'Administrator'
    ];
    return $roles[$role] ?? 'Unknown';
}
?>