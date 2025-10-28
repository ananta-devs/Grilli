<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile - Grilli Restaurant</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #2c1810 0%, #8b4513 50%, #d2691e 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .profile-container {
            background: linear-gradient(145deg, #fff8f0 0%, #faf5f0 100%);
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(139, 69, 19, 0.3);
            width: 90vw;
            max-width: 450px;
            aspect-ratio: 1;
            padding: 30px;
            text-align: center;
            transform: translateY(0);
            transition: all 0.3s ease;
            border: 2px solid #d2691e;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .profile-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 60px rgba(139, 69, 19, 0.4);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d2691e 0%, #8b4513 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            color: white;
            font-weight: bold;
            box-shadow: 0 10px 25px rgba(139, 69, 19, 0.3);
            border: 3px solid #fff;
        }

        .profile-header h1 {
            color: #2c1810;
            font-size: 1.8rem;
            margin-bottom: 8px;
            font-weight: 600;
            font-family: 'Georgia', serif;
        }

        .profile-header p {
            color: #8b4513;
            font-size: 1rem;
            margin-bottom: 20px;
            font-style: italic;
        }

        .profile-info {
            background: linear-gradient(145deg, #f5f5dc 0%, #fff8dc 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #d2691e;
            flex-grow: 1;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(139, 69, 19, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid #d2691e;
        }

        .info-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(139, 69, 19, 0.2);
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d2691e 0%, #8b4513 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: white;
            font-size: 14px;
        }

        .info-content {
            flex: 1;
            text-align: left;
        }

        .info-label {
            font-size: 0.8rem;
            color: #8b4513;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .info-value {
            font-size: 1rem;
            color: #2c1810;
            font-weight: 500;
        }

        .profile-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-family: 'Georgia', serif;
            flex: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d2691e 0%, #8b4513 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(210, 105, 30, 0.4);
        }

        .btn-secondary {
            background: #f5f5dc;
            color: #2c1810;
            border: 2px solid #d2691e;
        }

        .btn-secondary:hover {
            background: #d2691e;
            color: white;
            transform: translateY(-2px);
        }

        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #8b4513;
            height: 100%;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f5f5dc;
            border-top: 4px solid #d2691e;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error {
            color: #8b0000;
            background: #ffe4e1;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #cd5c5c;
        }



        /* Edit Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(44, 24, 16, 0.8);
        }

        .modal-content {
            background: linear-gradient(145deg, #fff8f0 0%, #faf5f0 100%);
            margin: 15% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(139, 69, 19, 0.5);
            border: 2px solid #d2691e;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            color: #2c1810;
            font-size: 1.5rem;
            font-weight: 600;
            font-family: 'Georgia', serif;
        }

        .close {
            color: #8b4513;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #d2691e;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #2c1810;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #d2691e;
            border-radius: 10px;
            font-size: 1rem;
            background: white;
            color: #2c1810;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #8b4513;
            box-shadow: 0 0 0 3px rgba(210, 105, 30, 0.2);
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .success-message {
            color: #2d5016;
            background: #d4edda;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }

        /* Responsive design for different screen sizes */
        @media (max-width: 768px) {
            .profile-container {
                width: 95vw;
                max-width: 380px;
                padding: 25px;
            }
            
            .profile-avatar {
                width: 70px;
                height: 70px;
                font-size: 30px;
            }
            
            .profile-header h1 {
                font-size: 1.6rem;
            }
            
            .info-item {
                padding: 10px;
            }
            
            .info-icon {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }
            
            .btn {
                padding: 8px 16px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .profile-container {
                width: 95vw;
                max-width: 320px;
                padding: 20px;
            }
            
            .profile-avatar {
                width: 60px;
                height: 60px;
                font-size: 24px;
                margin-bottom: 15px;
            }
            
            .profile-header h1 {
                font-size: 1.4rem;
            }
            
            .profile-header p {
                font-size: 0.9rem;
            }
            
            .info-item {
                padding: 8px;
                margin-bottom: 10px;
            }
            
            .info-value {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
                padding: 20px;
            }
        }

        /* Landscape orientation adjustments */
        @media (max-height: 600px) and (orientation: landscape) {
            .profile-container {
                width: 80vh;
                max-width: 400px;
                padding: 20px;
            }
            
            .profile-avatar {
                width: 60px;
                height: 60px;
                font-size: 24px;
                margin-bottom: 10px;
            }
            
            .profile-header h1 {
                font-size: 1.5rem;
            }
            
            .profile-header p {
                margin-bottom: 15px;
            }
            
            .profile-info {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .info-item {
                padding: 8px;
                margin-bottom: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div id="loading" class="loading">
            <div class="loading-spinner"></div>
            <p>Loading your profile...</p>
        </div>

        <div id="profile-content" style="display: none;">
            <div class="profile-top">
                <div class="profile-avatar">
                    <span id="avatar-initial"></span>
                </div>
                
                <div class="profile-header">
                    <h1 id="user-name"></h1>
                    <p>Welcome to Grilli Restaurant</p>
                </div>
            </div>



            <div class="profile-info">
                <div class="info-item">
                    <div class="info-icon">👤</div>
                    <div class="info-content">
                        <div class="info-label">Full Name</div>
                        <div class="info-value" id="display-name"></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">✉️</div>
                    <div class="info-content">
                        <div class="info-label">Email Address</div>
                        <div class="info-value" id="display-email"></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">📱</div>
                    <div class="info-content">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value" id="display-phone"></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">🆔</div>
                    <div class="info-content">
                        <div class="info-label">Customer ID</div>
                        <div class="info-value" id="display-id"></div>
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn btn-primary" onclick="openEditModal()">Edit Profile</button>
                <button class="btn btn-secondary" onclick="logout()">Logout</button>
            </div>
        </div>

        <div id="error-content" style="display: none;">
            <div class="error">
                <h3>Access Denied</h3>
                <p>You need to be logged in to view this page.</p>
                <div style="margin-top: 20px;">
                    <a href="login.html" class="btn btn-primary">Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Profile</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            
            <div id="success-message" class="success-message" style="display: none;"></div>
            
            <form id="editForm">
                <div class="form-group">
                    <label class="form-label" for="edit-name">Full Name</label>
                    <input type="text" id="edit-name" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="edit-phone">Phone Number</label>
                    <input type="tel" id="edit-phone" class="form-input" required>
                </div>
                
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentUser = null;

        // Function to check session and load user data
        async function loadUserProfile() {
            try {
                const response = await fetch('./assets/php/session_check.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'check_session'
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    currentUser = data.user;
                    displayUserProfile(data.user);
                } else {
                    showError();
                }
            } catch (error) {
                console.error('Error loading profile:', error);
                showError();
            }
        }

        // Function to display user profile data
        function displayUserProfile(user) {
            console.log('User data:', user); // Debug log
            
            // Hide loading
            document.getElementById('loading').style.display = 'none';
            
            // Show profile content
            document.getElementById('profile-content').style.display = 'block';

            // Set avatar initial (first letter of name)
            const initial = user.name ? user.name.charAt(0).toUpperCase() : '?';
            document.getElementById('avatar-initial').textContent = initial;

            // Set user information
            document.getElementById('user-name').textContent = user.name || 'Unknown User';
            document.getElementById('display-name').textContent = user.name || 'Not provided';
            document.getElementById('display-email').textContent = user.email || 'Not provided';
            
            // Handle phone number display with better debugging
            let phoneDisplay = 'Not provided';
            if (user.phone) {
                if (user.phone.trim() !== '') {
                    phoneDisplay = user.phone;
                } else {
                    phoneDisplay = 'Not provided (empty)';
                }
            } else {
                phoneDisplay = 'Not provided (null/undefined)';
            }
            
            console.log('Phone value:', user.phone, 'Display:', phoneDisplay); // Debug log
            document.getElementById('display-phone').textContent = phoneDisplay;
            
            document.getElementById('display-id').textContent = user.id || 'Unknown';
        }

        // Function to show error when not logged in
        function showError() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('error-content').style.display = 'block';
        }

        // Function to open edit modal
        function openEditModal() {
            if (currentUser) {
                document.getElementById('edit-name').value = currentUser.name || '';
                document.getElementById('edit-phone').value = currentUser.phone || '';
                document.getElementById('success-message').style.display = 'none';
                document.getElementById('editModal').style.display = 'block';
            }
        }

        // Function to close edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Function to handle form submission
        document.getElementById('editForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = document.getElementById('edit-name').value.trim();
            const phone = document.getElementById('edit-phone').value.trim();

            if (!name || !phone) {
                alert('Please fill in all fields');
                return;
            }

            try {
                const response = await fetch('./assets/php/update-profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: name,
                        phone: phone
                    })
                });

                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // Get response text first to debug
                const responseText = await response.text();
                console.log('Response text:', responseText);

                // Try to parse as JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (jsonError) {
                    console.error('JSON parse error:', jsonError);
                    console.error('Response was:', responseText);
                    throw new Error('Server returned invalid JSON response');
                }

                if (data.status === 'success') {
                    // Update current user data
                    currentUser.name = name;
                    currentUser.phone = phone;
                    
                    // Update display
                    displayUserProfile(currentUser);
                    
                    // Show success message
                    document.getElementById('success-message').textContent = 'Profile updated successfully!';
                    document.getElementById('success-message').style.display = 'block';
                    
                    // Close modal after 2 seconds
                    setTimeout(() => {
                        closeEditModal();
                    }, 2000);
                } else {
                    alert('Error updating profile: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                alert('Error updating profile: ' + error.message);
            }
        });

        // Function to handle logout
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                fetch('logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(() => {
                    window.location.href = 'signin.php';
                }).catch(() => {
                    window.location.href = 'signin.php';
                });
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }

        // Load user profile when page loads
        document.addEventListener('DOMContentLoaded', loadUserProfile);

        // Refresh profile data every 5 minutes to keep session alive
        setInterval(loadUserProfile, 300000);
    </script>
</body>
</html>