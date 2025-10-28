<?php
// session_check.php
// Prevent any output before JSON
ob_start();
error_reporting(0); // Disable error display in production
ini_set('display_errors', 0);

session_start();

// Clean any previous output
ob_clean();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    if ($action === 'check_session') {
        // Check if user is logged in
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && $_SESSION['logged_in'] === true) {
            // If session exists, also fetch fresh data from database to ensure accuracy
            if (file_exists('config.php')) {
                require_once 'config.php';
                
                $stmt = $pdo->prepare("SELECT id, cus_name, cus_email, cus_ph FROM customer WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Update session with fresh data
                    $_SESSION['user_name'] = $user['cus_name'];
                    $_SESSION['user_email'] = $user['cus_email'];
                    $_SESSION['user_phone'] = $user['cus_ph'];
                    
                    echo json_encode([
                        'status' => 'success',
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user['cus_name'],
                            'email' => $user['cus_email'],
                            'phone' => $user['cus_ph']
                        ],
                        'debug' => [
                            'session_phone' => $_SESSION['user_phone'] ?? 'not set',
                            'db_phone' => $user['cus_ph'] ?? 'not set',
                            'phone_length' => strlen($user['cus_ph'] ?? ''),
                            'phone_empty' => empty($user['cus_ph'])
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'User not found in database'
                    ]);
                }
            } else {
                // config.php doesn't exist, use session data
                echo json_encode([
                    'status' => 'success',
                    'user' => [
                        'id' => $_SESSION['user_id'],
                        'name' => $_SESSION['user_name'],
                        'email' => $_SESSION['user_email'] ?? '',
                        'phone' => $_SESSION['user_phone'] ?? ''
                    ],
                    'debug' => [
                        'source' => 'session_only',
                        'note' => 'config.php not found'
                    ]
                ]);
            }
        } else {
            // Check remember me cookie if session is not active
            if (isset($_COOKIE['remember_user']) && !empty($_COOKIE['remember_user']) && file_exists('config.php')) {
                require_once 'config.php';
                
                $cookieValue = base64_decode($_COOKIE['remember_user']);
                $parts = explode(':', $cookieValue);
                
                if (count($parts) === 2) {
                    $userId = $parts[0];
                    $hashedPassword = $parts[1];
                    
                    // Verify cookie with database
                    $stmt = $pdo->prepare("SELECT id, cus_name, cus_email, cus_ph, password FROM customer WHERE id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user && hash('sha256', $user['password']) === $hashedPassword) {
                        // Restore session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['cus_name'];
                        $_SESSION['user_email'] = $user['cus_email'];
                        $_SESSION['user_phone'] = $user['cus_ph'];
                        $_SESSION['logged_in'] = true;
                        
                        echo json_encode([
                            'status' => 'success',
                            'user' => [
                                'id' => $user['id'],
                                'name' => $user['cus_name'],
                                'email' => $user['cus_email'],
                                'phone' => $user['cus_ph']
                            ],
                            'debug' => [
                                'source' => 'remember_cookie',
                                'phone' => $user['cus_ph'] ?? 'not set'
                            ]
                        ]);
                        exit;
                    }
                }
                
                // Cookie is invalid, clear it
                setcookie('remember_user', '', time() - 3600, '/');
            }
            
            echo json_encode([
                'status' => 'error',
                'message' => 'No active session'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
    }
} catch (Exception $e) {
    // Log error for debugging
    error_log("Error in session_check.php: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error occurred',
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}

// End output buffering and flush
ob_end_flush();
?>