<nav class="navbar" data-navbar>
    <button class="close-btn" aria-label="close menu" data-nav-toggler>
        <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
    </button>

    <a href="#" class="logo">
        <img
            src="./assets/images/logo.svg"
            width="160"
            height="50"
            alt="Grilli - Home"
        />
    </a>

    <ul class="navbar-list">
        <li class="navbar-item">
            <a href="#home" class="navbar-link hover-underline active">
                <div class="separator"></div>
                <span class="span">Home</span>
            </a>
        </li>

        <li class="navbar-item">
            <a href="#menu" class="navbar-link hover-underline">
                <div class="separator"></div>
                <span class="span">Menus</span>
            </a>
        </li>

        <li class="navbar-item">
            <a href="#about" class="navbar-link hover-underline">
                <div class="separator"></div>
                <span class="span">About Us</span>
            </a>
        </li>

        <!-- Dynamic Profile/Sign In Section -->
        <li class="navbar-item profile-section">
            <!-- Show when user is NOT logged in -->
            <a href="http://localhost/grilli/home/signin.php"
                class="navbar-link hover-underline signin-link" id="signInLink">

                <div class="separator"></div>
                <span class="span">Sign In</span>
            </a>

            <!-- Show when user IS logged in (hidden by default) -->
            <div class="profile-dropdown" id="profileDropdown" style="display: none">
                <button class="navbar-link hover-underline profile-btn" id="profileBtn">
                    <div class="separator"></div>
                    <span class="span" id="profileName">Profile</span>
                    <ion-icon
                        name="chevron-down-outline"
                        class="dropdown-icon"
                    ></ion-icon>
                </button>

                <ul class="dropdown-menu" id="dropdownMenu">
                    <li>
                        <a href="profile.php" class="dropdown-item">Profile</a>
                    </li>
                    <li>
                        <a href="my-reservations.php" class="dropdown-item"
                            >Reservations</a
                        >
                    </li>
                    <li>
                        <a href="#" class="dropdown-item" id="logoutBtn"
                            >Logout</a
                        >
                    </li>
                </ul>
            </div>
        </li>
    </ul>

    <div class="text-center">
        <p class="headline-1 navbar-title">Visit Us</p>

        <address class="body-4">
            Restaurant Grilli <br />
            Salt Lake,Kolkata-700001
        </address>

        <p class="body-4 navbar-text">Open: 8.00 am - 10.00pm</p>

        <a href="mailto:booking@grilli.com" class="body-4 sidebar-link">
            booking@grilli.com
        </a>

        <div class="separator"></div>

        <p class="contact-label">Booking Request</p>

        <a
            href="tel:+917890123456"
            class="body-1 contact-number hover-underline"
        >
            +91-7890-123456
        </a>
    </div>
</nav>

<style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        min-width: 150px;
        z-index: 1000;
        margin-top: 5px;
    }

    .dropdown-menu li {
        list-style: none;
    }

    .dropdown-item {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
    }

    .profile-dropdown {
        position: relative;
    }

    .dropdown-icon {
        margin-left: 5px;
        font-size: 14px;
    }

    .profile-btn {
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 10000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        font-weight: 500;
    }

    .toast-success {
        background: #28a745;
        color: white;
    }

    .toast-error {
        background: #dc3545;
        color: white;
    }

    .toast-info {
        background: #17a2b8;
        color: white;
    }
</style>

<script>
// Enhanced Navigation Authentication Handler
class NavigationAuthHandler {
    constructor() {
        this.elements = {
            signInLink: document.getElementById('signInLink'),
            profileDropdown: document.getElementById('profileDropdown'),
            profileName: document.getElementById('profileName'),
            profileBtn: document.getElementById('profileBtn'),
            dropdownMenu: document.getElementById('dropdownMenu'),
            logoutBtn: document.getElementById('logoutBtn')
        };
        
        this.init();
    }

    init() {
        // Check authentication status on page load
        this.checkAuthStatus();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Check for login success from signin page
        this.handleLoginRedirect();
        
        // Listen for storage changes (for multi-tab support)
        window.addEventListener('storage', (e) => {
            if (e.key === 'userData' || e.key === 'loginSuccess') {
                this.checkAuthStatus();
            }
        });
    }

