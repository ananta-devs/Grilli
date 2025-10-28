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
                'fetch_tables',
                'add_table',
                'update_table_status',
                'delete_table'
            ]
        ]);
        break;
}
?>