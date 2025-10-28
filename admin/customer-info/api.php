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

    // DEFAULT CASE
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid or missing action parameter',
            'available_actions' => [
                'search_customers'
            ]
        ]);
        break;
}
?>