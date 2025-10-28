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


            <!-- Manage Tables Section -->
            <section id="tables" class="content-section active">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h3>Table Management</h3>
                    <button class="btn btn-primary" onclick="openModal('addTableModal')">➕ Add New Table</button>
                </div>

                <div id="alertContainer"></div>

                <div class="table-container">
                    <table id="tablesTable">
                        <thead>
                            <tr>
                                <th>Table No.</th>
                                <th>Max Persons</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tablesTableBody">
                            <tr>
                                <td colspan="6" class="loading">Loading tables...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

    <!-- Add Table Modal -->
    <div id="addTableModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addTableModal')">&times;</span>
            <h3>Add New Table</h3>
            <form id="addTableForm">
                <div class="form-group">
                    <label for="tableNo">Table Number:</label>
                    <input type="number" id="tableNo" name="tableNo" required min="1">
                </div>
                <div class="form-group">
                    <label for="maxPersons">Maximum Persons:</label>
                    <input type="number" id="maxPersons" name="maxPersons" required min="1" max="20">
                </div>
                <div style="text-align: right; margin-top: 1.5rem;">
                    <button type="button" class="btn" onclick="closeModal('addTableModal')" style="background-color: #6c757d; color: white;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Table</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Status Modal -->
    <div id="editStatusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editStatusModal')">&times;</span>
            <h3>Edit Table Status</h3>
            <form id="editStatusForm">
                <input type="hidden" id="editTableId">
                <div class="form-group">
                    <label for="tableStatus">Status:</label>
                    <select id="tableStatus" name="tableStatus" required>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div style="text-align: right; margin-top: 1.5rem;">
                    <button type="button" class="btn" onclick="closeModal('editStatusModal')" style="background-color: #6c757d; color: white;">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load tables on page load
        document.addEventListener("DOMContentLoaded", function () {
            loadTables();
        });

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modals = document.getElementsByClassName("modal");
            for (let modal of modals) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            }
        };

        // Load all tables from database
        async function loadTables() {
            try {
                const response = await fetch("api.php?action=fetch_tables");

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (!data.success) {
                    showAlert(
                        "Error loading tables: " + (data.error || "Unknown error"),
                        "error"
                    );
                    return;
                }

                const tbody = document.getElementById("tablesTableBody");
                tbody.innerHTML = "";

                if (!data.data || data.data.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="6" style="text-align: center;">No tables found</td></tr>';
                    return;
                }

                data.data.forEach((table) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                                <td>${escapeHtml(table.table_no)}</td>
                                <td>${escapeHtml(table.max_persons)}</td>
                                <td><span class="status status-${escapeHtml(
                                    table.status
                                )}">${escapeHtml(table.status)}</span></td>
                                <td>${formatDate(table.created_at)}</td>
                                <td>${formatDate(table.updated_at)}</td>
                                <td>
                                    <button class="btn btn-warning" onclick="editTableStatus(${
                                        table.id
                                    }, '${escapeHtml(
                        table.status
                    )}')">✏️ Edit Status</button>
                                    <button class="btn btn-danger" onclick="deleteTable(${
                                        table.id
                                    }, '${escapeHtml(
                        table.table_no
                    )}')">🗑️ Delete</button>
                                </td>
                            `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error("Error loading tables:", error);
                showAlert("Error loading tables: " + error.message, "error");
                document.getElementById("tablesTableBody").innerHTML =
                    '<tr><td colspan="6" style="text-align: center; color: #dc3545;">Error loading tables</td></tr>';
            }
        }

        // Add new table
        document
            .getElementById("addTableForm")
            .addEventListener("submit", async function (e) {
                e.preventDefault();

                const tableNo = document.getElementById("tableNo").value;
                const maxPersons = document.getElementById("maxPersons").value;

                try {
                    const formData = new FormData();
                    formData.append("action", "add_table");
                    formData.append("table_no", tableNo);
                    formData.append("max_persons", maxPersons);

                    const response = await fetch("api.php", {
                        method: "POST",
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        showAlert(data.message, "success");
                        closeModal("addTableModal");
                        document.getElementById("addTableForm").reset();
                        loadTables();
                    } else {
                        showAlert(data.message, "error");
                    }
                } catch (error) {
                    console.error("Error adding table:", error);
                    showAlert("Error adding table: " + error.message, "error");
                }
            });

        // Edit table status
        function editTableStatus(id, currentStatus) {
            document.getElementById("editTableId").value = id;
            document.getElementById("tableStatus").value = currentStatus;
            openModal("editStatusModal");
        }

        document
            .getElementById("editStatusForm")
            .addEventListener("submit", async function (e) {
                e.preventDefault();

                const id = document.getElementById("editTableId").value;
                const status = document.getElementById("tableStatus").value;

                try {
                    const formData = new FormData();
                    formData.append("action", "update_table_status");
                    formData.append("id", id);
                    formData.append("status", status);

                    const response = await fetch("api.php", {
                        method: "POST",
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        showAlert(data.message, "success");
                        closeModal("editStatusModal");
                        loadTables();
                    } else {
                        showAlert(data.message, "error");
                    }
                } catch (error) {
                    console.error("Error updating table status:", error);
                    showAlert("Error updating table status: " + error.message, "error");
                }
            });

        // Delete table
        async function deleteTable(id, tableNo) {
            if (
                confirm(
                    `Are you sure you want to delete Table ${tableNo}? This action cannot be undone.`
                )
            ) {
                try {
                    const formData = new FormData();
                    formData.append("action", "delete_table");
                    formData.append("id", id);

                    const response = await fetch("api.php", {
                        method: "POST",
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        showAlert(data.message, "success");
                        loadTables();
                    } else {
                        showAlert(data.message, "error");
                    }
                } catch (error) {
                    console.error("Error deleting table:", error);
                    showAlert("Error deleting table: " + error.message, "error");
                }
            }
        }

        // Show alert messages
        function showAlert(message, type) {
            const alertContainer = document.getElementById("alertContainer");
            const alertDiv = document.createElement("div");
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;

            alertContainer.innerHTML = "";
            alertContainer.appendChild(alertDiv);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Format date for display
        function formatDate(dateString) {
            if (!dateString) return "N/A";
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return "N/A";
            return date.toLocaleDateString() + " " + date.toLocaleTimeString();
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (text == null) return "";
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        }

    </script>
</body>
</html>