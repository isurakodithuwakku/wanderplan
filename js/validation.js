/**
 * Form Validation and Input Handling
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

document.addEventListener('DOMContentLoaded', () => {
    initRegisterValidation();
    initLoginValidation();
    initContactValidation();
    initItineraryFormValidation();
});

/**
 * 1. Register Form Real-Time Validation
 */
function initRegisterValidation() {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;

    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');

    // Username validation: min 3 chars, alphanumeric & underscore
    if (usernameInput) {
        usernameInput.addEventListener('input', () => {
            const val = usernameInput.value.trim();
            const isValid = /^[a-zA-Z0-9_]{3,20}$/.test(val);
            toggleFieldStatus(usernameInput, isValid, 'Username must be 3-20 letters, numbers, or underscores.');
        });
    }

    // Email validation
    if (emailInput) {
        emailInput.addEventListener('input', () => {
            const val = emailInput.value.trim();
            const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
            toggleFieldStatus(emailInput, isValid, 'Please enter a valid email address.');
        });
    }

    // Password strength calculation
    if (passwordInput) {
        passwordInput.addEventListener('input', () => {
            const pass = passwordInput.value;
            const strength = calculatePasswordStrength(pass);
            
            if (strengthBar && strengthText) {
                strengthBar.style.width = strength.percent + '%';
                strengthBar.style.backgroundColor = strength.color;
                strengthText.innerText = strength.label;
                strengthText.style.color = strength.color;
            }

            toggleFieldStatus(passwordInput, pass.length >= 6, 'Password must be at least 6 characters long.');
            
            // Check confirm match if already typed
            if (confirmPasswordInput && confirmPasswordInput.value) {
                const isMatch = passwordInput.value === confirmPasswordInput.value;
                toggleFieldStatus(confirmPasswordInput, isMatch, 'Passwords do not match.');
            }
        });
    }

    // Confirm password match
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', () => {
            const isMatch = passwordInput.value === confirmPasswordInput.value && confirmPasswordInput.value.length > 0;
            toggleFieldStatus(confirmPasswordInput, isMatch, 'Passwords do not match.');
        });
    }

    // Form submit check
    registerForm.addEventListener('submit', (e) => {
        let hasError = false;
        
        if (usernameInput && !/^[a-zA-Z0-9_]{3,20}$/.test(usernameInput.value.trim())) {
            toggleFieldStatus(usernameInput, false, 'Username must be 3-20 characters.');
            hasError = true;
        }
        if (emailInput && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
            toggleFieldStatus(emailInput, false, 'Please enter a valid email address.');
            hasError = true;
        }
        if (passwordInput && passwordInput.value.length < 6) {
            toggleFieldStatus(passwordInput, false, 'Password must be at least 6 characters.');
            hasError = true;
        }
        if (confirmPasswordInput && passwordInput.value !== confirmPasswordInput.value) {
            toggleFieldStatus(confirmPasswordInput, false, 'Passwords do not match.');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

/**
 * 2. Login Form Validation
 */
function initLoginValidation() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    const identifierInput = document.getElementById('identifier');
    const passwordInput = document.getElementById('password');

    loginForm.addEventListener('submit', (e) => {
        let hasError = false;
        if (identifierInput && identifierInput.value.trim() === '') {
            toggleFieldStatus(identifierInput, false, 'Please enter your username or email.');
            hasError = true;
        } else if (identifierInput) {
            toggleFieldStatus(identifierInput, true);
        }

        if (passwordInput && passwordInput.value.trim() === '') {
            toggleFieldStatus(passwordInput, false, 'Please enter your password.');
            hasError = true;
        } else if (passwordInput) {
            toggleFieldStatus(passwordInput, true);
        }

        if (hasError) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

/**
 * 3. Contact Form Validation
 */
function initContactValidation() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');

    contactForm.addEventListener('submit', (e) => {
        let hasError = false;
        if (nameInput && nameInput.value.trim().length < 2) {
            toggleFieldStatus(nameInput, false, 'Please enter your name (at least 2 characters).');
            hasError = true;
        } else if (nameInput) {
            toggleFieldStatus(nameInput, true);
        }

        if (emailInput && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
            toggleFieldStatus(emailInput, false, 'Please enter a valid email address.');
            hasError = true;
        } else if (emailInput) {
            toggleFieldStatus(emailInput, true);
        }

        if (messageInput && messageInput.value.trim().length < 10) {
            toggleFieldStatus(messageInput, false, 'Message must be at least 10 characters long.');
            hasError = true;
        } else if (messageInput) {
            toggleFieldStatus(messageInput, true);
        }

        if (hasError) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

/**
 * 4. Itinerary & Trip Form Date & Duration Validation
 */
function initItineraryFormValidation() {
    const tripForm = document.getElementById('tripForm');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const durationBadge = document.getElementById('tripDurationBadge');

    function updateDuration() {
        if (!startDateInput || !endDateInput) return;
        const startVal = startDateInput.value;
        const endVal = endDateInput.value;

        if (startVal && endVal) {
            const start = new Date(startVal);
            const end = new Date(endVal);

            if (end < start) {
                toggleFieldStatus(endDateInput, false, 'End date cannot be earlier than start date.');
                if (durationBadge) durationBadge.innerText = 'Invalid date range';
                return false;
            } else {
                toggleFieldStatus(endDateInput, true);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const nights = Math.max(0, diffDays - 1);
                if (durationBadge) {
                    durationBadge.innerHTML = `<i class="bi bi-calendar-check me-1"></i> ${diffDays} Days / ${nights} Nights`;
                }
                return true;
            }
        }
        return true;
    }

    if (startDateInput) {
        startDateInput.addEventListener('change', () => {
            if (endDateInput && (!endDateInput.value || endDateInput.value < startDateInput.value)) {
                endDateInput.value = startDateInput.value;
            }
            updateDuration();
        });
    }

    if (endDateInput) {
        endDateInput.addEventListener('change', updateDuration);
    }

    if (tripForm) {
        tripForm.addEventListener('submit', (e) => {
            const titleInput = document.getElementById('title');
            const destInput = document.getElementById('destination');
            let valid = true;

            if (titleInput && titleInput.value.trim().length < 3) {
                toggleFieldStatus(titleInput, false, 'Trip title must be at least 3 characters.');
                valid = false;
            }
            if (destInput && destInput.value.trim().length < 2) {
                toggleFieldStatus(destInput, false, 'Destination is required.');
                valid = false;
            }
            if (!updateDuration()) {
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
}

/**
 * Helper: Toggle Bootstrap is-valid / is-invalid classes and error text
 */
function toggleFieldStatus(inputEl, isValid, errorMsg = '') {
    if (!inputEl) return;
    const parent = inputEl.closest('.form-floating') || inputEl.parentElement;
    let feedback = parent.querySelector('.validation-error');
    
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'validation-error';
        parent.appendChild(feedback);
    }

    if (isValid) {
        inputEl.classList.remove('is-invalid');
        inputEl.classList.add('is-valid');
        feedback.style.display = 'none';
        feedback.innerText = '';
    } else {
        inputEl.classList.remove('is-valid');
        inputEl.classList.add('is-invalid');
        feedback.style.display = 'block';
        feedback.innerText = errorMsg;
    }
}

/**
 * Helper: Calculate Password Strength
 */
function calculatePasswordStrength(pass) {
    if (!pass) return { percent: 0, color: '#e2e8f0', label: '' };
    let score = 0;
    if (pass.length >= 6) score += 25;
    if (pass.length >= 10) score += 25;
    if (/[A-Z]/.test(pass)) score += 20;
    if (/[0-9]/.test(pass)) score += 15;
    if (/[^A-Za-z0-9]/.test(pass)) score += 15;

    if (score < 40) {
        return { percent: 30, color: '#f43f5e', label: 'Weak' };
    } else if (score < 75) {
        return { percent: 65, color: '#f59e0b', label: 'Medium' };
    } else {
        return { percent: 100, color: '#10b981', label: 'Strong' };
    }
}
