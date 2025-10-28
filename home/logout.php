<?php
// logout.php
session_start();
header('Content-Type: application/json');

// Enable CORS if needed
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'logout') {
    // Clear all session data
    session_unset();
    session_destroy();
    
    // Clear remember me cookie if it exists
    if (isset($_COOKIE['remember_user'])) {
        setcookie('remember_user', '', time() - 3600, '/');
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Logged out successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid action'
    ]);
}
?>