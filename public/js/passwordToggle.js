/**
 * EduConnect - Password Toggle JavaScript
 * Handles show/hide password functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initPasswordToggle();
});

/**
 * Initialize password toggle functionality
 */
function initPasswordToggle() {
    const passwordFields = document.querySelectorAll('input[type="password"]');
    
    passwordFields.forEach(field => {
        setupPasswordToggle(field);
    });
}

/**
 * Setup password toggle for a specific field
 * @param {HTMLInputElement} passwordField
 */
function setupPasswordToggle(passwordField) {
    // Create toggle button
    const toggleButton = createToggleButton();
    
    // Wrap the password field in a container if not already wrapped
    let container = passwordField.closest('.password-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'password-container';
        passwordField.parentNode.insertBefore(container, passwordField);
        container.appendChild(passwordField);
    }
    
    // Add toggle button to container
    container.appendChild(toggleButton);
    
    // Add event listener
    toggleButton.addEventListener('click', function() {
        togglePasswordVisibility(passwordField, toggleButton);
    });
}

/**
 * Create password toggle button
 * @returns {HTMLButtonElement}
 */
function createToggleButton() {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'password-toggle';
    button.innerHTML = '<span class="material-icons">visibility</span>';
    button.setAttribute('aria-label', 'Toggle password visibility');
    button.title = 'Show password';
    
    // Add styles if not already defined in CSS
    button.style.cssText = `
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
        font-size: 1.1rem;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    return button;
}

/**
 * Toggle password visibility
 * @param {HTMLInputElement} passwordField
 * @param {HTMLButtonElement} toggleButton
 */
function togglePasswordVisibility(passwordField, toggleButton) {
    const icon = toggleButton.querySelector('.material-icons');
    
    if (passwordField.type === 'password') {
        // Show password
        passwordField.type = 'text';
        icon.textContent = 'visibility_off';
        toggleButton.title = 'Hide password';
        toggleButton.setAttribute('aria-label', 'Hide password');
    } else {
        // Hide password
        passwordField.type = 'password';
        icon.textContent = 'visibility';
        toggleButton.title = 'Show password';
        toggleButton.setAttribute('aria-label', 'Show password');
    }
}

/**
 * Setup keyboard navigation for password toggle
 */
function setupKeyboardNavigation() {
    const toggleButtons = document.querySelectorAll('.password-toggle');
    
    toggleButtons.forEach(button => {
        button.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                button.click();
            }
        });
    });
}

/**
 * Handle password field focus/blur for better UX
 */
function setupPasswordFieldEvents() {
    const passwordFields = document.querySelectorAll('input[type="password"], input[type="text"][data-original-type="password"]');
    
    passwordFields.forEach(field => {
        field.addEventListener('focus', function() {
            const toggle = field.parentNode.querySelector('.password-toggle');
            if (toggle) {
                toggle.style.color = 'var(--text-secondary)';
            }
        });
        
        field.addEventListener('blur', function() {
            const toggle = field.parentNode.querySelector('.password-toggle');
            if (toggle) {
                toggle.style.color = 'var(--text-muted)';
            }
        });
    });
}

/**
 * Initialize advanced password toggle features
 */
function initAdvancedPasswordToggle() {
    setupKeyboardNavigation();
    setupPasswordFieldEvents();
}

// Initialize advanced features when DOM is loaded
document.addEventListener('DOMContentLoaded', initAdvancedPasswordToggle);

/**
 * Password strength indicator (optional enhancement)
 */
function initPasswordStrengthIndicator() {
    const passwordFields = document.querySelectorAll('input[name="password"]');
    
    passwordFields.forEach(field => {
        if (field.closest('form').action.includes('register')) {
            setupPasswordStrengthIndicator(field);
        }
    });
}

/**
 * Setup password strength indicator for registration
 * @param {HTMLInputElement} passwordField
 */
function setupPasswordStrengthIndicator(passwordField) {
    const strengthIndicator = document.createElement('div');
    strengthIndicator.className = 'password-strength';
    strengthIndicator.innerHTML = `
        <div class="strength-bar">
            <div class="strength-fill"></div>
        </div>
        <div class="strength-text">Password strength: <span class="strength-level">Weak</span></div>
    `;
    
    // Add styles
    strengthIndicator.style.cssText = `
        margin-top: 0.5rem;
        font-size: 0.875rem;
    `;
    
    const container = passwordField.closest('.form-group');
    if (container) {
        container.appendChild(strengthIndicator);
    }
    
    passwordField.addEventListener('input', function() {
        updatePasswordStrength(passwordField, strengthIndicator);
    });
}

/**
 * Update password strength indicator
 * @param {HTMLInputElement} passwordField
 * @param {HTMLElement} strengthIndicator
 */
function updatePasswordStrength(passwordField, strengthIndicator) {
    const password = passwordField.value;
    const strength = calculatePasswordStrength(password);
    
    const strengthFill = strengthIndicator.querySelector('.strength-fill');
    const strengthLevel = strengthIndicator.querySelector('.strength-level');
    
    let color, width, text;
    
    switch (strength) {
        case 0:
            color = '#ef4444';
            width = '20%';
            text = 'Very Weak';
            break;
        case 1:
            color = '#f59e0b';
            width = '40%';
            text = 'Weak';
            break;
        case 2:
            color = '#eab308';
            width = '60%';
            text = 'Fair';
            break;
        case 3:
            color = '#22c55e';
            width = '80%';
            text = 'Good';
            break;
        case 4:
            color = '#16a34a';
            width = '100%';
            text = 'Strong';
            break;
        default:
            color = '#e5e7eb';
            width = '0%';
            text = 'Enter password';
    }
    
    strengthFill.style.cssText = `
        height: 4px;
        background-color: ${color};
        width: ${width};
        transition: all 0.3s ease;
        border-radius: 2px;
    `;
    
    strengthLevel.textContent = text;
    strengthLevel.style.color = color;
}

/**
 * Calculate password strength score
 * @param {string} password
 * @returns {number} Score from 0-4
 */
function calculatePasswordStrength(password) {
    if (!password) return -1;
    
    let score = 0;
    
    // Length check
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    
    // Character variety checks
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    // Reduce score for common patterns
    if (/(.)\1{2,}/.test(password)) score--; // Repeated characters
    if (/123|abc|qwe/i.test(password)) score--; // Sequential characters
    
    return Math.max(0, Math.min(4, score - 2));
}

// Initialize password strength indicator when DOM is loaded
document.addEventListener('DOMContentLoaded', initPasswordStrengthIndicator);
