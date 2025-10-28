<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grilli Restaurant - Menu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px 0;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .header h1 {
            color: #d4af37;
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            font-weight: 300;
            letter-spacing: 2px;
        }

        .header p {
            color: #ccc;
            font-size: 1.2em;
            font-style: italic;
        }

        .menu-content {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }

        .menu-item {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #d4af37, #f4e184, #d4af37);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 2;
        }

        .menu-item:hover::before {
            transform: translateX(0);
        }

        .menu-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            background: rgba(255, 255, 255, 0.12);
        }

        .menu-item-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(45deg, #2d2d2d, #1a1a1a);
            border-radius: 20px 20px 0 0;
            overflow: hidden;
            position: relative;
        }

        .menu-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .menu-item:hover .menu-item-image img {
            transform: scale(1.05);
        }

        .menu-item-image .image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, #2d2d2d, #1a1a1a);
            color: #666;
            font-size: 3em;
        }

        .menu-item-content {
            padding: 25px;
        }

        .menu-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .menu-item h3 {
            color: #d4af37;
            font-size: 1.4em;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .menu-price {
            color: #f4e184;
            font-size: 1.3em;
            font-weight: bold;
            background: rgba(212, 175, 55, 0.2);
            padding: 5px 15px;
            border-radius: 25px;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .menu-description {
            color: #ccc;
            font-style: italic;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .menu-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .menu-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #aaa;
            font-size: 0.9em;
            background: rgba(255, 255, 255, 0.05);
            padding: 5px 10px;
            border-radius: 15px;
        }

        .rating {
            color: #ffd700;
            font-weight: bold;
        }

        .calories {
            color: #ff6b6b;
        }

        .spice-level {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .spice-low {
            color: #4CAF50;
        }

        .spice-medium {
            color: #FF9800;
        }

        .spice-high {
            color: #F44336;
        }

        .loading {
            text-align: center;
            color: #d4af37;
            font-size: 1.2em;
            padding: 40px;
        }

        .error {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid rgba(255, 0, 0, 0.3);
        }

        .empty-state {
            text-align: center;
            color: #888;
            padding: 60px 20px;
            font-style: italic;
        }

        .empty-state i {
            font-size: 4em;
            margin-bottom: 20px;
            display: block;
            color: #555;
        }

        .image-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            backdrop-filter: blur(5px);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
            }
            
            .menu-item-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .menu-item-image {
                height: 180px;
            }
        }
    </style>
</head>
<body>
    <?php
    // Get the menu type from URL parameter, default to 'break-fast'
    $menuType = isset($_GET['type']) ? $_GET['type'] : 'break-fast';
    
    // Validate the menu type to prevent XSS
    $validTypes = ['break-fast', 'lunch', 'dinner'];
    if (!in_array($menuType, $validTypes)) {
        $menuType = 'break-fast';
    }
    ?>

    <div class="container">
        <div class="header">
            <h1>Grilli Restaurant</h1>
            <p>Exquisite flavors, crafted with passion</p>
        </div>
        
        <!-- Single Menu Content -->
        <div class="menu-content">
            <div class="loading">Loading menu...</div>
        </div>
    </div>

    <script>
        class MenuManager {
            constructor() {
                // Get menu type from PHP (URL parameter)
                this.menuType = '<?php echo $menuType; ?>';
                this.init();
            }

            init() {
                this.loadMenu(this.menuType);
            }

            async loadMenu(type) {
                try {
                    const response = await fetch(`./assets/php/menu-api.php?type=${type}`);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();

                    if (data.success) {
                        this.displayMenu(data.data);
                    } else {
                        this.displayError(data.message);
                    }
                } catch (error) {
                    this.displayError('Failed to load menu. Please try again later.');
                    console.error('Menu loading error:', error);
                }
            }

            displayMenu(items) {
                const container = document.querySelector('.menu-content');

                if (!container) {
                    console.error('Menu container not found');
                    return;
                }

                if (items.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i>🍽️</i>
                            <h3>No items available</h3>
                            <p>Our menu is currently being updated.</p>
                        </div>
                    `;
                    return;
                }

                const menuHTML = `
                    <div class="menu-grid">
                        ${items.map(item => this.createMenuItemHTML(item)).join('')}
                    </div>
                `;

                container.innerHTML = menuHTML;
            }

            createMenuItemHTML(item) {
                const imageHTML = item.image ? 
                    `<img src="${item.image.url}" alt="${this.escapeHtml(item.menu_name)}" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'image-placeholder\\'>🍽️</div>'">` :
                    `<div class="image-placeholder">🍽️</div>`;

                // Create spice level HTML
                const spiceLevelHTML = item.spice_info ? 
                    `<span class="info-item">
                        <span class="spice-level spice-${item.spice_level}">
                            ${item.spice_info.emoji} ${item.spice_info.label}
                        </span>
                    </span>` : '';

                return `
                    <div class="menu-item">
                        <div class="menu-item-image">
                            ${imageHTML}
                            ${item.rating ? `<div class="image-overlay">★ ${item.rating}</div>` : ''}
                        </div>
                        
                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <div>
                                    <h3>${this.escapeHtml(item.menu_name)}</h3>
                                </div>
                                <div class="menu-price">₹${item.menu_price}</div>
                            </div>
                            
                            ${item.menu_description ? `<p class="menu-description">${this.escapeHtml(item.menu_description)}</p>` : ''}
                            
                            <div class="menu-details">
                                <div class="menu-info">
                                    ${item.calories ? `<span class="info-item"><span class="calories">${item.calories} cal</span></span>` : ''}
                                    ${item.cooking_time ? `<span class="info-item">🕒 ${item.cooking_time}</span>` : ''}
                                    ${spiceLevelHTML}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            displayError(message) {
                const container = document.querySelector('.menu-content');
                
                if (!container) {
                    console.error('Menu container not found');
                    return;
                }
                
                container.innerHTML = `
                    <div class="error">
                        <h3>Error Loading Menu</h3>
                        <p>${this.escapeHtml(message)}</p>
                        <button onclick="menuManager.loadMenu('${this.menuType}')" style="margin-top: 10px; padding: 10px 20px; background: #d4af37; color: #1a1a1a; border: none; border-radius: 5px; cursor: pointer;">
                            Try Again
                        </button>
                    </div>
                `;
            }

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }

        // Initialize the menu manager when the page loads
        let menuManager;
        document.addEventListener('DOMContentLoaded', () => {
            menuManager = new MenuManager();
        });
    </script>
</body>
</html>