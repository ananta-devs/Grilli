<?php
// Grilli Restaurant Database Installer
// This script creates the database and tables for the Grilli restaurant system

// Database configuration
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'grilli'
];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $db_config['host'] = $_POST['db_host'] ?? 'localhost';
    $db_config['username'] = $_POST['db_username'] ?? 'root';
    $db_config['password'] = $_POST['db_password'] ?? '';
    $db_config['database'] = $_POST['db_name'] ?? 'grilli';
    
    try {
        // First, connect without database to create it
        $pdo = new PDO("mysql:host={$db_config['host']}", $db_config['username'], $db_config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "<div class='success'>✓ Database '{$db_config['database']}' created successfully!</div>";
        
        // Now connect to the specific database
        $pdo = new PDO("mysql:host={$db_config['host']};dbname={$db_config['database']}", $db_config['username'], $db_config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // SQL commands to create tables
        $sql_commands = [
            // Admin table
            "CREATE TABLE IF NOT EXISTS `admin` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `adm_name` text NOT NULL,
                `adm_email` text NOT NULL,
                `adm_ph` int(11) NOT NULL,
                `password` text NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            // Customer table
            "CREATE TABLE IF NOT EXISTS `customer` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `cus_name` varchar(255) NOT NULL,
                `cus_email` varchar(255) NOT NULL,
                `cus_ph` varchar(20) DEFAULT NULL,
                `total_visit` int(11) NOT NULL DEFAULT 0,
                `password` varchar(255) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `cus_email` (`cus_email`),
                KEY `idx_email` (`cus_email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            // Menu table
            "CREATE TABLE IF NOT EXISTS `menu` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `menu_name` text NOT NULL,
                `menu_type` enum('break-fast','lunch','dinner') NOT NULL,
                `calories` decimal(10,0) NOT NULL,
                `menu_price` decimal(10,0) NOT NULL,
                `cooking_time` text NOT NULL,
                `spice_level` enum('low','medium','high') NOT NULL,
                `menu_img` text NOT NULL,
                `rating` float NOT NULL DEFAULT 0,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            // Table persons mapping
            "CREATE TABLE IF NOT EXISTS `table_persons_mapping` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `table_no` int(11) NOT NULL,
                `max_persons` int(11) NOT NULL,
                `status` enum('available','occupied','reserved','maintenance') NOT NULL DEFAULT 'available',
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `table_no` (`table_no`),
                KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            // Reservations table
            "CREATE TABLE IF NOT EXISTS `reservations` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `phone` varchar(20) NOT NULL,
                `persons` int(11) NOT NULL,
                `reservation_date` date NOT NULL,
                `time_slot` varchar(20) NOT NULL,
                `message` text DEFAULT NULL,
                `table_no` int(11) DEFAULT NULL,
                `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                `user_id` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_reservation_date` (`reservation_date`),
                KEY `idx_status` (`status`),
                KEY `idx_table_no` (`table_no`),
                KEY `fk_reservations_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        ];
        
        // Execute table creation commands
        foreach ($sql_commands as $sql) {
            $pdo->exec($sql);
        }
        echo "<div class='success'>✓ All tables created successfully!</div>";
        
        // Add foreign key constraints
        $constraint_commands = [
            "ALTER TABLE `reservations` ADD CONSTRAINT `fk_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `customer` (`id`) ON DELETE SET NULL",
            "ALTER TABLE `reservations` ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`table_no`) REFERENCES `table_persons_mapping` (`table_no`) ON DELETE SET NULL"
        ];
        
        foreach ($constraint_commands as $sql) {
            try {
                $pdo->exec($sql);
            } catch (PDOException $e) {
                // Ignore if constraint already exists
                if (strpos($e->getMessage(), 'Duplicate key name') === false) {
                    throw $e;
                }
            }
        }
        echo "<div class='success'>✓ Foreign key constraints added successfully!</div>";
        
        // Insert default admin data
        $admin_sql = "INSERT IGNORE INTO `admin` (`id`, `adm_name`, `adm_email`, `adm_ph`, `password`) 
              VALUES (1, 'Admin', 'admin@gmail.com', '0', '\$2y\$10\$NLAECICq/9LAXnowQChPI.4.JFGbrFn.k1VqApxqksdiZKvg2VDzK')";
        $pdo->exec($admin_sql);
        echo "<div class='success'>✓ Default admin user created!</div>";
        
        
        echo "<div class='success-final'>🎉 Installation completed successfully! You can now use your Grilli restaurant system.</div>";
        echo "<div class='info'>Default Admin Login: admin@gmail.com / Admin123</div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Database Error: " . $e->getMessage() . "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grilli Restaurant - Database Installer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 1.1em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .install-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .install-btn:hover {
            transform: translateY(-2px);
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
        }
        
        .success-final {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            border: 1px solid #bee5eb;
            text-align: center;
            font-weight: bold;
            font-size: 1.1em;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
        
        .info {
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ffeaa7;
            text-align: center;
        }
        
        .note {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .note h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .note p {
            color: #666;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍽️ Grilli</h1>
            <p>Database Installation</p>
        </div>
        
        <?php if ($_SERVER['REQUEST_METHOD'] != 'POST'): ?>
        <div class="note">
            <h3>📋 Installation Notes</h3>
            <p>This installer will create the database and all required tables for your Grilli restaurant system. Make sure your MySQL server is running and you have the correct credentials.</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="db_host">Database Host:</label>
                <input type="text" id="db_host" name="db_host" value="localhost" required>
            </div>
            
            <div class="form-group">
                <label for="db_username">Database Username:</label>
                <input type="text" id="db_username" name="db_username" value="root" required>
            </div>
            
            <div class="form-group">
                <label for="db_password">Database Password:</label>
                <input type="password" id="db_password" name="db_password" placeholder="Leave blank if no password">
            </div>
            
            <div class="form-group">
                <label for="db_name">Database Name:</label>
                <input type="text" id="db_name" name="db_name" value="grilli" required>
            </div>
            
            <button type="submit" class="install-btn">🚀 Install Database & Tables</button>
        </form>
        <?php endif; ?>
        
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($error)): ?>
        <div style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='install.php'" class="install-btn" style="background: #28a745;">
                🔄 Run Another Installation
            </button>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>