/**
 * EduConnect - Form Validation JavaScript
 * Handles client-side form validation for all forms
 */

document.addEventListener('DOMContentLoaded', function() {
    initFormValidation();
});

/**
 * Initialize form validation for all forms
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Add real-time validation (exclude hidden fields and radio buttons)
        const inputs = form.querySelectorAll('input:not([type="hidden"]):not([type="radio"]), textarea, select');
        inputs.forEach(input => {
            // Skip CSRF token and other hidden fields
            if (input.type === 'hidden' || input.name === 'csrf_token') {
                return;
            }
            
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => clearFieldError(input));
        });
        
        // Add special handling for radio buttons (rating)
        const radioButtons = form.querySelectorAll('input[type="radio"][name="rating"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                clearRatingError(form.querySelector('.rating-group'));
            });
        });
        
        // Validate on form submission
        form.addEventListener('submit', function(event) {
            if (!validateForm(form)) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
}

/**
 * Validate entire form
 * @param {HTMLFormElement} form
 * @returns {boolean}
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input:not([type="hidden"]):not([type="radio"]), textarea, select');
    
    // Clear previous errors
    clearFormErrors(form);
    
    inputs.forEach(input => {
        // Skip CSRF token and other hidden fields
        if (input.type === 'hidden' || input.name === 'csrf_token') {
            return;
        }
        
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    // Special validation for specific forms
    const formAction = form.action || window.location.pathname;
    
    if (formAction.includes('register.php')) {
        if (!validateRegistrationForm(form)) {
            isValid = false;
        }
    } else if (formAction.includes('login.php')) {
        if (!validateLoginForm(form)) {
            isValid = false;
        }
    } else if (formAction.includes('contact.php')) {
        if (!validateContactForm(form)) {
            isValid = false;
        }
    }
    
    if (!isValid) {
        showError('Please correct the errors in the form and try again.');
    }
    
    return isValid;
}

/**
 * Validate individual field
 * @param {HTMLElement} field
 * @returns {boolean}
 */
