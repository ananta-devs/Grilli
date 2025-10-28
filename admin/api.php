<?php
// merged-api.php - Unified API for all operations including dashboard data
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
class Database {
    private $host = "127.0.0.1";
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
    
    // DASHBOARD DATA OPERATIONS
    case 'get_dashboard_data':
        try {
            // Get total tables
            $stmt = $pdo->query("SELECT COUNT(*) FROM table_persons_mapping");
            $totalTables = $stmt->fetchColumn();
            
            // Get available tables
            $stmt = $pdo->query("SELECT COUNT(*) FROM table_persons_mapping WHERE status = 'available'");
            $availableTables = $stmt->fetchColumn();
            
            // Get today's bookings
            $stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE reservation_date = CURDATE()");
            $todaysBookings = $stmt->fetchColumn();
            
            // Get total customers
            $stmt = $pdo->query("SELECT COUNT(*) FROM customer");
            $totalCustomers = $stmt->fetchColumn();
            
            // Get recent activity
            $stmt = $pdo->query("
                SELECT name, phone, table_no, reservation_date, time_slot, status, created_at 
                FROM reservations 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'totalTables' => $totalTables,
                    'availableTables' => $availableTables,
                    'todaysBookings' => $todaysBookings,
                    'totalCustomers' => $totalCustomers,
                    'recentActivity' => $recentActivity
                ]
            ]);
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
        break;
    
    // CUSTOMER OPERATIONS
    case 'search_customers':
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            $sql = "SELECT id, cus_name, cus_email, cus_ph, total_visit FROM customer";
            
            if (!empty($search)) {
                $sql .= " WHERE cus_name LIKE :search OR cus_email LIKE :search OR cus_ph LIKE :search";
            }
            
            $sql .= " ORDER BY id ASC";
            
            $stmt = $pdo->prepare($sql);
            
            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search', $searchParam);
            }
            
            $stmt->execute();
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $customers,
                'total' => count($customers),
                'search' => $search
            ]);
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
        break;

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
            
            $update_sql = "UPDATE reservations SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($update_sql);
            $stmt->execute([$status, $booking_id]);
            
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error updating status: ' . $e->getMessage()]);
        }
        break;

    // TABLE OPERATIONS
    case 'fetch_tables':
        try {
            $query = "SELECT * FROM table_persons_mapping ORDER BY table_no";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $tables
            ]);
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
        break;

    case 'add_table':
        try {
            $table_no = $_POST['table_no'] ?? '';
            $max_persons = $_POST['max_persons'] ?? '';
            
            if (empty($table_no) || empty($max_persons)) {
                echo json_encode(["success" => false, "message" => "All fields are required"]);
                exit;
            }
            
            // Check if table number already exists
            $check_query = "SELECT COUNT(*) FROM table_persons_mapping WHERE table_no = ?";
            $check_stmt = $pdo->prepare($check_query);
            $check_stmt->execute([$table_no]);
            
            if ($check_stmt->fetchColumn() > 0) {
                echo json_encode(["success" => false, "message" => "Table number already exists"]);
                exit;
            }
            
            $query = "INSERT INTO table_persons_mapping (table_no, max_persons, status) VALUES (?, ?, 'available')";
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([$table_no, $max_persons]);
            
            if ($result) {
                echo json_encode(["success" => true, "message" => "Table added successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to add table"]);
            }
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
        break;

    case 'update_table_status':
        try {
            $id = $_POST['id'] ?? '';
            $status = $_POST['status'] ?? '';
            
            if (empty($id) || empty($status)) {
                echo json_encode(["success" => false, "message" => "ID and status are required"]);
                exit;
            }
            
            $query = "UPDATE table_persons_mapping SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([$status, $id]);
            
            if ($result) {
                echo json_encode(["success" => true, "message" => "Table status updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update table status"]);
            }
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
        break;

    case 'delete_table':
        try {
            $id = $_POST['id'] ?? '';
            
            if (empty($id)) {
                echo json_encode(["success" => false, "message" => "Table ID is required"]);
                exit;
            }
            
            $query = "DELETE FROM table_persons_mapping WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode(["success" => true, "message" => "Table deleted successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete table"]);
            }
            
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
        break;

    // DEFAULT CASE
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid or missing action parameter',
            'available_actions' => [
                'get_dashboard_data',
                'search_customers',
                'fetch_bookings', 
                'update_booking_status',
                'fetch_tables',
                'add_table',
                'update_table_status',
                'delete_table'
            ]
        ]);
        break;
}
?>