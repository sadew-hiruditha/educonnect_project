<?php
/**
 * StudyLink - Registration Page
 * Handles user registration with form validation and data storage
 */

require_once '../includes/config.php';

$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Get and sanitize form data
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate form data
        if (empty($name)) {
            $errors[] = 'Full name is required.';
        } elseif (strlen($name) < 2) {
            $errors[] = 'Full name must be at least 2 characters long.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (userExists($email)) {
            $errors[] = 'An account with this email already exists.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        // If no errors, register the user
        if (empty($errors)) {
            if (registerUser($name, $email, $password)) {
                $success = true;
                // Clear form data
                $name = $email = '';
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="form-container" style="max-width: 450px;">
        <div class="form-header" style="text-align: center; margin-bottom: 2rem;">
            <h1 class="form-title" style="font-size: 2rem; color: var(--primary-color);">Join StudyLink Today</h1>
            <p style="color: var(--text-secondary);">Create an account to get started.</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>Registration successful!</strong> Your account has been created. 
                <a href="login.php" style="color: var(--success-color); text-decoration: underline;">Click here to log in</a>.
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Please correct the following errors:</strong>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form method="POST" action="register.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="name" class="form-label">Full Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-input" 
                    value="<?php echo htmlspecialchars($name ?? ''); ?>"
                    required
                    minlength="2"
                    maxlength="100"
                    placeholder="Enter your full name"
                    autocomplete="name"
                >
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    value="<?php echo htmlspecialchars($email ?? ''); ?>"
                    required
                    maxlength="255"
                    placeholder="Enter your email address"
                    autocomplete="email"
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
                        minlength="8"
                        maxlength="255"
                        placeholder="Create a strong password"
                        autocomplete="new-password"
                    >
                </div>
                <small style="color: var(--text-muted); font-size: 0.875rem;">
                    Password must be at least 8 characters long
                </small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password *</label>
                <div class="password-container">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-input" 
                        required
                        minlength="8"
                        maxlength="255"
                        placeholder="Confirm your password"
                        autocomplete="new-password"
                    >
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-full btn-lg">
                    Create Account
                </button>
            </div>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: var(--text-secondary);">
                    Already have an account? 
                    <a href="login.php" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
        <?php endif; ?>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); text-align: center;">
            <p style="color: var(--text-muted); font-size: 0.875rem;">
                By creating an account, you agree to our 
                <a href="#" onclick="showModal('Terms of Service', 'This is a demo application. In a real application, you would link to your actual terms of service.', 'info')" style="color: var(--primary-color);">Terms of Service</a> 
                and 
                <a href="#" onclick="showModal('Privacy Policy', 'This is a demo application. In a real application, you would link to your actual privacy policy.', 'info')" style="color: var(--primary-color);">Privacy Policy</a>.
            </p>
        </div>
    </div>
</div>

<script>
// Auto-focus on first input field
document.addEventListener('DOMContentLoaded', function() {
    const firstInput = document.querySelector('input[name="name"]');
    if (firstInput) {
        firstInput.focus();
    }
});

// Show success modal if registration was successful
<?php if ($success): ?>
document.addEventListener('DOMContentLoaded', function() {
    showSuccess('Account created successfully! You can now log in with your credentials.');
});
<?php endif; ?>
</script>

<?php
require_once '../includes/footer.php';
?>