function validateField(field) {
    // Skip validation for hidden fields, CSRF tokens, and radio buttons (handled separately)
    if (field.type === 'hidden' || field.name === 'csrf_token' || field.type === 'radio') {
        return true;
    }
    
    const value = field.value.trim();
    const fieldName = field.name || field.id;
    let isValid = true;
    let errorMessage = '';
    
    // Skip validation for non-required fields that are empty
    if (!field.required && value === '') {
        return true;
    }
    
    // Required field validation
    if (field.required && value === '') {
        errorMessage = `${getFieldLabel(field)} is required.`;
        isValid = false;
    }
    
    // Email validation
    else if (field.type === 'email' && value !== '') {
        if (!isValidEmail(value)) {
            errorMessage = 'Please enter a valid email address.';
            isValid = false;
        }
    }
    
    // Password validation
    else if (field.type === 'password' && value !== '') {
        if (!isValidPassword(value)) {
            errorMessage = 'Password must be at least 8 characters long.';
            isValid = false;
        }
    }
    
    // Name validation
    else if ((fieldName === 'name' || fieldName === 'full_name') && value !== '') {
        if (!isValidName(value)) {
            errorMessage = 'Name must contain only letters and spaces.';
            isValid = false;
        }
    }
    
    // Text length validation
    else if (field.maxLength && value.length > field.maxLength) {
        errorMessage = `${getFieldLabel(field)} must not exceed ${field.maxLength} characters.`;
        isValid = false;
    }
    
    // Text minimum length validation
    else if (field.minLength && value.length < field.minLength) {
        errorMessage = `${getFieldLabel(field)} must be at least ${field.minLength} characters long.`;
        isValid = false;
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

/**
 * Validate registration form
 * @param {HTMLFormElement} form
 * @returns {boolean}
 */
function validateRegistrationForm(form) {
    let isValid = true;
    
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirm_password"]');
    
    if (password && confirmPassword) {
        if (password.value !== confirmPassword.value) {
            showFieldError(confirmPassword, 'Passwords do not match.');
            isValid = false;
        }
    }
    
    return isValid;
}

/**
 * Validate login form
 * @param {HTMLFormElement} form
 * @returns {boolean}
 */
function validateLoginForm(form) {
    // Basic validation is handled by validateField
    return true;
}

/**
 * Validate contact form
 * @param {HTMLFormElement} form
 * @returns {boolean}
 */
function validateContactForm(form) {
    let isValid = true;
    
    const rating = form.querySelector('input[name="rating"]:checked');
    if (!rating) {
        const ratingGroup = form.querySelector('.rating-group');
        if (ratingGroup) {
            showRatingError(ratingGroup, 'Please select a rating.');
            isValid = false;
        }
    } else {
        // Clear any existing rating error
        clearRatingError(form.querySelector('.rating-group'));
    }
    
    return isValid;
}

/**
 * Show rating error
 * @param {HTMLElement} ratingGroup
 * @param {string} message
 */
function showRatingError(ratingGroup, message) {
    clearRatingError(ratingGroup);
    
    const errorElement = document.createElement('div');
    errorElement.className = 'rating-error';
    errorElement.textContent = message;
    errorElement.style.cssText = `
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    `;
    
    const container = ratingGroup.closest('.form-group');
    if (container) {
        container.appendChild(errorElement);
    }
}

/**
 * Clear rating error
 * @param {HTMLElement} ratingGroup
 */
function clearRatingError(ratingGroup) {
    if (!ratingGroup) return;
    
    const container = ratingGroup.closest('.form-group');
    if (container) {
        const errorElement = container.querySelector('.rating-error');
        if (errorElement) {
            errorElement.remove();
        }
    }
}

/**
 * Check if email is valid
 * @param {string} email
 * @returns {boolean}
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Check if password is valid
 * @param {string} password
 * @returns {boolean}
 */
function isValidPassword(password) {
    return password.length >= 8;
}

/**
 * Check if name is valid
 * @param {string} name
 * @returns {boolean}
 */
function isValidName(name) {
    const nameRegex = /^[a-zA-Z\s'-]+$/;
    return nameRegex.test(name) && name.length >= 2;
}

/**
 * Show field error
 * @param {HTMLElement} field
 * @param {string} message
 */
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    errorElement.style.cssText = `
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    `;
    
    // Insert error message after the field or its container
    const container = field.closest('.form-group') || field.parentNode;
    container.appendChild(errorElement);
    
    // Add error styles to the field
    field.style.borderColor = 'var(--error-color)';
}

/**
 * Clear field error
 * @param {HTMLElement} field
 */
function clearFieldError(field) {
    field.classList.remove('error');
    field.style.borderColor = '';
    
    const container = field.closest('.form-group') || field.parentNode;
    const errorElement = container.querySelector('.field-error');
    if (errorElement) {
        errorElement.remove();
    }
}

/**
 * Clear all form errors
 * @param {HTMLFormElement} form
 */
function clearFormErrors(form) {
    const errorElements = form.querySelectorAll('.field-error, .rating-error');
    errorElements.forEach(element => element.remove());
    
    const fields = form.querySelectorAll('input, textarea, select');
    fields.forEach(field => {
        field.classList.remove('error');
        field.style.borderColor = '';
    });
}

/**
 * Get field label for error messages
 * @param {HTMLElement} field
 * @returns {string}
 */
function getFieldLabel(field) {
    const label = document.querySelector(`label[for="${field.id}"]`);
    if (label) {
        return label.textContent.replace('*', '').trim();
    }
    
    // Fallback to field name or placeholder
    return field.name || field.placeholder || 'Field';
}

/**
 * Real-time email validation
 */
function setupEmailValidation() {
    const emailFields = document.querySelectorAll('input[type="email"]');
    
    emailFields.forEach(field => {
        field.addEventListener('input', debounce(function() {
            if (field.value.trim() !== '') {
                validateField(field);
            }
        }, 500));
    });
}

/**
 * Real-time password validation
 */
function setupPasswordValidation() {
    const passwordFields = document.querySelectorAll('input[type="password"]');
    
    passwordFields.forEach(field => {
        field.addEventListener('input', function() {
            if (field.name === 'password') {
                validateField(field);
                
                // Also validate confirm password if it exists and has a value
                const confirmField = document.querySelector('input[name="confirm_password"]');
                if (confirmField && confirmField.value.trim() !== '') {
                    validateField(confirmField);
                }
            } else if (field.name === 'confirm_password') {
                validateField(field);
            }
        });
    });
}

/**
 * Setup form-specific validation
 */
function setupFormSpecificValidation() {
    setupEmailValidation();
    setupPasswordValidation();
}

// Initialize form-specific validation when DOM is loaded
document.addEventListener('DOMContentLoaded', setupFormSpecificValidation);
