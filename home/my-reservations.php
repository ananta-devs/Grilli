<?php
    session_start();

    // Function to check session using session_check.php logic
    function checkUserSession() {
        // Check if user is logged in
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && $_SESSION['logged_in'] === true) {
            return [
                'status' => 'success',
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'name' => $_SESSION['user_name'],
                    'email' => $_SESSION['user_email'] ?? '',
                    'phone' => $_SESSION['user_phone'] ?? ''
                ]
            ];
        } else {
            // Check remember me cookie if session is not active
            if (isset($_COOKIE['remember_user']) && !empty($_COOKIE['remember_user'])) {
                // Database connection for cookie verification
                $host = 'localhost';
                $dbname = 'grilli';
                $username = 'root';
                $password = '';
                
                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $cookieValue = base64_decode($_COOKIE['remember_user']);
                    $parts = explode(':', $cookieValue);
                    
                    if (count($parts) === 2) {
                        $userId = $parts[0];
                        $hashedPassword = $parts[1];
                        
                        // Verify cookie with database
                        $stmt = $pdo->prepare("SELECT id, cus_name, cus_email, cus_ph, password FROM customer WHERE id = ?");
                        $stmt->execute([$userId]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($user && hash('sha256', $user['password']) === $hashedPassword) {
                            // Restore session
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user_name'] = $user['cus_name'];
                            $_SESSION['user_email'] = $user['cus_email'];
                            $_SESSION['user_phone'] = $user['cus_ph'];
                            $_SESSION['logged_in'] = true;
                            
                            return [
                                'status' => 'success',
                                'user' => [
                                    'id' => $user['id'],
                                    'name' => $user['cus_name'],
                                    'email' => $user['cus_email'],
                                    'phone' => $user['cus_ph']
                                ]
                            ];
                        }
                    }
                } catch (Exception $e) {
                    // Cookie is invalid, clear it
                    setcookie('remember_user', '', time() - 3600, '/');
                }
            }
            
            return [
                'status' => 'error',
                'message' => 'No active session'
            ];
        }
    }

    // Check user session
    $sessionResult = checkUserSession();

    // If no valid session, redirect to signin
    if ($sessionResult['status'] !== 'success') {
        header('Location: signin.php');
        exit();
    }

    // Get user data from session check
    $currentUser = $sessionResult['user'];
    $user_id = $currentUser['id'];

    // Database connection
    $host = 'localhost';
    $dbname = 'grilli';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    $message = '';

    // Handle cancellation request
    if (isset($_POST['action']) && $_POST['action'] === 'cancel' && isset($_POST['reservation_id'])) {
        $reservation_id = $_POST['reservation_id'];
        
        // Check if reservation can be cancelled (confirmed status and within 24 hours)
        $check_sql = "SELECT * FROM reservations WHERE id = ? AND user_id = ? AND status = 'confirmed'";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$reservation_id, $user_id]);
        $reservation = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reservation) {
            $created_time = strtotime($reservation['created_at']);
            $current_time = time();
            $time_diff = $current_time - $created_time;
            
            // Check if within 24 hours (86400 seconds)
            if ($time_diff <= 86400) {
                $update_sql = "UPDATE reservations SET status = 'cancelled', updated_at = NOW() WHERE id = ? AND user_id = ?";
                $update_stmt = $pdo->prepare($update_sql);
                
                if ($update_stmt->execute([$reservation_id, $user_id])) {
                    $message = "<div class='alert alert-success'>Reservation cancelled successfully!</div>";
                } else {
                    $message = "<div class='alert alert-error'>Error cancelling reservation. Please try again.</div>";
                }
            } else {
                $message = "<div class='alert alert-error'>Cannot cancel reservation. Cancellation is only allowed within 24 hours of booking.</div>";
            }
        } else {
            $message = "<div class='alert alert-error'>Invalid reservation or reservation cannot be cancelled.</div>";
        }
    }

    // Fetch user's reservations
    $sql = "SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC, created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Grilli Restaurant</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .user-info {
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            margin-top: 15px;
            border-radius: 8px;
            font-size: 0.9em;
        }

        .content {
            padding: 30px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: bold;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .reservations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .reservation-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid #667eea;
        }

        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .reservation-card.confirmed {
            border-left-color: #28a745;
        }

        .reservation-card.pending {
            border-left-color: #ffc107;
        }

        .reservation-card.cancelled {
            border-left-color: #dc3545;
            opacity: 0.7;
        }

        .reservation-card.completed {
            border-left-color: #17a2b8;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .reservation-id {
            font-size: 0.9em;
            color: #666;
            font-weight: bold;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-confirmed {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .status-cancelled {
            background-color: #dc3545;
            color: white;
        }

        .status-completed {
            background-color: #17a2b8;
            color: white;
        }

        .card-details {
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 8px;
            align-items: center;
        }

        .detail-label {
            font-weight: bold;
            color: #555;
            min-width: 80px;
            margin-right: 10px;
        }

        .detail-value {
            color: #333;
            flex: 1;
        }

        .cancel-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .cancel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }

        .cancel-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .no-reservations {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }

        .no-reservations h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .modal-buttons {
            margin-top: 20px;
        }

        .modal-btn {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .confirm-btn {
            background: #dc3545;
            color: white;
        }

        .cancel-modal-btn {
            background: #6c757d;
            color: white;
        }

        @media (max-width: 768px) {
            .reservations-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>My Reservations</h1>
            <p>Manage your restaurant reservations</p>
            <div class="user-info">
                Welcome, <?php echo htmlspecialchars($currentUser['name']); ?>
                <?php if (!empty($currentUser['email'])): ?>
                    | <?php echo htmlspecialchars($currentUser['email']); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="content">
            
            <?php echo $message; ?>

            <?php if (empty($reservations)): ?>
                <div class="no-reservations">
                    <h3>No Reservations Found</h3>
                    <p>You haven't made any reservations yet. <a href="book-table.php">Book a table now!</a></p>
                </div>
            <?php else: ?>
                <div class="reservations-grid">
                    <?php foreach ($reservations as $reservation): ?>
                        <div class="reservation-card <?php echo $reservation['status']; ?>">
                            <div class="card-header">
                                <span class="reservation-id">ID: #<?php echo $reservation['id']; ?></span>
                                <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                    <?php echo ucfirst($reservation['status']); ?>
                                </span>
                            </div>

                            <div class="card-details">
                                <div class="detail-row">
                                    <span class="detail-label">Name:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($reservation['name']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Date:</span>
                                    <span class="detail-value"><?php echo date('F j, Y', strtotime($reservation['reservation_date'])); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Time:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($reservation['time_slot']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Persons:</span>
                                    <span class="detail-value"><?php echo $reservation['persons']; ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Phone:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($reservation['phone']); ?></span>
                                </div>
                                <?php if ($reservation['table_no']): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Table:</span>
                                        <span class="detail-value"><?php echo $reservation['table_no']; ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($reservation['message']): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Message:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($reservation['message']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="detail-row">
                                    <span class="detail-label">Booked:</span>
                                    <span class="detail-value"><?php echo date('M j, Y g:i A', strtotime($reservation['created_at'])); ?></span>
                                </div>
                            </div>

                            <?php
                            $can_cancel = false;
                            if ($reservation['status'] === 'confirmed') {
                                $created_time = strtotime($reservation['created_at']);
                                $current_time = time();
                                $time_diff = $current_time - $created_time;
                                $can_cancel = $time_diff <= 86400; // 24 hours
                            }
                            ?>

                            <?php if ($can_cancel): ?>
                                <button class="cancel-btn" onclick="showCancelModal(<?php echo $reservation['id']; ?>)">
                                    Cancel Reservation
                                </button>
                            <?php elseif ($reservation['status'] === 'confirmed'): ?>
                                <button class="cancel-btn" disabled>
                                    Cannot Cancel (24hr limit exceeded)
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="confirmation-modal" id="cancelModal">
        <div class="modal-content">
            <h3>Cancel Reservation</h3>
            <p>Are you sure you want to cancel this reservation? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="modal-btn confirm-btn" onclick="confirmCancel()">Yes, Cancel</button>
                <button class="modal-btn cancel-modal-btn" onclick="closeCancelModal()">No, Keep It</button>
            </div>
        </div>
    </div>

    <script>
        let reservationToCancel = null;

        function showCancelModal(reservationId) {
            reservationToCancel = reservationId;
            document.getElementById('cancelModal').style.display = 'block';
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
            reservationToCancel = null;
        }

        function confirmCancel() {
            if (reservationToCancel) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'cancel';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'reservation_id';
                idInput.value = reservationToCancel;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('cancelModal');
            if (event.target === modal) {
                closeCancelModal();
            }
        }

        // Add loading animation to cancel buttons
        document.querySelectorAll('.cancel-btn:not([disabled])').forEach(btn => {
            btn.addEventListener('click', function() {
                this.innerHTML = 'Processing...';
                this.disabled = true;
            });
        });
    </script>
</body>
</html>