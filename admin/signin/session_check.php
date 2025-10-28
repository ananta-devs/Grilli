<?php
require_once 'config.php';

function checkSession() {
    global $pdo;
    
    // Check if user is logged in via session
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return [
            'logged_in' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'type' => $_SESSION['user_type'] ?? 'customer'
            ]
        ];
    }
    
    // Check remember me cookie
    if (isset($_COOKIE['remember_user'])) {
        $cookieValue = base64_decode($_COOKIE['remember_user']);
        $parts = explode(':', $cookieValue);
        
        if (count($parts) === 3) {
            $userId = $parts[0];
            $passwordHash = $parts[1];
            $userType = $parts[2];
            
            // Set table and column names based on user type
            if ($userType === 'admin') {
                $table = 'admin';
                $nameCol = 'adm_name';
                $emailCol = 'adm_email';
            } else {
                $table = 'customer';
                $nameCol = 'cus_name';
                $emailCol = 'cus_email';
                $userType = 'customer'; // Default fallback
            }
            
            try {
                $stmt = $pdo->prepare("SELECT id, $nameCol, $emailCol, password FROM $table WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && hash('sha256', $user['password']) === $passwordHash) {
                    // Restore session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user[$nameCol];
                    $_SESSION['user_email'] = $user[$emailCol];
                    $_SESSION['user_type'] = $userType;
                    $_SESSION['logged_in'] = true;
                    
                    return [
                        'logged_in' => true,
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user[$nameCol],
                            'email' => $user[$emailCol],
                            'type' => $userType
                        ]
                    ];
                }
            } catch (PDOException $e) {
                // Invalid cookie, remove it
                setcookie('remember_user', '', time() - 3600, '/');
            }
        } else if (count($parts) === 2) {
            // Handle old cookie format (backward compatibility)
            $userId = $parts[0];
            $passwordHash = $parts[1];
            
            try {
                // Try customer table first
                $stmt = $pdo->prepare("SELECT id, cus_name, cus_email, password FROM customer WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && hash('sha256', $user['password']) === $passwordHash) {
                    // Restore session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['cus_name'];
                    $_SESSION['user_email'] = $user['cus_email'];
                    $_SESSION['user_type'] = 'customer';
                    $_SESSION['logged_in'] = true;
                    
                    return [
                        'logged_in' => true,
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user['cus_name'],
                            'email' => $user['cus_email'],
                            'type' => 'customer'
                        ]
                    ];
                }
            } catch (PDOException $e) {
                // Invalid cookie, remove it
                setcookie('remember_user', '', time() - 3600, '/');
            }
        }
    }
    
    return ['logged_in' => false];
}

// If this file is accessed directly, return JSON response
if (basename($_SERVER['PHP_SELF']) === 'session_check.php') {
    header('Content-Type: application/json');
    echo json_encode(checkSession());
}

// Function to protect pages that require authentication
function requireLogin($requiredUserType = null) {
    $session = checkSession();
    if (!$session['logged_in']) {
        header('Location: http://localhost/grilli/admin/signin/signinSignOut.php');
        exit;
    }
    
    // Check if specific user type is required
    if ($requiredUserType && $session['user']['type'] !== $requiredUserType) {
        http_response_code(403);
        echo "Access denied. Required user type: " . $requiredUserType;
        exit;
    }
    
    return $session['user'];
}

// Function to protect admin-only pages
function requireAdmin() {
    return requireLogin('admin');
}

// Function to protect customer-only pages
function requireCustomer() {
    return requireLogin('customer');
}

// Function to check if current user is admin
function isAdmin() {
    $session = checkSession();
    return $session['logged_in'] && $session['user']['type'] === 'admin';
}

// Function to check if current user is customer
function isCustomer() {
    $session = checkSession();
    return $session['logged_in'] && $session['user']['type'] === 'customer';
}
?>