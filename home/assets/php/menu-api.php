<?php
// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'grilli';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password

// Image directory configuration
// Physical path for file existence checks (from document root)
$image_dir = $_SERVER['DOCUMENT_ROOT'] . '/grilli/admin/menu/images/';

// Web URL for accessing images
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host_url = $protocol . $_SERVER['HTTP_HOST'];

// Web path to images (from web root) - FIXED: Added /grilli/
$image_web_path = '/grilli/admin/menu/images/';
$base_url = $host_url . $image_web_path;

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the type parameter from URL
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    
    // Validate the type parameter
    $valid_types = ['break-fast', 'lunch', 'dinner'];
    
    if (empty($type)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Type parameter is required',
            'valid_types' => $valid_types
        ]);
        exit;
    }
    
    if (!in_array($type, $valid_types)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid type parameter',
            'provided_type' => $type,
            'valid_types' => $valid_types
        ]);
        exit;
    }
    
    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_type = ? ORDER BY id ASC");
    $stmt->execute([$type]);
    
    // Fetch all matching records
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Function to get image information from database filename
    function getImageInfo($menu_img, $image_dir, $base_url) {
        if (empty($menu_img)) {
            return null;
        }
        
        // Clean the filename - remove any path components
        $filename = basename($menu_img);
        
        // Remove any query parameters or fragments
        $filename = preg_replace('/[?#].*$/', '', $filename);
        
        // Construct the full file path
        $filepath = rtrim($image_dir, '/') . '/' . $filename;
        
        // Check if file exists
        if (file_exists($filepath)) {
            return [
                'filename' => $filename,
                'url' => rtrim($base_url, '/') . '/' . $filename,
                'path' => $filepath,
                'size' => filesize($filepath),
                'exists' => true
            ];
        }
        
        return [
            'filename' => $filename,
            'url' => rtrim($base_url, '/') . '/' . $filename,
            'path' => $filepath,
            'size' => 0,
            'exists' => false
        ];
    }
    
    // Function to get spice level display info
    function getSpiceLevelInfo($spice_level) {
        $spice_levels = [
            'low' => ['emoji' => '🌶️', 'color' => '#4CAF50', 'label' => 'Mild'],
            'medium' => ['emoji' => '🌶️🌶️', 'color' => '#FF9800', 'label' => 'Medium'],
            'high' => ['emoji' => '🌶️🌶️🌶️', 'color' => '#F44336', 'label' => 'Spicy']
        ];
        
        return isset($spice_levels[$spice_level]) ? $spice_levels[$spice_level] : [
            'emoji' => '🌶️',
            'color' => '#666',
            'label' => ucfirst($spice_level)
        ];
    }
    
    // Convert numeric fields and add image information
    foreach ($menu_items as &$item) {
        $item['id'] = (int)$item['id'];
        $item['calories'] = (int)$item['calories'];
        $item['menu_price'] = (int)$item['menu_price'];
        $item['rating'] = (float)$item['rating'];
        
        // Get image information from database
        $image_info = getImageInfo($item['menu_img'], $image_dir, $base_url);
        $item['image'] = $image_info;
        
        // Add spice level information
        $item['spice_info'] = getSpiceLevelInfo($item['spice_level']);
        
        // Add menu_description field if it doesn't exist
        if (!isset($item['menu_description'])) {
            $item['menu_description'] = '';
        }
    }
    
    // Get total count of images found
    $images_found = count(array_filter($menu_items, function($item) {
        return $item['image'] !== null && $item['image']['exists'];
    }));
    
    // Return successful response
    echo json_encode([
        'success' => true,
        'type' => $type,
        'count' => count($menu_items),
        'images_found' => $images_found,
        'image_base_url' => $base_url,
        'image_directory' => $image_dir,
        'debug_info' => [
            'script_path' => $_SERVER['PHP_SELF'],
            'document_root' => $_SERVER['DOCUMENT_ROOT'],
            'calculated_base_url' => $base_url,
            'physical_image_dir' => $image_dir,
            'image_web_path' => $image_web_path,
            'sample_files' => is_dir($image_dir) ? array_slice(scandir($image_dir), 2, 5) : ['Directory not found']
        ],
        'data' => $menu_items
    ]);
    
} catch (PDOException $e) {
    // Handle database connection errors
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    // Handle other errors
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
}
?>