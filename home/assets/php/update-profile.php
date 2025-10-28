<?php
// Turn off error display to prevent HTML output
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Function to send JSON response and exit
function sendResponse($status, $message, $data = null) {
    $response = [
        'status' => $status,
        'message' => $message
    ];
    
    if ($data) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    sendResponse('error', 'Not logged in');
}

// Get JSON input
$json = file_get_contents('php://input');
$input = json_decode($json, true);

// Check if JSON decode was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    sendResponse('error', 'Invalid JSON data');
}

// Validate input
if (!isset($input['name']) || !isset($input['phone'])) {
    sendResponse('error', 'Missing required fields');
}

// Sanitize input
$name = trim($input['name']);
$phone = trim($input['phone']);

// Validate name
if (empty($name) || strlen($name) < 2 || strlen($name) > 100) {
    sendResponse('error', 'Name must be between 2 and 100 characters');
}

// Validate phone number
if (empty($phone) || !preg_match('/^[+]?[\d\s\-\(\)]{10,20}$/', $phone)) {
    sendResponse('error', 'Please enter a valid phone number');
}

// Check if name contains only valid characters
if (!preg_match('/^[a-zA-Z\s\.\-\']+$/', $name)) {
    sendResponse('error', 'Name contains invalid characters');
}

try {
    // Include database configuration
    if (!file_exists('config.php')) {
        sendResponse('error', 'Configuration file not found');
    }
    
    require_once 'config.php';
    
    // Check if PDO connection exists
    if (!isset($pdo)) {
        sendResponse('error', 'Database connection not available');
    }

    // Check if customer exists
    $stmt = $pdo->prepare("SELECT id FROM customer WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        sendResponse('error', 'Customer not found');
    }

    // Update customer profile in customer table
    $stmt = $pdo->prepare("UPDATE customer SET cus_name = ?, cus_ph = ? WHERE id = ?");
    $result = $stmt->execute([$name, $phone, $_SESSION['user_id']]);

    if ($result) {
        // Update session data
        $_SESSION['user_name'] = $name;
        $_SESSION['user_phone'] = $phone;

        // Optional: Log the update if you have an activity log table
        try {
            $stmt = $pdo->prepare("INSERT INTO user_activity_log (user_id, activity, ip_address, created_at) VALUES (?, 'profile_update', ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
        } catch(PDOException $e) {
            // Log table might not exist, ignore this error
            error_log("Activity log error: " . $e->getMessage());
        }

        sendResponse('success', 'Profile updated successfully', [
            'user' => [
                'name' => $name,
                'phone' => $phone
            ]
        ]);
    } else {
        sendResponse('error', 'Failed to update profile');
    }

} catch(PDOException $e) {
    // Log error for debugging (don't expose to user)
    error_log("Database error in update-profile.php: " . $e->getMessage());
    sendResponse('error', 'Database error occurred');
    
} catch(Exception $e) {
    // Log any other errors
    error_log("General error in update-profile.php: " . $e->getMessage());
    sendResponse('error', 'An unexpected error occurred');
}

// Close connection
$pdo = null;
?>