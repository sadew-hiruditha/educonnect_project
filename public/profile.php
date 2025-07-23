<?php
/**
 * StudyLink - Profile Page
 * Displays user profile information (protected page)
 */

require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectTo('login.php?redirect=profile.php');
}

// Handle profile update
$updateSuccess = false;
$updateError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        $newName = trim($_POST['name']);
        $newEmail = trim($_POST['email']);
        
        // Validate input
        if (empty($newName) || strlen($newName) < 2 || strlen($newName) > 100) {
            $updateError = 'Name must be between 2 and 100 characters.';
        } elseif (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $updateError = 'Please enter a valid email address.';
        } else {
            // Check if email is already taken by another user
            $users = loadUsers();
            $emailTaken = false;
            foreach ($users as $user) {
                if ($user['email'] === $newEmail && $user['id'] !== $_SESSION['user_id']) {
                    $emailTaken = true;
                    break;
                }
            }
            
            if ($emailTaken) {
                $updateError = 'This email address is already registered to another account.';
            } else {
                // Update user data
                $updated = false;
                for ($i = 0; $i < count($users); $i++) {
                    if ($users[$i]['id'] === $_SESSION['user_id']) {
                        $users[$i]['name'] = $newName;
                        $users[$i]['email'] = $newEmail;
                        $updated = true;
                        break;
                    }
                }
                
                if ($updated && saveUsers($users)) {
                    // Update session data
                    $_SESSION['user_name'] = $newName;
                    $_SESSION['user_email'] = $newEmail;
                    $updateSuccess = true;
                } else {
                    $updateError = 'Failed to update profile. Please try again.';
                }
            }
        }
    } else {
        $updateError = 'Invalid security token. Please try again.';
    }
}

// Get user information from session
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];
$userId = $_SESSION['user_id'];

