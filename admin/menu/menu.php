<?php
    require_once '../signin/session_check.php';
    $user = requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="menu.css">
    <style>
        .view-item-container{
            display: flex;
            gap: 25px;
        }

        .view-image  {
            width: 100px;
            height: 100px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include '../include/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Manage Menus Section -->
            <section id="menus" class="content-section active">
                <div class="section-header">
                    <h2>Menu Management</h2>
                    <div class="search-container">
                        <form class="search-box" id="searchForm">
                            <input type="text" 
                                name="search" 
                                class="search-input" 
                                placeholder="Search menu items..." 
                                maxlength="100">
                            <button type="submit" class="btn btn-secondary">Search</button>
                            <button type="button" class="btn btn-outline" id="clearSearch" style="display: none;">Clear</button>
                        </form>
                        <button class="btn btn-primary" onclick="openMenuModal('add')">
                            <span>+</span> Add Menu Item
                        </button>
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="results-info" id="resultsInfo" style="display: none;">
                        <p id="resultsText"></p>
                    </div>
                    
                    <div id="tableWrapper">
                        <!-- Table will be loaded here -->
                    </div>

                    <div class="pagination" id="pagination" style="display: none;">
                        <!-- Pagination will be loaded here -->
                    </div>

                    <div class="no-results" id="noResults" style="display: none;">
                        <h3 id="noResultsTitle">No menu items found</h3>
                        <p id="noResultsText">Start by adding your first menu item.</p>
                        <button class="btn btn-primary" id="noResultsBtn" onclick="openMenuModal('add')">Add Your First Menu Item</button>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- View Menu Modal -->
    <div id="viewMenuModal" class="modal" role="dialog" aria-labelledby="viewMenuModalTitle" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="viewMenuModalTitle">Menu Item Details</h3>
                <span class="close" onclick="closeMenuModal('viewMenuModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="view-item-container">
                    <div class="view-image">
                        <img id="viewImage" src="" alt="" class="menu-image">
                    </div>
                    <div class="view-details">
                        <h4 id="viewName"></h4>
                        <div class="detail-row">
                            <span class="label">Type:</span>
                            <span id="viewType"></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Price:</span>
                            <span id="viewPrice"></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Calories:</span>
                            <span id="viewCalories"></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Cooking Time:</span>
                            <span id="viewCookingTime"></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Spice Level:</span>
                            <span id="viewSpiceLevel"></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Rating:</span>
                            <span id="viewRating"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="menuModal" class="modal" role="dialog" aria-labelledby="menuModalTitle" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="menuModalTitle">Add Menu Item</h3>
                <span class="close" onclick="closeMenuModal('menuModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="menuForm">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="menuId" value="">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="menu_name">Menu Name <span class="required">*</span></label>
                            <input type="text" 
                                   id="menu_name" 
                                   name="menu_name" 
                                   required 
                                   maxlength="100"
                                   placeholder="Enter menu item name">
                        </div>
                        
                        <div class="form-group">
                            <label for="menu_type">Menu Type <span class="required">*</span></label>
                            <select id="menu_type" name="menu_type" required>
                                <option value="">Select Type</option>
                                <option value="break-fast">Break-fast</option>
                                <option value="lunch">Lunch</option>
                                <option value="dinner">Dinner</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="menu_price">Price <span class="required">*</span></label>
                            <input type="number" 
                                   id="menu_price" 
                                   name="menu_price" 
                                   required 
                                   min="0" 
                                   max="999.99" 
                                   step="0.01"
                                   placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label for="calories">Calories</label>
                            <input type="number" 
                                   id="calories" 
                                   name="calories" 
                                   min="0" 
                                   max="9999"
                                   placeholder="Optional">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cooking_time">Cooking Time</label>
                            <input type="text" 
                                   id="cooking_time" 
                                   name="cooking_time" 
                                   placeholder="e.g., 15 mins, 1 hour"
                                   maxlength="50">
                        </div>
                        
                        <div class="form-group">
                            <label for="spice_level">Spice Level <span class="required">*</span></label>
                            <select id="spice_level" name="spice_level" required>
                                <option value="">Select Spice Level</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <input type="number" 
                                   id="rating" 
                                   name="rating" 
                                   min="0" 
                                   max="5" 
                                   step="0.1"
                                   placeholder="0.0 - 5.0">
                        </div>
                        
                        <div class="form-group">
                            <label for="menu_image">Menu Image</label>
                            <input type="file" 
                                   id="menu_image" 
                                   name="menu_image" 
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="form-help">Max 5MB. JPEG, PNG, GIF, WebP only.</small>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeMenuModal('menuModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h3 id="deleteModalTitle">Confirm Delete</h3>
                <span class="close" onclick="closeMenuModal('deleteModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="deleteItemName"></span>"?</p>
                <p class="warning-text">This action cannot be undone.</p>
                
                <form id="deleteForm">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteItemId" value="">
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeMenuModal('deleteModal')">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="deleteBtn">Delete Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global state
        let currentPage = 1;
        let currentSearch = "";
        let isLoading = false;
        let currentItems = [];

        // Initialize the application
        document.addEventListener("DOMContentLoaded", function () {
            loadMenuItems();
            initializeEventListeners();
            setupAccessibility();
        });

        // Initialize all event listeners
        function initializeEventListeners() {
            const $ = (id) => document.getElementById(id);

            // Search handlers
            $("searchForm").addEventListener("submit", handleSearch);
            $("clearSearch").addEventListener("click", clearSearch);
            
            // Fixed: Use querySelector for search input instead of getElementById
            const searchInput = document.querySelector(".search-input");
            if (searchInput) {
                // Debounced search input
                searchInput.addEventListener("input", debounce(function() {
                    const value = this.value.trim();
                    if (value.length >= 2 || value.length === 0) {
                        loadMenuItems(1, value);
                    }
                }, 300));
            }

            // Form handlers
            const menuForm = $("menuForm");
            const deleteForm = $("deleteForm");
            const menuImageInput = $("menu_image");

            if (menuForm) {
                menuForm.addEventListener("submit", handleMenuForm);
            }
            
            if (deleteForm) {
                deleteForm.addEventListener("submit", handleDelete);
            }
            
            if (menuImageInput) {
                menuImageInput.addEventListener("change", validateFileUpload);
            }

            // Modal handlers
            window.addEventListener("click", handleOutsideClick);
            document.addEventListener("keydown", handleEscapeKey);
        }

        // Load menu items via AJAX
        async function loadMenuItems(page = 1, search = "") {
            if (isLoading) return;

            try {
                setLoadingState(true);
                const response = await fetch("menu-api.php", {
                    method: "POST",
                    body: new URLSearchParams({
                        action: "get_items",
                        page,
                        ...(search && { search })
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    renderMenuItems(data.data);
                    currentPage = page;
                    currentSearch = search;
                    updateSearchState(search);
                } else {
                    showAlert(data.message || "Failed to load menu items", "error");
                }
            } catch (error) {
                console.error("Error loading menu items:", error);
                showAlert("Failed to load menu items. Please try again.", "error");
            } finally {
                setLoadingState(false);
            }
        }

        // Render menu items
        function renderMenuItems(data) {
            const { items, total, pages } = data;
            currentItems = items || [];

            const elements = {
                tableWrapper: document.getElementById("tableWrapper"),
                resultsInfo: document.getElementById("resultsInfo"),
                pagination: document.getElementById("pagination"),
                noResults: document.getElementById("noResults")
            };

            if (items?.length > 0) {
                elements.resultsInfo.style.display = "block";
                document.getElementById("resultsText").textContent = 
                    `Showing ${items.length} of ${total} items${currentSearch ? ` for "${currentSearch}"` : ""}`;

                elements.tableWrapper.innerHTML = renderTable(items);
                
                if (pages > 1) {
                    elements.pagination.style.display = "block";
                    elements.pagination.innerHTML = renderPagination(pages, currentPage);
                } else {
                    elements.pagination.style.display = "none";
                }
                
                elements.noResults.style.display = "none";
            } else {
                elements.resultsInfo.style.display = "none";
                elements.tableWrapper.innerHTML = "";
                elements.pagination.style.display = "none";
                elements.noResults.style.display = "block";

                const isSearchResult = !!currentSearch;
                document.getElementById("noResultsTitle").textContent = 
                    isSearchResult ? `No items found for "${currentSearch}"` : "No menu items found";
                document.getElementById("noResultsText").textContent = 
                    isSearchResult ? "Try different keywords or check your spelling." : "Start by adding your first menu item.";

                const btn = document.getElementById("noResultsBtn");
                btn.textContent = isSearchResult ? "Show All Items" : "Add Your First Menu Item";
                btn.onclick = isSearchResult ? clearSearch : () => openMenuModal("add");
            }
        }

        // Render table HTML
        function renderTable(items) {
            return `
                <table class="menu-table" role="table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Image</th><th>Name</th><th>Type</th><th>Price</th>
                            <th>Calories</th><th>Time</th><th>Spice</th><th>Rating</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map((item, index) => `
                            <tr>
                                <td>${parseInt(item.id)}</td>
                                <td>
                                    <img src="${getImageSrc(item)}" 
                                        alt="${escapeHtml(item.menu_name)}" 
                                        class="menu-image" loading="lazy"
                                        onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIGZpbGw9IiNmMGYwZjAiPjx0ZXh0IHg9IjI1IiB5PSIzMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1zaXplPSIxMCI+Tm8gSW1hZ2U8L3RleHQ+PC9zdmc+'">
                                </td>
                                <td>${escapeHtml(item.menu_name)}</td>
                                <td>${formatMenuType(item.menu_type)}</td>
                                <td>$${parseFloat(item.menu_price).toFixed(2)}</td>
                                <td>${item.calories ? parseInt(item.calories) + " cal" : "N/A"}</td>
                                <td>${escapeHtml(item.cooking_time || "N/A")}</td>
                                <td><span class="spice-badge spice-${item.spice_level}">${formatSpiceLevel(item.spice_level)}</span></td>
                                <td><span class="rating">★ ${item.rating ? parseFloat(item.rating).toFixed(1) : "N/A"}</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info btn-sm" onclick="viewItem(${index})" title="View Details">View</button>
                                        <button class="btn btn-warning btn-sm" onclick="editItem(${index})" title="Edit Item">Edit</button>
                                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(${parseInt(item.id)}, '${escapeHtml(item.menu_name)}')" title="Delete Item">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        `).join("")}
                    </tbody>
                </table>
            `;
        }

        // Render pagination
        function renderPagination(totalPages, currentPage) {
            let html = "";

            if (currentPage > 1) {
                html += `<a href="#" onclick="loadMenuItems(1, '${currentSearch}')">&laquo; First</a>`;
                html += `<a href="#" onclick="loadMenuItems(${currentPage - 1}, '${currentSearch}')">&lsaquo; Prev</a>`;
            }

            const start = Math.max(1, currentPage - 2);
            const end = Math.min(totalPages, currentPage + 2);

            for (let i = start; i <= end; i++) {
                html += i === currentPage
                    ? `<span class="current">${i}</span>`
                    : `<a href="#" onclick="loadMenuItems(${i}, '${currentSearch}')">${i}</a>`;
            }

            if (currentPage < totalPages) {
                html += `<a href="#" onclick="loadMenuItems(${currentPage + 1}, '${currentSearch}')">Next &rsaquo;</a>`;
                html += `<a href="#" onclick="loadMenuItems(${totalPages}, '${currentSearch}')">Last &raquo;</a>`;
            }

            return html;
        }

        // View item details
        function viewItem(index) {
            const item = currentItems[index];
            if (!item) {
                showAlert("Item not found", "error");
                return;
            }

            const fields = {
                viewImage: { src: getImageSrc(item), alt: item.menu_name },
                viewName: item.menu_name,
                viewType: formatMenuType(item.menu_type),
                viewPrice: "$" + parseFloat(item.menu_price).toFixed(2),
                viewCalories: item.calories ? item.calories + " cal" : "N/A",
                viewCookingTime: item.cooking_time || "N/A",
                viewSpiceLevel: formatSpiceLevel(item.spice_level),
                viewRating: item.rating ? "★ " + parseFloat(item.rating).toFixed(1) : "N/A"
            };

            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    if (id === "viewImage") {
                        element.src = value.src;
                        element.alt = value.alt;
                    } else {
                        element.textContent = value;
                    }
                }
            });

            openMenuModal("view");
        }

        // Edit item
        function editItem(index) {
            const item = currentItems[index];
            if (!item) {
                showAlert("Item not found", "error");
                return;
            }
            openMenuModal("edit", item);
        }

        // Handle search
        function handleSearch(event) {
            event.preventDefault();
            const searchValue = document.querySelector(".search-input").value.trim();

            if (searchValue.length > 0 && searchValue.length < 2) {
                showAlert("Search term must be at least 2 characters long", "error");
                return;
            }

            loadMenuItems(1, searchValue);
        }

        // Clear search
        function clearSearch() {
            document.querySelector(".search-input").value = "";
            loadMenuItems(1, "");
        }

        // Update search state
        function updateSearchState(search) {
            document.getElementById("clearSearch").style.display = search ? "inline-block" : "none";
        }

        // Handle form submission
        async function handleMenuForm(event) {
            event.preventDefault();
            if (isLoading) return;

            const formData = new FormData(event.target);
            const submitBtn = document.getElementById("submitBtn");
            const originalText = submitBtn.textContent;

            const errors = validateForm(formData);
            if (errors.length > 0) {
                showAlert(errors.join(", "), "error");
                return;
            }

            try {
                setButtonLoading(submitBtn, true);
                
                const response = await fetch("menu-api.php", {
                    method: "POST",
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, "success");
                    setTimeout(() => closeMenuModal("menuModal"), 1000);
                    loadMenuItems(currentPage, currentSearch);
                } else {
                    showAlert(data.message || "Operation failed", "error");
                }
            } catch (error) {
                console.error("Error submitting form:", error);
                showAlert("Failed to submit form. Please try again.", "error");
            } finally {
                setButtonLoading(submitBtn, false, originalText);
            }
        }

        // Handle delete
        async function handleDelete(event) {
            event.preventDefault();
            if (isLoading) return;

            const formData = new FormData(event.target);
            const deleteBtn = document.getElementById("deleteBtn");

            try {
                setButtonLoading(deleteBtn, true);
                
                const response = await fetch("menu-api.php", {
                    method: "POST",
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, "success");
                    setTimeout(() => closeMenuModal("deleteModal"), 1000);
                    loadMenuItems(currentPage, currentSearch);
                } else {
                    showAlert(data.message || "Delete failed", "error");
                }
            } catch (error) {
                console.error("Error deleting item:", error);
                showAlert("Failed to delete item. Please try again.", "error");
            } finally {
                setButtonLoading(deleteBtn, false, "Delete Item");
            }
        }

        // Modal management
        function openMenuModal(action, item = null) {
            if (action === "view") {
                document.getElementById("viewMenuModal").style.display = "block";
                document.body.style.overflow = "hidden";
                return;
            }

            const modal = document.getElementById("menuModal");
            const title = document.getElementById("menuModalTitle");
            const form = document.getElementById("menuForm");
            const submitBtn = document.getElementById("submitBtn");

            if (action === "add") {
                title.textContent = "Add Menu Item";
                document.getElementById("formAction").value = "add";
                submitBtn.textContent = "Add Item";
                form.reset();
                document.getElementById("menuId").value = "";
            } else if (action === "edit" && item) {
                title.textContent = "Edit Menu Item";
                document.getElementById("formAction").value = "edit";
                submitBtn.textContent = "Update Item";
                document.getElementById("menuId").value = item.id;

                const fields = {
                    menu_name: item.menu_name,
                    menu_type: item.menu_type,
                    menu_price: parseFloat(item.menu_price).toFixed(2),
                    calories: item.calories || "",
                    cooking_time: item.cooking_time || "",
                    spice_level: item.spice_level,
                    rating: item.rating || ""
                };

                Object.entries(fields).forEach(([id, value]) => {
                    const element = document.getElementById(id);
                    if (element) element.value = value;
                });
            }

            modal.style.display = "block";
            document.body.style.overflow = "hidden";
        }

        // Close modal
        function closeMenuModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = "none";
                document.body.style.overflow = "auto";
                
                if (modalId === "menuModal") {
                    const form = document.getElementById("menuForm");
                    if (form) {
                        form.reset();
                        document.getElementById("formAction").value = "add";
                        document.getElementById("menuId").value = "";
                    }
                }
                
                if (modalId === "deleteModal") {
                    const deleteForm = document.getElementById("deleteForm");
                    if (deleteForm) deleteForm.reset();
                }
            }
        }

        // Confirm delete
        function confirmDelete(id, name) {
            document.getElementById("deleteItemId").value = id;
            document.getElementById("deleteItemName").textContent = name;
            document.getElementById("deleteModal").style.display = "block";
            document.body.style.overflow = "hidden";
        }

        // Utility functions
        function showAlert(message, type = "info") {
            let alert = document.getElementById("alert");
            if (!alert) {
                alert = document.createElement("div");
                alert.id = "alert";
                alert.className = "alert";
                alert.innerHTML = '<span id="alertMessage"></span>';
                document.body.appendChild(alert);
            }

            const alertMessage = document.getElementById("alertMessage");
            alert.className = `alert alert-${type} fade-in`;
            alert.style.display = "block";
            alertMessage.textContent = message;

            setTimeout(() => alert.style.display = "none", 5000);
        }

        function setLoadingState(loading) {
            isLoading = loading;
            if (loading) {
                const tableWrapper = document.getElementById("tableWrapper");
                if (tableWrapper) {
                    tableWrapper.innerHTML = '<div style="text-align: center; padding: 40px;"><div class="loading-spinner"></div>Loading...</div>';
                }
            }
        }

        function setButtonLoading(button, loading, originalText = "") {
            if (!button) return;
            
            if (loading) {
                button.innerHTML = '<span class="loading-spinner"></span>Processing...';
                button.disabled = true;
            } else {
                button.innerHTML = originalText || "Submit";
                button.disabled = false;
            }
        }

        function validateFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];

            if (file.size > maxSize) {
                showAlert("File size too large. Maximum 5MB allowed.", "error");
                event.target.value = "";
            } else if (!allowedTypes.includes(file.type)) {
                showAlert("Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.", "error");
                event.target.value = "";
            }
        }

        // Event handlers
        function handleOutsideClick(event) {
            const modals = ["menuModal", "deleteModal", "viewMenuModal"];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && event.target === modal) {
                    closeMenuModal(modalId);
                }
            });
        }

        function handleEscapeKey(event) {
            if (event.key === "Escape") {
                const modals = ["menuModal", "deleteModal", "viewMenuModal"];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && modal.style.display === "block") {
                        closeMenuModal(modalId);
                    }
                });
            }
        }

        // Helper functions
        function getImageSrc(item) {
            return item.menu_img?.trim() || "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIGZpbGw9IiNmMGYwZjAiPjx0ZXh0IHg9IjI1IiB5PSIzMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1zaXplPSIxMCI+Tm8gSW1hZ2U8L3RleHQ+PC9zdmc+";
        }

        const formatters = {
            menuType: { "break-fast": "Breakfast", lunch: "Lunch", dinner: "Dinner" },
            spiceLevel: { low: "Low", medium: "Medium", high: "High" }
        };

        function formatMenuType(type) {
            return formatters.menuType[type] || type;
        }

        function formatSpiceLevel(level) {
            return formatters.spiceLevel[level] || level;
        }

        function escapeHtml(text) {
            if (!text) return "";
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        }

        // Form validation
        function validateForm(formData) {
            const errors = [];
            const price = parseFloat(formData.get("menu_price"));
            const rating = formData.get("rating");
            const calories = formData.get("calories");

            if (!formData.get("menu_name")?.trim()) errors.push("Menu name is required");
            if (!formData.get("menu_type")) errors.push("Menu type is required");
            if (!formData.get("menu_price") || price <= 0) errors.push("Valid price is required");
            if (!formData.get("spice_level")) errors.push("Spice level is required");

            if (price < 0 || price > 999.99) errors.push("Price must be between 0 and 999.99");
            if (rating && (parseFloat(rating) < 0 || parseFloat(rating) > 5)) errors.push("Rating must be between 0 and 5");
            if (calories && (parseInt(calories) < 0 || parseInt(calories) > 9999)) errors.push("Calories must be between 0 and 9999");

            return errors;
        }

        // Accessibility setup
        function setupAccessibility() {
            document.querySelectorAll("button").forEach(button => {
                if (!button.getAttribute("aria-label") && button.title) {
                    button.setAttribute("aria-label", button.title);
                }
            });

            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener("keydown", function(event) {
                    if (event.key === "Tab") {
                        const focusables = modal.querySelectorAll("a[href], button, textarea, input, select");
                        const first = focusables[0];
                        const last = focusables[focusables.length - 1];

                        if (event.shiftKey && document.activeElement === first) {
                            last?.focus();
                            event.preventDefault();
                        } else if (!event.shiftKey && document.activeElement === last) {
                            first?.focus();
                            event.preventDefault();
                        }
                    }
                });
            });
        }

        // Debounce utility
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Error handling
        window.addEventListener("error", function(event) {
            console.error("JavaScript error:", event.error);
            showAlert("An unexpected error occurred. Please refresh the page.", "error");
        });

        // Network status
        window.addEventListener("online", () => showAlert("Connection restored.", "success"));
        window.addEventListener("offline", () => showAlert("Connection lost. Please check your internet connection.", "error"));
    </script>
</body>
</html>