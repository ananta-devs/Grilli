<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        color: #333;
    }

    .admin-container {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        width: 280px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-right: 1px solid rgba(255, 255, 255, 0.2);
        padding: 2rem 0;
        transition: all 0.3s ease;
    }

    .logo {
        text-align: center;
        margin-bottom: 3rem;
        padding: 0 2rem;
    }

    .logo h1 {
        color: #667eea;
        font-size: 1.8rem;
        font-weight: bold;
    }

    .logo p {
        color: #666;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .nav-menu {
        list-style: none;
    }

    .nav-item {
        margin-bottom: 0.5rem;
    }

    .nav-link {
        display: block;
        padding: 1rem 2rem;
        color: #555;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .nav-link:hover,
    .nav-link.active {
        background: linear-gradient(
            90deg,
            rgba(102, 126, 234, 0.1),
            transparent
        );
        color: #667eea;
        border-left-color: #667eea;
        transform: translateX(5px);
    }

    .nav-link.active {
        background: linear-gradient(
            90deg,
            rgba(102, 126, 234, 0.15),
            transparent
        );
        font-weight: 600;
        box-shadow: inset 0 2px 4px rgba(102, 126, 234, 0.1);
    }

    .nav-link::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.2),
            transparent
        );
        transition: left 0.5s;
    }

    .nav-link:hover::before {
        left: 100%;
    }

    .logout-btn {
        background: linear-gradient(45deg, #ff6b6b, #ee5a6f);
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }

    .logout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    }

    .content-section {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        display: none;
        animation: fadeIn 0.5s ease-in-out;
    }

    .content-section.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .admin-container {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            height: auto;
        }
    }
</style>

<?php
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Define page mappings
$page_mappings = [
    'index.php' => 'dashboard',
    'tables.php' => 'tables',
    'menu.php' => 'menus',
    'bookings.php' => 'bookings',
    'customers.php' => 'customers'
];

// Get current section
$current_section = isset($page_mappings[$current_page]) ? $page_mappings[$current_page] : '';
?>

<nav class="sidebar">
    <div class="logo">
        <h1>Grilli Admin</h1>
        <p>Management Dashboard</p>
    </div>
    <ul class="nav-menu">
        <li class="nav-item">
            <a href="http://localhost/grilli/admin/index.php" 
               class="nav-link <?php echo ($current_section === 'dashboard') ? 'active' : ''; ?>" 
               data-section="dashboard">📊 Dashboard Overview</a>
        </li>
        <li class="nav-item">
            <a href="http://localhost/grilli/admin/manage-tables/tables.php" 
               class="nav-link <?php echo ($current_section === 'tables') ? 'active' : ''; ?>" 
               data-section="tables">🪑 Manage Tables</a>
        </li>
        <li class="nav-item">
            <a href="http://localhost/grilli/admin/menu/menu.php" 
               class="nav-link <?php echo ($current_section === 'menus') ? 'active' : ''; ?>" 
               data-section="menus">🍽️ Menu</a>
        </li>
        <li class="nav-item">
            <a href="http://localhost/grilli/admin/view-bookings/bookings.php" 
               class="nav-link <?php echo ($current_section === 'bookings') ? 'active' : ''; ?>" 
               data-section="bookings">📅 View Bookings</a>
        </li>
        <li class="nav-item">
            <a href="http://localhost/grilli/admin/customer-info/customers.php" 
               class="nav-link <?php echo ($current_section === 'customers') ? 'active' : ''; ?>" 
               data-section="customers">👥 Customer Info</a>
        </li>
        <li class="nav-item">
            <a href="http://localhost/grilli/admin/signin/logout.php" 
               class="nav-link" 
               data-section="signout">Sign Out</a>
        </li>
    </ul>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');
        
        // Add click effect for immediate visual feedback
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Add a temporary loading state
                this.style.opacity = '0.7';
                
                // Reset opacity after a short delay
                setTimeout(() => {
                    this.style.opacity = '1';
                }, 200);
            });
        });
        
        // Optional: Smooth scroll effect for mobile
        if (window.innerWidth <= 768) {
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    setTimeout(() => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }, 100);
                });
            });
        }
    });
</script>