// Improved JavaScript with better error handling for non-JSON responses
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservationForm');
    const personsSelect = document.getElementById('personsSelect');
    const notification = document.getElementById('notification');
    const notificationText = document.getElementById('notificationText');
    
    // Load person options on page load
    loadPersonOptions();
    
    // Set minimum date to today
    const dateInput = document.querySelector('input[name="reservation_date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    }
    
    // Load available person options
    async function loadPersonOptions() {
        try {
            const response = await fetch('./assets/php/get_persons_options.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const text = await response.text();
            let data;
            
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response from get_persons_options.php:', text);
                showNotification('Error loading table options', 'error');
                return;
            }
            
            if (Array.isArray(data) && data.length > 0) {
                personsSelect.innerHTML = '<option value="">Number of Persons</option>';
                
                data.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.max_persons;
                    optionElement.textContent = `${option.max_persons} Person${option.max_persons > 1 ? 's' : ''}`;
                    personsSelect.appendChild(optionElement);
                });
            } else {
                showNotification('No tables available at the moment', 'error');
            }
        } catch (error) {
            console.error('Error loading person options:', error);
            showNotification('Error loading table options', 'error');
        }
    }
    
    // Form submission handler
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="text text-1">Processing...</span>';
            
            try {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                
                if (!validateForm(data)) {
                    return;
                }
                
                console.log('Submitting reservation data:', data);
                
                const response = await fetch('./assets/php/process_reservation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data),
                    credentials: 'include'
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Get response as text first to check if it's valid JSON
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text:', responseText);
                    
                    // Check if response contains HTML error messages
                    if (responseText.includes('<br />') || responseText.includes('<b>')) {
                        showNotification('Server configuration error. Please contact support.', 'error');
                    } else {
                        showNotification('Invalid server response. Please try again.', 'error');
                    }
                    return;
                }
                
                console.log('Parsed result:', result);
                
                // Handle response
                if (result.success) {
                    showNotification(result.message || 'Reservation confirmed successfully!', 'success');
                    form.reset();
                    
                    // Show additional details if available
                    if (result.details) {
                        console.log('Reservation Details:', result.details);
                    }
                    
                    // Reload person options
                    setTimeout(() => {
                        loadPersonOptions();
                    }, 1000);
                    
                } else {
                    if (result.login_required) {
                        showNotification(result.message || 'Please sign in to continue', 'error');
                        setTimeout(() => {
                            window.location.href = 'signin.php';
                        }, 2000);
                    } else {
                        showNotification(result.message || 'Reservation failed. Please try again.', 'error');
                    }
                }
                
            } catch (error) {
                console.error('Error submitting reservation:', error);
                
                if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                    showNotification('Network error. Please check your connection and try again.', 'error');
                } else if (error.name === 'SyntaxError' && error.message.includes('JSON')) {
                    showNotification('Server response error. Please contact support if this continues.', 'error');
                } else {
                    showNotification('An error occurred. Please try again.', 'error');
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    // Form validation
    function validateForm(data) {
        const errors = [];
        
        if (!data.name || data.name.trim() === '') {
            errors.push('Name is required');
        }
        
        if (!data.phone || data.phone.trim() === '') {
            errors.push('Phone number is required');
        } else if (!/^[0-9+\-\s()]{10,20}$/.test(data.phone)) {
            errors.push('Please enter a valid phone number');
        }
        
        if (!data.persons || parseInt(data.persons) <= 0) {
            errors.push('Please select number of persons');
        }
        
        if (!data.reservation_date) {
            errors.push('Reservation date is required');
        } else {
            const selectedDate = new Date(data.reservation_date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (selectedDate < today) {
                errors.push('Reservation date cannot be in the past');
            }
        }
        
        if (!data.time_slot) {
            errors.push('Please select a time slot');
        }
        
        if (errors.length > 0) {
            showNotification(errors.join('. '), 'error');
            return false;
        }
        
        return true;
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        console.log('Showing notification:', type, message);
        
        if (notification && notificationText) {
            notificationText.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        } else {
            alert(message);
        }
    }
    
    // Close notification on click
    if (notification) {
        notification.addEventListener('click', function() {
            notification.style.display = 'none';
        });
    }
});

// CSS for notifications
const notificationStyles = `
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    z-index: 9999;
    max-width: 400px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: all 0.3s ease;
}

.notification.success {
    background-color: #28a745;
    border-left: 4px solid #1e7e34;
}

.notification.error {
    background-color: #dc3545;
    border-left: 4px solid #c82333;
}

.notification.info {
    background-color: #17a2b8;
    border-left: 4px solid #138496;
}

.notification:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}
`;

if (!document.querySelector('#notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = notificationStyles;
    document.head.appendChild(style);
}