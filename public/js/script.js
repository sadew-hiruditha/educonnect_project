/**
 * EduConnect - Main JavaScript File
 * Handles modal functionality, theme toggle, and general interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initThemeToggle();
    initMobileMenu();
    initModal();
    initFormSubmissions();
});

/**
 * Theme Toggle Functionality
 */
function initThemeToggle() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle.querySelector('.theme-icon');
    
    // Load saved theme preference or default to light mode
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeIcon.textContent = 'light_mode';
    } else {
        themeIcon.textContent = 'dark_mode';
    }
    
    themeToggle.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            themeIcon.textContent = 'light_mode';
        } else {
            localStorage.setItem('theme', 'light');
            themeIcon.textContent = 'dark_mode';
        }
    });
}

/**
 * Mobile Menu Toggle
 */
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
}

/**
 * Modal Functionality
 */
function initModal() {
    const modal = document.getElementById('modal');
    const closeModal = document.querySelector('.close-modal');
    
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            hideModal();
        });
    }
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            hideModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideModal();
        }
    });
}

/**
 * Show modal with custom content
 * @param {string} title - Modal title
 * @param {string} message - Modal message
 * @param {string} type - Modal type (success, error, warning)
 */
function showModal(title, message, type = 'info') {
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modal-body');
    
    let icon = '';
    switch (type) {
        case 'success':
            icon = '<span class="material-icons" style="color: var(--success-color);">check_circle</span>';
            break;
        case 'error':
            icon = '<span class="material-icons" style="color: var(--error-color);">error</span>';
            break;
        case 'warning':
            icon = '<span class="material-icons" style="color: var(--warning-color);">warning</span>';
            break;
        default:
            icon = '<span class="material-icons" style="color: var(--primary-color);">info</span>';
    }
    
    modalBody.innerHTML = `
        <div class="modal-header">
            <h3 style="color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                ${icon} ${title}
            </h3>
        </div>
        <div class="modal-message">
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">${message}</p>
        </div>
        <div class="modal-actions">
            <button class="btn btn-primary" onclick="hideModal()">OK</button>
        </div>
    `;
    
    modal.style.display = 'block';
}

/**
 * Hide modal
 */
function hideModal() {
    const modal = document.getElementById('modal');
    modal.style.display = 'none';
}

/**
 * Show success message
 * @param {string} message
 */
function showSuccess(message) {
    showModal('Success', message, 'success');
}

/**
 * Show error message
 * @param {string} message
 */
function showError(message) {
    showModal('Error', message, 'error');
}

/**
 * Show warning message
 * @param {string} message
 */
function showWarning(message) {
    showModal('Warning', message, 'warning');
}

/**
 * Initialize form submission handling
 */
function initFormSubmissions() {
    // Handle form submissions with loading states
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.classList.add('loading');
                submitButton.disabled = true;
                
                // Re-enable button after a short delay (in case of validation errors)
                setTimeout(() => {
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                }, 3000);
            }
        });
    });
}

/**
 * Smooth scroll to element
 * @param {string} selector
 */
function scrollToElement(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

/**
 * Format date for display
 * @param {string} dateString
 * @returns {string}
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Copy text to clipboard
 * @param {string} text
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showSuccess('Text copied to clipboard!');
    }).catch(() => {
        showError('Failed to copy text to clipboard.');
    });
}

/**
 * Debounce function
 * @param {Function} func
 * @param {number} wait
 * @returns {Function}
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Check if element is in viewport
 * @param {Element} element
 * @returns {boolean}
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Handle page-specific functionality
 */
function initPageSpecific() {
    const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
    
    switch (currentPage) {
        case 'index':
            initHomePage();
            break;
        case 'contact':
            initContactPage();
            break;
        case 'profile':
            initProfilePage();
            break;
    }
}

/**
 * Initialize home page specific functionality
 */
function initHomePage() {
    // Add any home page specific JavaScript here
}

/**
 * Initialize contact page specific functionality
 */
function initContactPage() {
    // Handle star rating
    const ratingInputs = document.querySelectorAll('.rating-input');
    const ratingLabels = document.querySelectorAll('.rating-label');
    
    ratingLabels.forEach((label, index) => {
        label.addEventListener('mouseenter', () => {
            highlightStars(index + 1);
        });
        
        label.addEventListener('click', () => {
            selectRating(index + 1);
        });
    });
    
    // Reset stars when mouse leaves rating area
    const ratingGroup = document.querySelector('.rating-group');
    if (ratingGroup) {
        ratingGroup.addEventListener('mouseleave', () => {
            const checkedRating = document.querySelector('.rating-input:checked');
            if (checkedRating) {
                highlightStars(checkedRating.value);
            } else {
                highlightStars(0);
            }
        });
    }
}

/**
 * Highlight stars up to the given rating
 * @param {number} rating
 */
function highlightStars(rating) {
    const ratingLabels = document.querySelectorAll('.rating-label');
    ratingLabels.forEach((label, index) => {
        if (index < rating) {
            label.style.color = '#fbbf24';
        } else {
            label.style.color = 'var(--text-muted)';
        }
    });
}

/**
 * Select a rating
 * @param {number} rating
 */
function selectRating(rating) {
    const ratingInput = document.querySelector(`.rating-input[value="${rating}"]`);
    if (ratingInput) {
        ratingInput.checked = true;
        highlightStars(rating);
    }
}

/**
 * Initialize profile page specific functionality
 */
function initProfilePage() {
    // Add any profile page specific JavaScript here
}

// Initialize page-specific functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', initPageSpecific);
