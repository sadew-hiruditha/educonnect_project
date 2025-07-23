<?php
/**
 * StudyLink - Login Page
 * Handles user authentication and session management
 */

require_once '../includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectTo('profile.php');
}

$errors = [];
$email = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Get and sanitize form data
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate form data
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }
        
        // If no errors, attempt login
        if (empty($errors)) {
            $user = verifyLogin($email, $password);
            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect to profile page or requested page
                $redirect = $_GET['redirect'] ?? 'profile.php';
                redirectTo($redirect);
            } else {
                $errors[] = 'Invalid email or password.';
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="form-container" style="max-width: 450px;">
        <div class="form-header" style="text-align: center; margin-bottom: 2rem;">
            <h1 class="form-title" style="font-size: 2rem; color: var(--primary-color);">Welcome Back!</h1>
            <p style="color: var(--text-secondary);">Sign in to continue to StudyLink.</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Login failed:</strong>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">
                <strong>Registration successful!</strong> Please log in with your new account.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['logout'])): ?>
            <div class="alert alert-success">
                <strong>Logged out successfully!</strong> Thank you for using StudyLink.
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    value="<?php echo htmlspecialchars($email); ?>"
                    required
                    placeholder="Enter your email address"
                    autocomplete="email"
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password *</label>
                <div class="password-container">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        required
                        placeholder="Enter your password"
                        autocomplete="current-password"
                    >
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-full btn-lg">
                    Sign In
                </button>
            </div>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: var(--text-secondary);">
                    Don't have an account? 
                    <a href="register.php" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                        Create one here
                    </a>
                </p>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="#" onclick="showModal('Forgot Password', 'This is a demo application. In a real application, you would implement password reset functionality.', 'info')" 
                   style="color: var(--text-muted); font-size: 0.875rem; text-decoration: none;">
                    Forgot your password?
                </a>
            </div>
        </form>

    </div>
</div>

<script>
// Handle form submission with loading state
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitButton.textContent = 'Signing In...';
        submitButton.disabled = true;
    });
    
    // Handle "Remember me" functionality (if implemented)
    const emailInput = document.getElementById('email');
    const savedEmail = localStorage.getItem('rememberedEmail');
    if (savedEmail && !emailInput.value) {
        emailInput.value = savedEmail;
    }
    
    // Save email for next time (basic remember functionality)
    emailInput.addEventListener('input', function() {
        if (emailInput.value) {
            localStorage.setItem('rememberedEmail', emailInput.value);
        }
    });
});

// Auto-focus on email field if empty, password field if email is filled
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    if (emailInput.value) {
        passwordInput.focus();
    } else {
        emailInput.focus();
    }
});
</script>

<?php
require_once '../includes/footer.php';
?>
