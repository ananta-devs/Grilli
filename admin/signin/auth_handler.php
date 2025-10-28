<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'register':
        handleRegister($pdo, $input);
        break;
    case 'login':
        handleLogin($pdo, $input);
        break;
    case 'forgot_password':
        handleForgotPassword($pdo, $input);
        break;
    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

function handleRegister($pdo, $data) {
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirmPassword'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        return;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long']);
        return;
    }
    
    if ($password !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        return;
    }
    
    // Validate phone number (should be numeric)
    if (!empty($phone) && !is_numeric($phone)) {
        echo json_encode(['status' => 'error', 'message' => 'Phone number must be numeric']);
        return;
    }
    
    // Check if admin email already exists
    try {
        $stmt = $pdo->prepare("SELECT id FROM admin WHERE adm_email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Admin email already registered']);
            return;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new admin
        $phoneValue = empty($phone) ? null : (int)$phone;
        $stmt = $pdo->prepare("INSERT INTO admin (adm_name, adm_email, adm_ph, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phoneValue, $hashedPassword]);
        
        echo json_encode(['status' => 'success', 'message' => 'Admin account created successfully! Please sign in.']);
        
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Admin registration failed. Please try again.']);
    }
}

function handleLogin($pdo, $data) {
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $remember = $data['remember'] ?? false;
    
    // Validation
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, adm_name, adm_email, password FROM admin WHERE adm_email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Login successful
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['adm_name'];
            $_SESSION['user_email'] = $admin['adm_email'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['logged_in'] = true;
            
            // Set remember me cookie if requested
            if ($remember) {
                $cookieValue = base64_encode($admin['id'] . ':' . hash('sha256', $admin['password']) . ':admin');
                setcookie('remember_user', $cookieValue, time() + (30 * 24 * 60 * 60), '/'); // 30 days
            }
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Admin login successful!',
                'user' => [
                    'id' => $admin['id'],
                    'name' => $admin['adm_name'],
                    'email' => $admin['adm_email'],
                    'type' => 'admin'
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid admin credentials']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Admin login failed. Please try again.']);
    }
}

function handleForgotPassword($pdo, $data) {
    $email = trim($data['email'] ?? '');
    
    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email is required']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM admin WHERE adm_email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            // In a real application, you would generate a reset token and send an email
            // For demo purposes, we'll just return success
            echo json_encode(['status' => 'success', 'message' => 'Password reset link sent to admin email']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Admin email not found']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Password reset failed. Please try again.']);
    }
}
?>