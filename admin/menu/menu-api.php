<?php
    // menu-api.php - Fixed version with AJAX support
    session_start();

    // Database configuration
    $host = 'localhost';
    $dbname = 'grilli';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }

    // Initialize variables
    $message = '';
    $messageType = '';
    $search = trim($_GET['search'] ?? '');
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 10;
    $menuItems = [];
    $totalItems = 0;
    $totalPages = 0;

    // Handle AJAX requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        // Handle AJAX get_items request
        if ($action === 'get_items') {
            header('Content-Type: application/json');
            
            try {
                $search = trim($_POST['search'] ?? '');
                $page = max(1, intval($_POST['page'] ?? 1));
                
                $result = getMenuItems($pdo, $search, $page, $limit);
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'items' => $result['items'],
                        'total' => $result['total'],
                        'pages' => $result['pages']
                    ]
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error loading menu items: ' . $e->getMessage()
                ]);
            }
            exit();
        }
        
        // Handle other form submissions (add, edit, delete)
        try {
            switch ($action) {
                case 'add':
                    $result = addMenuItem($pdo, $_POST, $_FILES);
                    break;
                case 'edit':
                    $result = updateMenuItem($pdo, $_POST, $_FILES);
                    break;
                case 'delete':
                    $result = deleteMenuItem($pdo, $_POST);
                    break;
                default:
                    throw new Exception('Invalid action');
            }
            
            // Return JSON response for AJAX requests
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => $result['success'],
                    'message' => $result['message']
                ]);
                exit();
            }
            
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
            
        } catch (Exception $e) {
            // Return JSON response for AJAX requests
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
                exit();
            }
            
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
        
        // Redirect to prevent form resubmission (for non-AJAX requests)
        $redirectUrl = $_SERVER['PHP_SELF'];
        $params = [];
        if ($search) $params['search'] = $search;
        if ($page > 1) $params['page'] = $page;
        if (!empty($params)) $redirectUrl .= '?' . http_build_query($params);
        
        $_SESSION['message'] = $message;
        $_SESSION['messageType'] = $messageType;
        header("Location: $redirectUrl");
        exit();
    }

    // Get message from session if available
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $messageType = $_SESSION['messageType'];
        unset($_SESSION['message'], $_SESSION['messageType']);
    }

    // Fetch menu items for non-AJAX requests
    try {
        $result = getMenuItems($pdo, $search, $page, $limit);
        $menuItems = $result['items'];
        $totalItems = $result['total'];
        $totalPages = $result['pages'];
    } catch (Exception $e) {
        $message = 'Error loading menu items: ' . $e->getMessage();
        $messageType = 'error';
    }

    // Function to add menu item
    function addMenuItem($pdo, $postData, $files) {
        // Validate required fields
        $requiredFields = ['menu_name', 'menu_type', 'menu_price', 'spice_level'];
        foreach ($requiredFields as $field) {
            if (empty($postData[$field])) {
                throw new Exception("$field is required");
            }
        }
        
        // Validate menu_type
        $validTypes = ['break-fast', 'lunch', 'dinner'];
        if (!in_array($postData['menu_type'], $validTypes)) {
            throw new Exception('Invalid menu type');
        }
        
        // Validate spice_level
        $validSpiceLevels = ['low', 'medium', 'high'];
        if (!in_array($postData['spice_level'], $validSpiceLevels)) {
            throw new Exception('Invalid spice level');
        }
        
        // Validate price
        $price = floatval($postData['menu_price']);
        if ($price < 0 || $price > 999.99) {
            throw new Exception('Price must be between 0 and 999.99');
        }
        
        // Handle image upload
        $imagePath = '';
        if (isset($files['menu_image']) && $files['menu_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = handleImageUpload($files['menu_image']);
        }
        
        // Prepare SQL
        $sql = "INSERT INTO menu (menu_name, menu_type, menu_price, calories, cooking_time, spice_level, menu_img, rating) 
                VALUES (:menu_name, :menu_type, :menu_price, :calories, :cooking_time, :spice_level, :menu_img, :rating)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':menu_name' => trim($postData['menu_name']),
            ':menu_type' => $postData['menu_type'],
            ':menu_price' => $price,
            ':calories' => !empty($postData['calories']) ? intval($postData['calories']) : null,
            ':cooking_time' => trim($postData['cooking_time']) ?: null,
            ':spice_level' => $postData['spice_level'],
            ':menu_img' => $imagePath,
            ':rating' => !empty($postData['rating']) ? floatval($postData['rating']) : null
        ]);
        
        return ['success' => true, 'message' => 'Menu item added successfully'];
    }

    // Function to update menu item
    function updateMenuItem($pdo, $postData, $files) {
        $id = intval($postData['id']);
        if (!$id) {
            throw new Exception('Invalid item ID');
        }
        
        // Check if item exists
        $stmt = $pdo->prepare("SELECT id, menu_img FROM menu WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $existingItem = $stmt->fetch();
        
        if (!$existingItem) {
            throw new Exception('Menu item not found');
        }
        
        // Validate required fields
        $requiredFields = ['menu_name', 'menu_type', 'menu_price', 'spice_level'];
        foreach ($requiredFields as $field) {
            if (empty($postData[$field])) {
                throw new Exception("$field is required");
            }
        }
        
        // Validate menu_type
        $validTypes = ['break-fast', 'lunch', 'dinner'];
        if (!in_array($postData['menu_type'], $validTypes)) {
            throw new Exception('Invalid menu type');
        }
        
        // Validate spice_level
        $validSpiceLevels = ['low', 'medium', 'high'];
        if (!in_array($postData['spice_level'], $validSpiceLevels)) {
            throw new Exception('Invalid spice level');
        }
        
        // Validate price
        $price = floatval($postData['menu_price']);
        if ($price < 0 || $price > 999.99) {
            throw new Exception('Price must be between 0 and 999.99');
        }
        
        // Handle image upload - FIXED: Better file handling
        $imagePath = $existingItem['menu_img']; // Keep existing image by default
        
        // Check if a new file was uploaded
        if (isset($files['menu_image']) && is_array($files['menu_image']) && $files['menu_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $newImagePath = handleImageUpload($files['menu_image']);
                // Delete old image if it exists and is not a placeholder
                if ($imagePath && file_exists($imagePath) && strpos($imagePath, 'data:image') === false) {
                    @unlink($imagePath); // Use @ to suppress warnings if file doesn't exist
                }
                $imagePath = $newImagePath;
            } catch (Exception $e) {
                throw new Exception('Image upload failed: ' . $e->getMessage());
            }
        }
        
        // Prepare SQL
        $sql = "UPDATE menu SET 
                menu_name = :menu_name, 
                menu_type = :menu_type, 
                menu_price = :menu_price, 
                calories = :calories, 
                cooking_time = :cooking_time, 
                spice_level = :spice_level, 
                menu_img = :menu_img, 
                rating = :rating 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':id' => $id,
            ':menu_name' => trim($postData['menu_name']),
            ':menu_type' => $postData['menu_type'],
            ':menu_price' => $price,
            ':calories' => !empty($postData['calories']) ? intval($postData['calories']) : null,
            ':cooking_time' => !empty($postData['cooking_time']) ? trim($postData['cooking_time']) : null,
            ':spice_level' => $postData['spice_level'],
            ':menu_img' => $imagePath,
            ':rating' => !empty($postData['rating']) ? floatval($postData['rating']) : null
        ]);
        
        if (!$result) {
            throw new Exception('Failed to update menu item');
        }
        
        return ['success' => true, 'message' => 'Menu item updated successfully'];
    }

        // Function to delete menu item
    function deleteMenuItem($pdo, $postData) {
        $id = intval($postData['id']);
        if (!$id) {
            throw new Exception('Invalid item ID');
        }
        
        // Start transaction for data integrity
        $pdo->beginTransaction();
        
        try {
            // Get item details for image deletion
            $stmt = $pdo->prepare("SELECT menu_name, menu_img FROM menu WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $item = $stmt->fetch();
            
            if (!$item) {
                throw new Exception('Menu item not found');
            }
            
            // Delete the item
            $stmt = $pdo->prepare("DELETE FROM menu WHERE id = :id");
            $result = $stmt->execute([':id' => $id]);
            
            if (!$result) {
                throw new Exception('Failed to delete menu item from database');
            }
            
            // Check if any rows were affected
            if ($stmt->rowCount() === 0) {
                throw new Exception('Menu item not found or already deleted');
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Delete associated image file if it exists (after successful DB deletion)
            if ($item['menu_img'] && 
                file_exists($item['menu_img']) && 
                strpos($item['menu_img'], 'data:image') === false) {
                @unlink($item['menu_img']); // Use @ to suppress warnings
            }
            
            return [
                'success' => true, 
                'message' => "Menu item '{$item['menu_name']}' deleted successfully"
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollback();
            throw $e;
        }
    }

    // Function to get menu items with pagination and search
    function getMenuItems($pdo, $search = '', $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause for search
        $whereClause = '';
        $params = [];
        
        if ($search) {
            $whereClause = "WHERE menu_name LIKE :search OR menu_type LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM menu $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalItems = $countStmt->fetch()['total'];
        
        // Get items
        $sql = "SELECT * FROM menu $whereClause ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        
        // Bind search parameter if exists
        if ($search) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $items = $stmt->fetchAll();
        $totalPages = ceil($totalItems / $limit);
        
        return [
            'items' => $items,
            'total' => $totalItems,
            'pages' => $totalPages
        ];
    }

    // Function to handle image upload
    function handleImageUpload($file) {
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
        }
        
        if ($file['size'] > $maxSize) {
            throw new Exception('File size too large. Maximum 5MB allowed.');
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = 'images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('menu_') . '.' . $extension;
        $uploadPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload file');
        }
        
        return $uploadPath;
    }

    // Helper functions
    function getImageSrc($item) {
        if (!empty($item['menu_img']) && file_exists($item['menu_img'])) {
            return $item['menu_img'];
        }
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIGZpbGw9IiNmMGYwZjAiPjx0ZXh0IHg9IjI1IiB5PSIzMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1zaXplPSIxMCI+Tm8gSW1hZ2U8L3RleHQ+PC9zdmc+';
    }

    function getPaginationUrl($pageNum, $search = '') {
        $url = $_SERVER['PHP_SELF'] . '?page=' . $pageNum;
        if ($search) $url .= '&search=' . urlencode($search);
        return $url;
    }
?>