    setupEventListeners() {
        // Profile dropdown toggle
        if (this.elements.profileBtn) {
            this.elements.profileBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDropdown();
            });
        }

        // Logout button
        if (this.elements.logoutBtn) {
            this.elements.logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleLogout();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.elements.profileDropdown?.contains(e.target)) {
                this.closeDropdown();
            }
        });
    }

    async checkAuthStatus() {
        try {
            // First check sessionStorage for immediate response
            const sessionData = sessionStorage.getItem('userData');
            if (sessionData) {
                const userData = JSON.parse(sessionData);
                this.updateNavigation(userData);
                return;
            }

            // Then check server session
            const response = await fetch('./assets/php/session_check.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'check_session' })
            });

            const data = await response.json();
            
            if (data.status === 'success' && data.user) {
                // Update sessionStorage with server data
                sessionStorage.setItem('userData', JSON.stringify(data.user));
                this.updateNavigation(data.user);
            } else {
                this.showSignIn();
            }
        } catch (error) {
            console.error('Auth check error:', error);
            this.showSignIn();
        }
    }

    handleLoginRedirect() {
        // Check if user just logged in
        const urlParams = new URLSearchParams(window.location.search);
        const justLoggedIn = sessionStorage.getItem('justLoggedIn');
        const loginSuccess = sessionStorage.getItem('loginSuccess');
        
        if (urlParams.get('login') === 'success' || justLoggedIn === 'true' || loginSuccess === 'true') {
            // Clear the flags
            sessionStorage.removeItem('justLoggedIn');
            sessionStorage.removeItem('loginSuccess');
            
            // Remove URL parameter
            if (urlParams.get('login')) {
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
            
            // Check auth status to update navigation
            this.checkAuthStatus();
            
            // Show success message
            this.showWelcomeMessage();
        }
    }

    updateNavigation(userData) {
        if (!userData || !userData.name) return;
        
        // Hide sign in link
        if (this.elements.signInLink) {
            this.elements.signInLink.style.display = 'none';
        }
        
        // Show profile dropdown
        if (this.elements.profileDropdown) {
            this.elements.profileDropdown.style.display = 'block';
        }
        
        // Update profile name
        if (this.elements.profileName) {
            this.elements.profileName.textContent = userData.name;
        }
        
        console.log('Navigation updated for user:', userData.name);
    }

    showSignIn() {
        // Show sign in link
        if (this.elements.signInLink) {
            this.elements.signInLink.style.display = 'block';
        }
        
        // Hide profile dropdown
        if (this.elements.profileDropdown) {
            this.elements.profileDropdown.style.display = 'none';
        }
        
        // Clear session data
        sessionStorage.removeItem('userData');
    }

    toggleDropdown() {
        if (this.elements.dropdownMenu) {
            const isVisible = this.elements.dropdownMenu.style.display === 'block';
            this.elements.dropdownMenu.style.display = isVisible ? 'none' : 'block';
        }
    }

    closeDropdown() {
        if (this.elements.dropdownMenu) {
            this.elements.dropdownMenu.style.display = 'none';
        }
    }

    async handleLogout() {
        try {
            const response = await fetch('logout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'logout' })
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                // Clear client-side data
                sessionStorage.clear();
                localStorage.removeItem('userData'); // Clear if using localStorage
                
                // Update navigation
                this.showSignIn();
                
                // Show logout message
                this.showLogoutMessage();
                
                // Redirect to home or refresh
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                console.error('Logout failed:', data.message);
                alert('Logout failed. Please try again.');
            }
        } catch (error) {
            console.error('Logout error:', error);
            alert('Network error. Please try again.');
        }
    }

    showWelcomeMessage() {
        const userData = JSON.parse(sessionStorage.getItem('userData') || '{}');
        if (userData.name) {
            this.showToast(`Welcome back, ${userData.name}!`, 'success');
        }
    }

    showLogoutMessage() {
        this.showToast('Logged out successfully!', 'info');
    }

    showToast(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 10000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: slideInRight 0.3s ease;
        `;

        document.body.appendChild(toast);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.navAuthHandler = new NavigationAuthHandler();
});

// Export for external use
window.NavigationAuthHandler = NavigationAuthHandler;
</script>