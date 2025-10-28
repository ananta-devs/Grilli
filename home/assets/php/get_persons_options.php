<?php
// get_persons_options.php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    
    // Fetch available tables with their maximum person capacity
    $stmt = $pdo->prepare("
        SELECT table_no, max_persons 
        FROM table_persons_mapping 
        WHERE status = 'available' 
        ORDER BY max_persons ASC
    ");
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create unique person count options
    $personOptions = [];
    $seenPersons = [];
    
    foreach ($results as $row) {
        if (!in_array($row['max_persons'], $seenPersons)) {
            $personOptions[] = [
                'max_persons' => $row['max_persons'],
                'table_no' => $row['table_no']
            ];
            $seenPersons[] = $row['max_persons'];
        }
    }
    
    echo json_encode($personOptions);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>