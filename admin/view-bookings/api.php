<?php
// merged-api.php - Unified API for all operations including dashboard data
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
class Database {
    private $host = "localhost";
    private $db_name = "grilli";
    private $username = "root"; // Change as needed
    private $password = ""; // Change as needed
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            http_response_code(500);
            echo json_encode(['error' => 'Connection failed: ' . $exception->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}

// Get database connection
$database = new Database();
$pdo = $database->getConnection();

// Get action parameter
$action = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    } elseif (isset($input['action'])) {
        $action = $input['action'];
    }
}

// Main switch case for all operations
switch ($action) {
    
    // BOOKING OPERATIONS
    case 'fetch_bookings':
        try {
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            $sql = "SELECT * FROM reservations WHERE 1=1";

            switch($filter) {
                case 'today':
                    $sql .= " AND reservation_date = CURDATE()";
                    break;
                case 'tomorrow':
                    $sql .= " AND reservation_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
                    break;
                case 'pending':
                    $sql .= " AND status = 'pending'";
                    break;
                case 'confirmed':
                    $sql .= " AND status = 'confirmed'";
                    break;
                case 'cancelled':
                    $sql .= " AND status = 'cancelled'";
                    break;
                case 'completed':
                    $sql .= " AND status = 'completed'";
                    break;
            }

            $sql .= " ORDER BY reservation_date ASC, time_slot ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $bookings,
                'total' => count($bookings),
                'filter' => $filter
            ]);
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
        break;

    case 'update_booking_status':
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['booking_id']) || !isset($input['status'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Booking ID and status are required']);
                exit;
            }
            
            $booking_id = intval($input['booking_id']);
            $status = $input['status'];
            
            // Validate status
            $valid_statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
            if (!in_array($status, $valid_statuses)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid status']);
                exit;
            }
            
            // Start transaction for data consistency
            $pdo->beginTransaction();
            
            try {
                // Get user_id from reservation
                $check_sql = "SELECT user_id FROM reservations WHERE id = ?";
                $check_stmt = $pdo->prepare($check_sql);
                $check_stmt->execute([$booking_id]);
                $current_booking = $check_stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$current_booking) {
                    throw new Exception('Booking not found');
                }
                
                $user_id = $current_booking['user_id'];
                
                // Update booking status
                $update_sql = "UPDATE reservations SET status = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $pdo->prepare($update_sql);
                $stmt->execute([$status, $booking_id]);
                
                // If status is 'completed', increment total_visit for the customer
                if ($status === 'completed') {
                    // Increment total_visit for the customer
                    $visit_sql = "UPDATE customer SET total_visit = total_visit + 1, updated_at = NOW() WHERE id = ?";
                    $visit_stmt = $pdo->prepare($visit_sql);
                    $visit_stmt->execute([$user_id]);
                    
                    // Check if customer was found and updated
                    if ($visit_stmt->rowCount() === 0) {
                        throw new Exception('Customer not found for visit increment');
                    }
                }
                
                // Commit transaction
                $pdo->commit();
                
                $response = ['success' => true, 'message' => 'Status updated successfully'];
                
                // Add additional info if visit was incremented
                if ($status === 'completed') {
                    $response['visit_incremented'] = true;
                    $response['customer_id'] = $user_id;
                }
                
                echo json_encode($response);
                
            } catch(Exception $e) {
                // Rollback transaction on error
                $pdo->rollback();
                throw $e;
            }
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error updating status: ' . $e->getMessage()]);
        }
        break;

    // DEFAULT CASE
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid or missing action parameter',
            'available_actions' => [
                'fetch_bookings', 
                'update_booking_status'
            ]
        ]);
        break;
}
?>