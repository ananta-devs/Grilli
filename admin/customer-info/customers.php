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
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include '../include/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Customer Details Section -->
            <section id="customers" class="content-section active">
                <div class="search-container">
                    <h3>Customer Database</h3>
                    <div class="search-form">
                        <input 
                            type="text" 
                            id="searchInput"
                            class="search-input"
                            placeholder="🔍 Search customers..." 
                        >
                        <button onclick="searchCustomers()" class="search-btn">Search</button>
                        <button onclick="clearSearch()" class="clear-btn" id="clearBtn" style="display: none;">Clear</button>
                    </div>
                </div>

                <div id="errorMessage" class="error" style="display: none;"></div>
                
                <div class="customer-count" id="customerCount">
                    Loading customers...
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Total Visits</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody">
                            <tr>
                                <td colspan="5" class="loading">Loading customers...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        let currentSearch = "";

        // Load customers when page loads
        document.addEventListener("DOMContentLoaded", function () {
            loadCustomers();

            // Add enter key support for search
            document
                .getElementById("searchInput")
                .addEventListener("keypress", function (e) {
                    if (e.key === "Enter") {
                        searchCustomers();
                    }
                });
        });

        async function loadCustomers(search = "") {
            try {
                // Hide error message
                document.getElementById("errorMessage").style.display = "none";

                // Show loading state
                document.getElementById("customersTableBody").innerHTML =
                    '<tr><td colspan="5" class="loading">Loading customers...</td></tr>';

                // Build URL with correct backend endpoint and action
                let url = "api.php?action=search_customers";
                if (search) {
                    url += `&search=${encodeURIComponent(search)}`;
                }

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    displayCustomers(data.data, data.total, data.search);
                } else {
                    throw new Error(data.error || "Failed to fetch customers");
                }
            } catch (error) {
                console.error("Error loading customers:", error);
                showError("Failed to load customers: " + error.message);
                document.getElementById("customersTableBody").innerHTML =
                    '<tr><td colspan="5" class="no-data">Error loading customers</td></tr>';
                document.getElementById("customerCount").textContent =
                    "Error loading customer count";
            }
        }

        function displayCustomers(customers, total, search) {
            const tbody = document.getElementById("customersTableBody");
            const countDiv = document.getElementById("customerCount");
            const clearBtn = document.getElementById("clearBtn");

            // Update customer count
            let countText = `Total Customers: ${total}`;
            if (search) {
                countText += ` (filtered by "${search}")`;
                clearBtn.style.display = "inline-block";
            } else {
                clearBtn.style.display = "none";
            }
            countDiv.textContent = countText;

            // Clear table
            tbody.innerHTML = "";

            if (customers.length === 0) {
                const noDataText = search
                    ? "No customers found matching your search."
                    : "No customers found in the database.";
                tbody.innerHTML = `<tr><td colspan="5" class="no-data">${noDataText}</td></tr>`;
                return;
            }

            // Add customer rows
            customers.forEach((customer) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                            <td>${escapeHtml(customer.id)}</td>
                            <td>${escapeHtml(customer.cus_name)}</td>
                            <td>${escapeHtml(customer.cus_email)}</td>
                            <td>${escapeHtml(customer.cus_ph || "N/A")}</td>
                            <td>
                                <span class="total-visits-badge">
                                    ${escapeHtml(customer.total_visit)}
                                </span>
                            </td>
                        `;
                tbody.appendChild(row);
            });
        }

        function searchCustomers() {
            const searchValue = document.getElementById("searchInput").value.trim();
            currentSearch = searchValue;
            loadCustomers(searchValue);
        }

        function clearSearch() {
            document.getElementById("searchInput").value = "";
            currentSearch = "";
            loadCustomers();
        }

        function showError(message) {
            const errorDiv = document.getElementById("errorMessage");
            errorDiv.textContent = message;
            errorDiv.style.display = "block";
        }

        function escapeHtml(text) {
            if (text == null) return "";
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        }

    </script>
</body>
</html>