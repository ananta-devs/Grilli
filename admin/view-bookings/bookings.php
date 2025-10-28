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
    <style>
        .table-container td {
            padding: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include '../include/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">

            <!-- View Bookings Section -->
            <section id="bookings" class="content-section active">
                <div class="filter-container">
                    <h3>Booking Management</h3>
                    <div>
                        <select id="bookingFilter" onchange="filterBookings()">
                            <option value="all">All Bookings</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div id="messageContainer"></div>
                
                <div class="booking-count" id="bookingCount">
                    Loading bookings...
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Table</th>
                                <th>Date & Time</th>
                                <th>Guests</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody">
                            <tr>
                                <td colspan="8" class="loading">Loading bookings...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

   

     <!-- View Booking Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <h2>Booking Details</h2>
            <div id="bookingDetails"></div>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Booking Status</h2>
            <div id="editForm"></div>
        </div>
    </div>


    <script>
        let currentFilter = "all";
        let bookingsData = [];

        // Load bookings when page loads
        document.addEventListener("DOMContentLoaded", function () {
            loadBookings();
        });

        async function loadBookings(filter = "all") {
            try {
                clearMessage();

                // Show loading state
                document.getElementById("bookingsTableBody").innerHTML =
                    '<tr><td colspan="8" class="loading">Loading bookings...</td></tr>';

                // Build URL with filter parameter using api.php
                const url = `api.php?action=fetch_bookings${
                    filter !== "all" ? "&filter=" + encodeURIComponent(filter) : ""
                }`;

                const response = await fetch(url);
                const data = await response.json();

                if (data.success) {
                    bookingsData = data.data;
                    displayBookings(bookingsData, data.total, data.filter);
                    currentFilter = data.filter || filter;
                } else {
                    throw new Error(data.error || "Failed to fetch bookings");
                }
            } catch (error) {
                console.error("Error loading bookings:", error);
                showMessage("Failed to load bookings: " + error.message, "error");
                document.getElementById("bookingsTableBody").innerHTML =
                    '<tr><td colspan="8" style="text-align: center; padding: 20px;">Error loading bookings</td></tr>';
            }
        }

        function displayBookings(bookings, total, filter) {
            const tbody = document.getElementById("bookingsTableBody");
            const countDiv = document.getElementById("bookingCount");

            // Update booking count
            let countText = `Total Bookings: ${total}`;
            if (filter && filter !== "all") {
                countText += ` (${filter})`;
            }
            countDiv.textContent = countText;

            // Clear table
            tbody.innerHTML = "";

            if (bookings.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="8" style="text-align: center; padding: 20px;">No bookings found</td></tr>';
                return;
            }

            // Add booking rows
            bookings.forEach((booking) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                            <td>#${String(booking.id).padStart(4, "0")}</td>
                            <td>${escapeHtml(booking.name)}</td>
                            <td>${escapeHtml(booking.phone)}</td>
                            <td>${
                                booking.table_no
                                    ? "Table " + booking.table_no
                                    : "Not Assigned"
                            }</td>
                            <td>${formatDate(booking.reservation_date)} at ${
                    booking.time_slot
                }</td>
                            <td>${booking.persons} person(s)</td>
                            <td><span class="status-badge status-${
                                booking.status
                            }">${capitalizeFirst(booking.status)}</span></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewBooking(${
                                    booking.id
                                })">View</button>
                                <button class="action-btn btn-edit" onclick="editBooking(${
                                    booking.id
                                })">Edit</button>
                            </td>
                        `;
                tbody.appendChild(row);
            });
        }

        function filterBookings() {
            const filter = document.getElementById("bookingFilter").value;
            currentFilter = filter;
            loadBookings(filter);
        }

        function viewBooking(bookingId) {
            const booking = bookingsData.find((b) => b.id == bookingId);
            if (!booking) return;

            const modal = document.getElementById("viewModal");
            const detailsDiv = document.getElementById("bookingDetails");

            const createdDate = formatDateTime(booking.created_at);
            const updatedDate = formatDateTime(booking.updated_at);

            detailsDiv.innerHTML = `
                        <div class="detail-row">
                            <div class="detail-label">Booking ID:</div>
                            <div class="detail-value">#${String(booking.id).padStart(
                                4,
                                "0"
                            )}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Customer Name:</div>
                            <div class="detail-value">${escapeHtml(booking.name)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Phone Number:</div>
                            <div class="detail-value">${escapeHtml(booking.phone)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Number of Guests:</div>
                            <div class="detail-value">${booking.persons} person(s)</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Reservation Date:</div>
                            <div class="detail-value">${formatDate(
                                booking.reservation_date
                            )}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Time Slot:</div>
                            <div class="detail-value">${booking.time_slot}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Table Number:</div>
                            <div class="detail-value">${
                                booking.table_no
                                    ? "Table " + booking.table_no
                                    : "Not Assigned"
                            }</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">
                                <span class="status-badge status-${
                                    booking.status
                                }">${capitalizeFirst(booking.status)}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Special Message:</div>
                            <div class="detail-value">${
                                escapeHtml(booking.message) || "No special requests"
                            }</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Created At:</div>
                            <div class="detail-value">${createdDate}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Last Updated:</div>
                            <div class="detail-value">${updatedDate}</div>
                        </div>
                    `;

            modal.style.display = "block";
        }

        function editBooking(bookingId) {
            const booking = bookingsData.find((b) => b.id == bookingId);
            if (!booking) return;

            const modal = document.getElementById("editModal");
            const formDiv = document.getElementById("editForm");

            formDiv.innerHTML = `
                        <div class="detail-row">
                            <div class="detail-label">Booking ID:</div>
                            <div class="detail-value">#${String(booking.id).padStart(
                                4,
                                "0"
                            )}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Customer:</div>
                            <div class="detail-value">${escapeHtml(booking.name)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Date & Time:</div>
                            <div class="detail-value">${formatDate(
                                booking.reservation_date
                            )} at ${booking.time_slot}</div>
                        </div>
                        
                        <div class="update-form">
                            <div class="form-group">
                                <label for="status">Update Status:</label>
                                <select id="statusSelect">
                                    <option value="pending" ${
                                        booking.status === "pending" ? "selected" : ""
                                    }>Pending</option>
                                    <option value="confirmed" ${
                                        booking.status === "confirmed" ? "selected" : ""
                                    }>Confirmed</option>
                                    <option value="cancelled" ${
                                        booking.status === "cancelled" ? "selected" : ""
                                    }>Cancelled</option>
                                    <option value="completed" ${
                                        booking.status === "completed" ? "selected" : ""
                                    }>Completed</option>
                                </select>
                            </div>
                            
                            <button type="button" class="btn-update" onclick="updateBookingStatus(${
                                booking.id
                            })">Update Status</button>
                        </div>
                    `;

            modal.style.display = "block";
        }

        async function updateBookingStatus(bookingId) {
            const newStatus = document.getElementById("statusSelect").value;

            try {
                // Use api.php with update_booking_status action
                const response = await fetch("api.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        action: "update_booking_status",
                        booking_id: bookingId,
                        status: newStatus,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    showMessage("Status updated successfully!", "success");
                    closeModal("editModal");
                    loadBookings(currentFilter); // Reload bookings
                } else {
                    throw new Error(data.error || "Failed to update status");
                }
            } catch (error) {
                console.error("Error updating status:", error);
                showMessage("Error updating status: " + error.message, "error");
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        function showMessage(message, type) {
            const container = document.getElementById("messageContainer");
            container.innerHTML = `<div class="message ${type}">${message}</div>`;

            // Auto-hide success messages after 3 seconds
            if (type === "success") {
                setTimeout(() => {
                    container.innerHTML = "";
                }, 3000);
            }
        }

        function clearMessage() {
            document.getElementById("messageContainer").innerHTML = "";
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        }

        function formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            return date.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        }

        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function escapeHtml(text) {
            if (text == null) return "";
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        }

        // Close modal when clicking outside of it
        window.onclick = function (event) {
            const viewModal = document.getElementById("viewModal");
            const editModal = document.getElementById("editModal");

            if (event.target == viewModal) {
                viewModal.style.display = "none";
            }
            if (event.target == editModal) {
                editModal.style.display = "none";
            }
        };

    </script>
</body>
</html>