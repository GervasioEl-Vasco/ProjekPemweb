<?php
// Function 1: Validasi email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function 2: Sanitasi input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function 3: Cek jika user adalah admin
function isAdmin() {
    return isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'admin';
}

// Function 4: Cek jika user adalah moderator atau admin
function isStaff() {
    return isset($_SESSION['account_type']) && 
          ($_SESSION['account_type'] == 'admin' || $_SESSION['account_type'] == 'moderator');
}

// Function 5: Format harga
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Function 6: Dapatkan nama peran pengguna
function getRoleName($role) {
    $roles = [
        'player' => 'Player',
        'moderator' => 'Moderator',
        'admin' => 'Administrator'
    ];
    return $roles[$role] ?? 'Unknown';
}
?>