// Get additional user information from storage
$users = loadUsers();
$currentUser = null;
foreach ($users as $user) {
    if ($user['id'] === $userId) {
        $currentUser = $user;
        break;
    }
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="profile-container">
        <div class="profile-card" style="border-top: 4px solid var(--primary-color);">
            <div class="profile-header" style="text-align: center; margin-bottom: 2rem;">
                <div class="profile-avatar" style="width: 100px; height: 100px; font-size: 3rem; margin-bottom: 1rem;">
                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                </div>
                <h1 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.75rem;">
                    <?php echo htmlspecialchars($userName); ?>
                </h1>
                <p style="color: var(--text-secondary); margin: 0;">
                    <?php echo htmlspecialchars($userEmail); ?>
                </p>
            </div>
            
            <div class="tabs" style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem;">
                <button class="tab-button active" style="flex: 1; padding: 0.75rem; border: none; border-radius: var(--border-radius-md); background-color: var(--bg-tertiary); color: var(--text-primary); font-weight: 500;" onclick="switchTab('overview')">
                    Profile Overview
                </button>
                <button class="tab-button" style="flex: 1; padding: 0.75rem; border: none; border-radius: var(--border-radius-md); background-color: var(--bg-secondary); color: var(--text-primary); font-weight: 500;" onclick="switchTab('settings')">
                    Edit Profile
                </button>
            </div>
            
            <div id="overview-tab" class="tab-content active">
                <div class="profile-info" style="margin-bottom: 2rem;">
                    <div class="info-item">
                        <span class="info-label">Full Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($userName); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Email Address:</span>
                        <span class="info-value"><?php echo htmlspecialchars($userEmail); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">User ID:</span>
                        <span class="info-value"><?php echo htmlspecialchars($userId); ?></span>
                    </div>
                    
                    <?php if ($currentUser && isset($currentUser['created_at'])): ?>
                    <div class="info-item">
                        <span class="info-label">Member Since:</span>
                        <span class="info-value">
                            <?php echo date('F j, Y', strtotime($currentUser['created_at'])); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <span class="info-label">Account Status:</span>
                        <span class="info-value" style="color: var(--success-color); font-weight: 500; display: flex; align-items: center; gap: 0.25rem;">
                            <span class="material-icons" style="font-size: 1rem;">check_circle</span> Active
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Last Login:</span>
                        <span class="info-value">
                            <?php echo date('F j, Y \a\t g:i A'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div id="settings-tab" class="tab-content" style="display: none;">
                <div style="padding: 1rem 0;">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Edit Profile</h3>
                        <p style="color: var(--text-secondary); margin: 0;">Update your account information</p>
                    </div>
                    
                    <form id="editProfileForm" method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label class="form-label" for="edit_name">
                                <span class="material-icons" style="font-size: 1rem; vertical-align: middle;">person</span>
                                Full Name
                            </label>
                            <input type="text" id="edit_name" name="name" class="form-input" 
                                   value="<?php echo htmlspecialchars($userName); ?>" 
                                   required minlength="2" maxlength="100"
                                   placeholder="Enter your full name">
                            <span class="field-error" id="edit_name_error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="edit_email">
                                <span class="material-icons" style="font-size: 1rem; vertical-align: middle;">email</span>
                                Email Address
                            </label>
                            <input type="email" id="edit_email" name="email" class="form-input" 
                                   value="<?php echo htmlspecialchars($userEmail); ?>" 
                                   required maxlength="255"
                                   placeholder="Enter your email address">
                            <span class="field-error" id="edit_email_error"></span>
                            <div style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">
                                <span class="material-icons" style="font-size: 0.875rem; vertical-align: middle;">info</span>
                                Changing your email will require you to log in again
                            </div>
                        </div>
                        
                        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <span class="material-icons" style="font-size: 1rem;">save</span>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="contact.php" class="btn btn-primary">
                    Contact Support
                </a>
                <a href="logout.php" class="btn btn-outline-danger">
                    <span class="material-icons" style="font-size: 1rem;">logout</span>
                    Logout
                </a>
            </div>
        </div>
        
        <!-- Quick Actions Section -->
        <div style="margin-top: 3rem;">
            <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">
                Quick Actions
            </h2>
            
            <div class="features-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">contact_mail</span></div>
                    <h3>Contact Us</h3>
                    <p>Send us a message or provide feedback about your experience with StudyLink.</p>
                    <a href="contact.php" class="btn btn-primary" style="margin-top: 1rem;">
                        Send Message
                    </a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">home</span></div>
                    <h3>Home</h3>
                    <p>Return to the main page to explore features and learn more about StudyLink.</p>
                    <a href="index.php" class="btn btn-secondary" style="margin-top: 1rem;">
                        Go Home
                    </a>
                </div>
                
          
            </div>
        </div>
        
        <!-- Account Statistics -->
        <div style="margin-top: 3rem; background-color: var(--bg-secondary); padding: 2rem; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color);">
            <h3 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">
                Account Overview
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; text-align: center;">
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; font-weight: 600; color: var(--primary-color); margin-bottom: 0.5rem;">
                        <?php
                        // Count user's contact submissions
                        $contacts = loadContacts();
                        $userContacts = array_filter($contacts, function($contact) use ($userEmail) {
                            return $contact['email'] === $userEmail;
                        });
                        echo count($userContacts);
                        ?>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">Messages Sent</div>
                </div>
                
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; font-weight: 600; color: var(--primary-color); margin-bottom: 0.5rem;">
                        <?php
                        // Calculate days since registration
                        if ($currentUser && isset($currentUser['created_at'])) {
                            $daysSince = floor((time() - strtotime($currentUser['created_at'])) / (60 * 60 * 24));
                            echo max(1, $daysSince);
                        } else {
                            echo '1';
                        }
                        ?>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">Days as Member</div>
                </div>
                
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; font-weight: 600; color: var(--primary-color); margin-bottom: 0.5rem;">100%</div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">Profile Complete</div>
                </div>
                
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; font-weight: 600; color: var(--success-color); margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 2rem;">verified</span>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">Email Verified</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <span class="close-modal" onclick="hideLogoutConfirmation()">&times;</span>
        <div style="text-align: center; padding: 1rem 0;">
            <div style="font-size: 3rem; color: var(--warning-color); margin-bottom: 1rem;">
                <span class="material-icons" style="font-size: 3rem;">logout</span>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Confirm Logout</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                Are you sure you want to log out of your account? You will need to sign in again to access your profile.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button onclick="hideLogoutConfirmation()" class="btn btn-secondary">
                    <span class="material-icons" style="font-size: 1rem;">close</span>
                    Cancel
                </button>
                <a href="logout.php" class="btn btn-danger">
                    <span class="material-icons" style="font-size: 1rem;">logout</span>
                    Yes, Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Show logout confirmation modal
function showLogoutConfirmation() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.style.display = 'block';
        // Add fade-in animation
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.opacity = '1';
        }, 10);
    }
}

