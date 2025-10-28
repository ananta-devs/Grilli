<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delicious Bites - Sign In</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff6b35, #f7931e, #ffd23f);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #2c3e50;
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo p {
            color: #7f8c8d;
            font-size: 0.9em;
            font-style: italic;
        }

        .auth-tabs {
            display: flex;
            margin-bottom: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }

        .tab-btn {
            flex: 1;
            background: none;
            border: none;
            padding: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #6c757d;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }

        .form-container {
            position: relative;
        }

        .form {
            display: none;
        }

        .form.active {
            display: block;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.9em;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
            transform: translateY(-1px);
        }

        .form-group input::placeholder {
            color: #adb5bd;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }

        .checkbox-group label {
            margin-bottom: 0;
            font-weight: normal;
            font-size: 0.9em;
            color: #6c757d;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.3);
        }

        .submit-btn:hover:not(:disabled)::before {
            left: 100%;
        }

        .back-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9em;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #ff6b35;
            text-decoration: none;
            font-size: 0.9em;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .forgot-password a:hover {
            color: #e55a2b;
            text-decoration: underline;
        }

        .social-divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #6c757d;
            font-size: 0.9em;
        }

        .social-divider::before,
        .social-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #dee2e6;
        }

        .social-divider span {
            padding: 0 15px;
        }

        .social-buttons {
            display: flex;
            gap: 10px;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            text-decoration: none;
            color: #495057;
        }

        .social-btn:hover {
            border-color: #ff6b35;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .google-btn:hover {
            background: #db4437;
            color: white;
            border-color: #db4437;
        }

        .facebook-btn:hover {
            background: #3b5998;
            color: white;
            border-color: #3b5998;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .logo h1 {
                font-size: 2em;
            }
            
            .social-buttons {
                flex-direction: column;
            }
        }

        .success-message {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
            animation: slideDown 0.3s ease;
        }

        .error-message {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .otp-input-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }

        .otp-input {
            width: 50px !important;
            height: 50px;
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            border: 2px solid #e9ecef !important;
            border-radius: 10px !important;
            padding: 0 !important;
        }

        .otp-input:focus {
            border-color: #ff6b35 !important;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1) !important;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #2c3e50;
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #6c757d;
            font-size: 0.9em;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <h1>🍽️ Grilli</h1>
        </div>

        <div class="success-message" id="successMessage">
            Welcome! Your account has been created successfully.
        </div>

        <div class="error-message" id="errorMessage">
            Something went wrong. Please try again.
        </div>

        <div class="auth-tabs" id="authTabs">
            <button class="tab-btn active" onclick="switchTab('signin')">Sign In</button>
            <button class="tab-btn" onclick="switchTab('signup')">Sign Up</button>
        </div>

        <div class="form-container">
            <!-- Sign In Form -->
            <form class="form active" id="signinForm">
                <div class="form-group">
                    <label for="signinEmail">Email Address</label>
                    <input type="email" id="signinEmail" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="signinPassword">Password</label>
                    <input type="password" id="signinPassword" placeholder="Enter your password" required>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="rememberMe">
                    <label for="rememberMe">Remember me</label>
                </div>
                <button type="submit" class="submit-btn">Sign In</button>
                <div class="forgot-password">
                    <a onclick="showForgotPassword()">Forgot your password?</a>
                </div>
            </form>

            <!-- Sign Up Form -->
            <form class="form" id="signupForm">
                <div class="form-group">
                    <label for="signupName">Full Name</label>
                    <input type="text" id="signupName" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="signupEmail">Email Address</label>
                    <input type="email" id="signupEmail" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="signupPhone">Phone Number</label>
                    <input type="tel" id="signupPhone" placeholder="Enter your phone number">
                </div>
                <div class="form-group">
                    <label for="signupPassword">Password</label>
                    <input type="password" id="signupPassword" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="submit-btn">Create Account</button>
            </form>

            <!-- Forgot Password Form -->
            <form class="form" id="forgotPasswordForm">
                <div class="form-header">
                    <h2>Reset Password</h2>
                    <p>Enter your email address and we'll send you a link to reset your password</p>
                </div>
                <button type="button" class="back-btn" onclick="backToSignIn()">← Back to Sign In</button>
                <div class="form-group">
                    <label for="resetEmail">Email Address</label>
                    <input type="email" id="resetEmail" placeholder="Enter your email address" required>
                </div>
                <button type="submit" class="submit-btn">Send Reset Link</button>
            </form>
        </div>
    </div>

    <script>
        // Cache DOM elements for better performance
        const elements = {
            authTabs: document.getElementById("authTabs"),
            successMessage: document.getElementById("successMessage"),
            errorMessage: document.getElementById("errorMessage"),
            forms: {
                signin: document.getElementById("signinForm"),
                signup: document.getElementById("signupForm"),
                forgotPassword: document.getElementById("forgotPasswordForm")
            }
        };

        // Utility functions
        const hideMessages = () => {
            elements.successMessage.style.display = "none";
            elements.errorMessage.style.display = "none";
        };

        const showMessage = (type, message, duration = 5000) => {
            hideMessages();
            const messageEl = elements[type + "Message"];
            messageEl.textContent = message;
            messageEl.style.display = "block";
            setTimeout(() => messageEl.style.display = "none", duration);
        };

        const setLoading = (button, isLoading) => {
            button.disabled = isLoading;
            if (isLoading) {
                button.innerHTML = '<span class="loading"></span>Processing...';
            } else {
                // Reset button text based on context
                const buttonText = {
                    signinForm: "Sign In",
                    signupForm: "Create Account",
                    forgotPasswordForm: "Send Reset Link"
                };
                const formId = button.closest("form").id;
                button.textContent = buttonText[formId] || "Submit";
            }
        };

        const switchForm = (activeFormId) => {
            document.querySelectorAll(".form").forEach(form => form.classList.remove("active"));
            document.getElementById(activeFormId).classList.add("active");
            hideMessages();
        };

        const makeApiRequest = async (data) => {
            const response = await fetch("./assets/php/auth_handler.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });
            return response.json();
        };

        // Enhanced redirect functions
        const redirectAfterLogin = (userData) => {
            // Store user data with timestamp for security
            const userDataWithTimestamp = {
                ...userData,
                loginTime: new Date().toISOString(),
                sessionId: Math.random().toString(36).substr(2, 9)
            };
            
            // Store in sessionStorage (more secure than localStorage)
            sessionStorage.setItem('userData', JSON.stringify(userDataWithTimestamp));
            
            // Also set a temporary flag for immediate use
            sessionStorage.setItem('justLoggedIn', 'true');
            
            // Redirect to main page with success parameter
            window.location.href = 'index.php?login=success';
        };

        const notifyParentWindow = (userData) => {
            // For iframe/popup scenarios
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({
                    type: 'login_success',
                    userData: userData,
                    timestamp: new Date().toISOString()
                }, window.location.origin);
            }
        };

        const updateMainPageAfterLogin = (userData) => {
            // Store data securely
            sessionStorage.setItem('userData', JSON.stringify(userData));
            
            // Set login success flag
            sessionStorage.setItem('loginSuccess', 'true');
            
            // Clear any previous error states
            sessionStorage.removeItem('loginError');
            
            // Redirect to main page
            window.location.href = 'index.php';
        };

        // Tab switching
        function switchTab(tab) {
            document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
            event.target.classList.add("active");
            switchForm(tab + "Form");
        }

        // Forgot password navigation
        function showForgotPassword() {
            elements.authTabs.style.display = "none";
            switchForm("forgotPasswordForm");
        }

        function backToSignIn() {
            elements.authTabs.style.display = "flex";
            switchForm("signinForm");
            // Reset to first tab as active
            document.querySelectorAll(".tab-btn").forEach((btn, index) => {
                btn.classList.toggle("active", index === 0);
            });
        }

        // Enhanced form validation
        const validateSignIn = (email, password) => {
            if (!email || !password) {
                showMessage("error", "Please fill in all fields");
                return false;
            }
            
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showMessage("error", "Please enter a valid email address");
                return false;
            }
            
            return true;
        };

        const validateSignUp = (name, email, password, confirmPassword) => {
            if (!name || !email || !password || !confirmPassword) {
                showMessage("error", "Please fill in all required fields");
                return false;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showMessage("error", "Please enter a valid email address");
                return false;
            }
            
            if (password !== confirmPassword) {
                showMessage("error", "Passwords do not match!");
                return false;
            }
            
            if (password.length < 6) {
                showMessage("error", "Password must be at least 6 characters long");
                return false;
            }
            
            // Password strength check
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            
            if (!hasUpperCase || !hasLowerCase || !hasNumbers) {
                showMessage("error", "Password must contain at least one uppercase letter, one lowercase letter, and one number");
                return false;
            }
            
            return true;
        };

        const validateEmail = (email) => {
            if (!email) {
                showMessage("error", "Please enter your email address");
                return false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showMessage("error", "Please enter a valid email address");
                return false;
            }
            
            return true;
        };

        // Enhanced form handlers
        const handleSignIn = async (e) => {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector(".submit-btn");
            
            const formData = {
                email: document.getElementById("signinEmail").value.trim(),
                password: document.getElementById("signinPassword").value,
                remember: document.getElementById("rememberMe")?.checked || false
            };

            if (!validateSignIn(formData.email, formData.password)) return;

            setLoading(submitBtn, true);

            try {
                const data = await makeApiRequest({
                    action: "login",
                    ...formData
                });

                if (data.status === "success") {
                    showMessage("success", data.message || "Login successful! Redirecting...");
                    
                    // Enhanced redirect logic
                    if (data.user) {
                        // Choose redirect method based on context
                        if (window.parent && window.parent !== window) {
                            // If in iframe/popup
                            notifyParentWindow(data.user);
                        } else {
                            // Normal redirect
                            redirectAfterLogin(data.user);
                        }
                    } else {
                        // Fallback redirect
                        setTimeout(() => window.location.href = "index.php", 1500);
                    }
                } else {
                    showMessage("error", data.message || "Login failed. Please try again.");
                }
            } catch (error) {
                console.error("Login error:", error);
                showMessage("error", "Network error. Please check your connection and try again.");
            } finally {
                setLoading(submitBtn, false);
            }
        };

        const handleSignUp = async (e) => {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector(".submit-btn");
            
            const formData = {
                name: document.getElementById("signupName").value.trim(),
                email: document.getElementById("signupEmail").value.trim(),
                phone: document.getElementById("signupPhone")?.value.trim() || "",
                password: document.getElementById("signupPassword").value,
                confirmPassword: document.getElementById("confirmPassword").value
            };

            if (!validateSignUp(formData.name, formData.email, formData.password, formData.confirmPassword)) return;

            setLoading(submitBtn, true);

            try {
                const data = await makeApiRequest({
                    action: "register",
                    ...formData
                });

                if (data.status === "success") {
                    showMessage("success", data.message || "Registration successful! Please sign in.");
                    form.reset();
                    setTimeout(() => {
                        switchTab("signin");
                        document.getElementById("signinEmail").value = formData.email;
                        document.getElementById("signinEmail").focus();
                    }, 2000);
                } else {
                    showMessage("error", data.message || "Registration failed. Please try again.");
                }
            } catch (error) {
                console.error("Registration error:", error);
                showMessage("error", "Network error. Please check your connection and try again.");
            } finally {
                setLoading(submitBtn, false);
            }
        };

        const handleForgotPassword = async (e) => {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector(".submit-btn");
            const email = document.getElementById("resetEmail").value.trim();

            if (!validateEmail(email)) return;

            setLoading(submitBtn, true);

            try {
                const data = await makeApiRequest({
                    action: "forgot_password",
                    email
                });

                if (data.status === "success") {
                    showMessage("success", data.message || "Password reset link sent to your email.");
                    form.reset();
                    setTimeout(backToSignIn, 3000);
                } else {
                    showMessage("error", data.message || "Password reset failed. Please try again.");
                }
            } catch (error) {
                console.error("Password reset error:", error);
                showMessage("error", "Network error. Please check your connection and try again.");
            } finally {
                setLoading(submitBtn, false);
            }
        };

        // Session management
        const initializeSession = () => {
            // Clear any stale session data on page load
            const sessionData = sessionStorage.getItem('userData');
            if (sessionData) {
                try {
                    const userData = JSON.parse(sessionData);
                    const loginTime = new Date(userData.loginTime);
                    const now = new Date();
                    const timeDiff = now - loginTime;
                    
                    // Clear session if older than 24 hours
                    if (timeDiff > 24 * 60 * 60 * 1000) {
                        sessionStorage.removeItem('userData');
                        sessionStorage.removeItem('justLoggedIn');
                        sessionStorage.removeItem('loginSuccess');
                    }
                } catch (error) {
                    console.error("Session validation error:", error);
                    sessionStorage.clear();
                }
            }
        };

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            initializeSession();
            
            // Event listeners
            elements.forms.signin?.addEventListener("submit", handleSignIn);
            elements.forms.signup?.addEventListener("submit", handleSignUp);
            elements.forms.forgotPassword?.addEventListener("submit", handleForgotPassword);

            // Interactive input effects
            document.querySelectorAll("input:not(.otp-input)").forEach(input => {
                const parent = input.parentElement;
                if (parent) {
                    input.addEventListener("focus", () => {
                        parent.style.transform = "scale(1.02)";
                        parent.style.transition = "transform 0.2s ease";
                    });
                    input.addEventListener("blur", () => {
                        parent.style.transform = "scale(1)";
                    });
                }
            });

            // Auto-focus first input
            const firstInput = document.querySelector('.form.active input:not([type="hidden"])');
            if (firstInput) {
                firstInput.focus();
            }
        });

        // Handle browser back button
        window.addEventListener('popstate', () => {
            // Clear session flags when navigating back
            sessionStorage.removeItem('justLoggedIn');
            sessionStorage.removeItem('loginSuccess');
        });

        // Export functions for external use
        window.authFunctions = {
            redirectAfterLogin,
            notifyParentWindow,
            updateMainPageAfterLogin,
            switchTab,
            showForgotPassword,
            backToSignIn
        };
    </script>
</body>
</html>