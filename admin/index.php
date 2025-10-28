<?php
    require_once './signin/session_check.php';
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
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include './include/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" id="totalTables">--</div>
                        <div class="stat-label">Total Tables</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="availableTables">--</div>
                        <div class="stat-label">Available Tables</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="todaysBookings">--</div>
                        <div class="stat-label">Today's Bookings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalCustomers">--</div>
                        <div class="stat-label">Total Customers</div>
                    </div>
                </div>

                <h3 style="margin-bottom: 1rem; color: #555;">Recent Activity</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Table</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentActivityTable">
                            <tr>
                                <td colspan="6" style="text-align: center;">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>


        </main>
    </div>


    <script>
        // Function to load dashboard data
        async function loadDashboardData() {
            try {
                const response = await fetch("api.php?action=get_dashboard_data");
                const result = await response.json();

                if (result.success) {
                    const data = result.data;

                    // Update stat cards
                    document.getElementById("totalTables").textContent =
                        data.totalTables;
                    document.getElementById("availableTables").textContent =
                        data.availableTables;
                    document.getElementById("todaysBookings").textContent =
                        data.todaysBookings;
                    document.getElementById("totalCustomers").textContent =
                        data.totalCustomers;

                    // Update recent activity table
                    updateRecentActivityTable(data.recentActivity);
                } else {
                    console.error("Error loading dashboard data:", result.error);
                    // Show error message in the table
                    document.getElementById("recentActivityTable").innerHTML =
                        '<tr><td colspan="6" style="text-align: center; color: red;">Error loading data</td></tr>';
                }
            } catch (error) {
                console.error("Error fetching dashboard data:", error);
                // Show error message in the table
                document.getElementById("recentActivityTable").innerHTML =
                    '<tr><td colspan="6" style="text-align: center; color: red;">Error loading data</td></tr>';
            }
        }

        // Function to update recent activity table
        function updateRecentActivityTable(recentActivity) {
            const tbody = document.getElementById("recentActivityTable");

            if (recentActivity.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="6" style="text-align: center;">No recent activity found</td></tr>';
                return;
            }

            let html = "";
            recentActivity.forEach((activity) => {
                const date = new Date(activity.reservation_date);
                const formattedDate = date.toLocaleDateString("en-US", {
                    month: "short",
                    day: "numeric",
                    year: "numeric",
                });

                html += `
                            <tr>
                                <td>${escapeHtml(activity.name)}</td>
                                <td>${escapeHtml(activity.phone)}</td>
                                <td>${
                                    activity.table_no
                                        ? "Table " + activity.table_no
                                        : "Not Assigned"
                                }</td>
                                <td>${formattedDate}</td>
                                <td>${escapeHtml(activity.time_slot)}</td>
                                <td>
                                    <span class="status-${activity.status}">
                                        ${capitalizeFirst(activity.status)}
                                    </span>
                                </td>
                            </tr>
                        `;
            });

            tbody.innerHTML = html;
        }

        // Helper function to escape HTML characters
        function escapeHtml(text) {
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        }

        // Helper function to capitalize first letter
        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Load dashboard data when the page loads
        document.addEventListener("DOMContentLoaded", function () {
            loadDashboardData();

            // Refresh data every 30 seconds
            setInterval(loadDashboardData, 30000);
        });

        // Refresh data when dashboard section becomes active (if using navigation)
        document.addEventListener("click", function (e) {
            if (e.target.matches('[data-section="dashboard"]')) {
                loadDashboardData();
            }
        });

    </script>
</body>
</html>