// Hide logout confirmation modal
function hideLogoutConfirmation() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideLogoutConfirmation();
    }
});

function switchTab(tabName) {
    const overviewTab = document.getElementById('overview-tab');
    const settingsTab = document.getElementById('settings-tab');
    const overviewButton = document.querySelector('.tab-button[onclick*="overview"]');
    const settingsButton = document.querySelector('.tab-button[onclick*="settings"]');

    if (tabName === 'overview') {
        overviewTab.style.display = 'block';
        settingsTab.style.display = 'none';
        overviewButton.classList.add('active');
        settingsButton.classList.remove('active');
        overviewButton.style.backgroundColor = 'var(--bg-tertiary)';
        settingsButton.style.backgroundColor = 'var(--bg-secondary)';
    } else if (tabName === 'settings') {
        overviewTab.style.display = 'none';
        settingsTab.style.display = 'block';
        overviewButton.classList.remove('active');
        settingsButton.classList.add('active');
        overviewButton.style.backgroundColor = 'var(--bg-secondary)';
        settingsButton.style.backgroundColor = 'var(--bg-tertiary)';
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const logoutModal = document.getElementById('logoutModal');
    
    if (event.target === logoutModal) {
        hideLogoutConfirmation();
    }
}

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideLogoutConfirmation();
    }
});

// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editProfileForm');
    if (editForm) {
        // Initialize form validation
        initFormValidation(editForm);
        
        // Handle form submission
        editForm.addEventListener('submit', function(event) {
            if (!validateForm(editForm)) {
                event.preventDefault();
                return false;
            }
            
            // Add loading state to submit button
            const submitBtn = editForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            }
        });
    }
});

// Show success/error messages
<?php if ($updateSuccess): ?>
document.addEventListener('DOMContentLoaded', function() {
    showSuccess('Profile updated successfully! Your changes have been saved.');
});
<?php elseif (!empty($updateError)): ?>
document.addEventListener('DOMContentLoaded', function() {
    showModal('Update Failed', '<?php echo addslashes($updateError); ?>', 'error');
});
<?php endif; ?>

// Show security settings modal (placeholder functionality)
function showSecurityModal() {
    showModal(
        'Security Settings', 
        'Security settings would include options to change password, enable two-factor authentication, view login history, and manage active sessions.',
        'info'
    );
}

// Add copy to clipboard functionality for user ID
document.addEventListener('DOMContentLoaded', function() {
    const userIdElement = document.querySelector('.info-item .info-value');
    if (userIdElement) {
        userIdElement.style.cursor = 'pointer';
        userIdElement.title = 'Click to copy User ID';
        userIdElement.addEventListener('click', function() {
            copyToClipboard('<?php echo htmlspecialchars($userId); ?>');
        });
    }
});

// Welcome animation (subtle)
document.addEventListener('DOMContentLoaded', function() {
    const profileCard = document.querySelector('.profile-card');
    if (profileCard) {
        profileCard.style.opacity = '0';
        profileCard.style.transform = 'translateY(20px)';
        profileCard.style.transition = 'all 0.5s ease';
        
        setTimeout(() => {
            profileCard.style.opacity = '1';
            profileCard.style.transform = 'translateY(0)';
        }, 100);
    }
});
</script>

<?php
require_once '../includes/footer.php';
?>
