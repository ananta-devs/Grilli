<?php
// process_reservation.php - Fixed version to prevent HTML output
session_start();

// CRITICAL: Turn off error display and use only error logging
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);

// Clean any previous output
ob_start();

require_once 'config.php';

// Set JSON headers IMMEDIATELY
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Clean any buffered output before processing
    ob_clean();
    
    // Get JSON input or form data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    // Log input for debugging (will go to error log, not output)
    error_log("Reservation input: " . json_encode($input));
    
    // Get user_id from session
    $user_id = null;
    if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $user_id = $_SESSION['user_id'];
    } else {
        // Check remember me cookie if session is not active
        if (isset($_COOKIE['remember_user']) && !empty($_COOKIE['remember_user'])) {
            try {
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
                        
                        $user_id = $user['id'];
                    }
                }
            } catch (Exception $e) {
                error_log("Cookie validation error: " . $e->getMessage());
                setcookie('remember_user', '', time() - 3600, '/');
            }
        }
    }
    
    // Check if user is logged in
    if (!$user_id) {
        ob_clean(); // Clean any output
        echo json_encode([
            'success' => false, 
            'message' => 'Please sign in to continue',
            'login_required' => true
        ]);
        exit;
    }
    
    // Validate and sanitize input data
    $name = trim($input['name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $persons = intval($input['persons'] ?? 0);
    $reservation_date = $input['reservation_date'] ?? '';
    $time_slot = $input['time_slot'] ?? '';
    $message = trim($input['message'] ?? '');
    
    // Use session data for name and phone if not provided
    if (isset($_SESSION['user_name']) && isset($_SESSION['user_phone'])) {
        if (empty($name)) {
            $name = $_SESSION['user_name'];
        }
        if (empty($phone)) {
            $phone = $_SESSION['user_phone'];
        }
    }
    
    // Validation
    if (empty($name) || empty($phone) || $persons <= 0 || empty($reservation_date) || empty($time_slot)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit;
    }
    
    // Validate phone number
    if (!preg_match('/^[0-9+\-\s()]{10,20}$/', $phone)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Please enter a valid phone number']);
        exit;
    }
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $reservation_date)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        exit;
    }
    
    // Validate date (must be today or future)
    $today = date('Y-m-d');
    if ($reservation_date < $today) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Reservation date cannot be in the past']);
        exit;
    }
    
    // Validate persons count
    if ($persons > 10) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Maximum 10 persons allowed per reservation']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Find suitable available table
    $tableStmt = $pdo->prepare("
        SELECT tpm.table_no, tpm.max_persons 
        FROM table_persons_mapping tpm
        LEFT JOIN reservations r ON tpm.table_no = r.table_no 
            AND r.reservation_date = ? 
            AND r.time_slot = ? 
            AND r.status IN ('pending', 'confirmed')
        WHERE tpm.status = 'available' 
        AND tpm.max_persons >= ?
        AND r.table_no IS NULL
        ORDER BY tpm.max_persons ASC 
        LIMIT 1
    ");
    $tableStmt->execute([$reservation_date, $time_slot, $persons]);
    $availableTable = $tableStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$availableTable) {
        $pdo->rollBack();
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'No tables available for the selected date and time']);
        exit;
    }
    
    // Validate user exists
    $userStmt = $pdo->prepare("SELECT id FROM customer WHERE id = ?");
    $userStmt->execute([$user_id]);
    if (!$userStmt->fetch()) {
        $pdo->rollBack();
        ob_clean();
        echo json_encode([
            'success' => false, 
            'message' => 'Please sign in to continue',
            'login_required' => true
        ]);
        exit;
    }
    
    // Insert reservation
    $insertStmt = $pdo->prepare("
        INSERT INTO reservations (name, phone, persons, reservation_date, time_slot, message, table_no, status, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)
    ");
    
    $result = $insertStmt->execute([
        $name,
        $phone,
        $persons,
        $reservation_date,
        $time_slot,
        $message,
        $availableTable['table_no'],
        $user_id
    ]);
    
    if (!$result) {
        $pdo->rollBack();
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to create reservation']);
        exit;
    }
    
    $reservation_id = $pdo->lastInsertId();
        
    // Commit transaction
    $pdo->commit();
    
    // Clean output buffer and send success response
    ob_clean();
    echo json_encode([
        'success' => true, 
        'message' => 'Reservation confirmed successfully!',
        'reservation_id' => $reservation_id,
        'table_no' => $availableTable['table_no'],
        'user_logged_in' => true,
        'details' => [
            'name' => $name,
            'phone' => $phone,
            'date' => $reservation_date,
            'time' => $time_slot,
            'persons' => $persons,
            'table' => $availableTable['table_no'],
            'message' => $message,
            'user_id' => $user_id
        ]
    ]);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Database error in process_reservation.php: " . $e->getMessage());
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error occurred. Please try again.']);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("General error in process_reservation.php: " . $e->getMessage());
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}

// End output buffering and send response
ob_end_flush();
?>