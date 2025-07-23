<?php
/**
 * EduConnect - Contact Page
 * Handles contact form submissions and displays previous submissions (protected page)
 */

require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectTo('login.php?redirect=contact.php');
}

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
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');
        $rating = intval($_POST['rating'] ?? 0);
        
        // Validate form data
        if (empty($name)) {
            $errors[] = 'Full name is required.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (empty($subject)) {
            $errors[] = 'Subject is required.';
        }
        
        if (empty($message)) {
            $errors[] = 'Message is required.';
        } elseif (strlen($message) < 10) {
            $errors[] = 'Message must be at least 10 characters long.';
        }
        
        if ($rating < 1 || $rating > 5) {
            $errors[] = 'Please select a rating between 1 and 5 stars.';
        }
        
        // If no errors, save the contact submission
        if (empty($errors)) {
            if (saveContact($name, $email, $subject, $message, $rating)) {
                $success = true;
                // Clear form data
                $name = $email = $subject = $message = '';
                $rating = 0;
            } else {
                $errors[] = 'Failed to submit your message. Please try again.';
            }
        }
    }
}

// Load all contact submissions for display
$contacts = loadContacts();
// Sort by submission date (newest first)
usort($contacts, function($a, $b) {
    return strtotime($b['submitted_at']) - strtotime($a['submitted_at']);
});

require_once '../includes/header.php';
?>

<div class="container">
    <div class="contact-container">
        <h1 style="text-align: center; margin-bottom: 1rem; color: var(--text-primary);">
            Contact Us
        </h1>
        <p style="text-align: center; color: var(--text-secondary); margin-bottom: 3rem;">
            We'd love to hear from you! Send us a message and we'll respond as soon as possible.
        </p>
        
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="form-container">
                <h2 class="form-title">Send us a Message</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <strong>Message sent successfully!</strong> Thank you for contacting us. We'll get back to you soon.
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
                
                <form method="POST" action="contact.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input" 
                            value="<?php echo htmlspecialchars($name ?? $_SESSION['user_name']); ?>"
                            required
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
                            value="<?php echo htmlspecialchars($email ?? $_SESSION['user_email']); ?>"
                            required
                            placeholder="Enter your email address"
                            autocomplete="email"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">Subject *</label>
                        <input 
                            type="text" 
                            id="subject" 
                            name="subject" 
                            class="form-input" 
                            value="<?php echo htmlspecialchars($subject ?? ''); ?>"
                            required
                            placeholder="What is this message about?"
                            maxlength="200"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Message *</label>
                        <textarea 
                            id="message" 
                            name="message" 
                            class="form-textarea" 
                            required
                            minlength="10"
                            maxlength="1000"
                            placeholder="Tell us how we can help you..."
                            rows="5"
                        ><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        <small style="color: var(--text-muted); font-size: 0.875rem;">
                            Minimum 10 characters, maximum 1000 characters
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Rate Your Experience *</label>
                        <div class="rating-group">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="rating" value="<?php echo $i; ?>" id="rating_<?php echo $i; ?>" class="rating-input" <?php echo (isset($rating) && $rating == $i) ? 'checked' : ''; ?>>
                                <label for="rating_<?php echo $i; ?>" class="rating-label">★</label>
                            <?php endfor; ?>
                            <span style="margin-left: 1rem; color: var(--text-muted); font-size: 0.875rem;">
                                (1 = Poor, 5 = Excellent)
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-full btn-lg">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Previous Submissions -->
            <div class="submissions-section">
                <h2 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                    Previous Submissions
                    <span style="font-size: 0.875rem; color: var(--text-muted); font-weight: normal;">
                        (<?php echo count($contacts); ?> total)
                    </span>
                </h2>
                
                <?php if (empty($contacts)): ?>
                    <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">
                            <span class="material-icons" style="font-size: 3rem; color: var(--text-muted);">mail_outline</span>
                        </div>
                        <p>No messages have been submitted yet.</p>
                        <p style="font-size: 0.875rem;">Be the first to send us a message!</p>
                    </div>
                <?php else: ?>
                    <div class="submissions-list">
                        <?php foreach ($contacts as $contact): ?>
                            <div class="submission-item">
                                <div class="submission-header">
                                    <div>
                                        <div class="submission-name">
                                            <?php echo htmlspecialchars($contact['name']); ?>
                                        </div>
                                        <div style="font-size: 0.875rem; color: var(--text-muted);">
                                            <?php echo htmlspecialchars($contact['email']); ?>
                                        </div>
                                    </div>
                                    <div class="submission-date">
                                        <?php echo date('M j, Y \a\t g:i A', strtotime($contact['submitted_at'])); ?>
                                    </div>
                                </div>
                                
                                <div class="submission-subject">
                                    <?php echo htmlspecialchars($contact['subject']); ?>
                                </div>
                                
                                <div class="submission-message">
                                    <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
                                </div>
                                
                                <div class="submission-rating">
                                    <span style="color: var(--text-secondary); font-size: 0.875rem;">Rating:</span>
                                    <span class="stars">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $contact['rating'] ? '★' : '☆';
                                        }
                                        ?>
                                    </span>
                                    <span style="color: var(--text-muted); font-size: 0.875rem;">
                                        (<?php echo $contact['rating']; ?>/5)
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div style="margin-top: 4rem; background-color: var(--bg-secondary); padding: 2rem; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color);">
            <h3 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">
                Other Ways to Reach Us
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center;">
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                        <span class="material-icons" style="font-size: 2rem; color: var(--primary-color);">email</span>
                    </div>
                    <h4 style="color: var(--text-primary); margin-bottom: 0.5rem;">Email</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">info@educonnect.com</p>
                </div>
                
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                        <span class="material-icons" style="font-size: 2rem; color: var(--primary-color);">phone</span>
                    </div>
                    <h4 style="color: var(--text-primary); margin-bottom: 0.5rem;">Phone</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">+1 (555) 123-4567</p>
                </div>
                
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                        <span class="material-icons" style="font-size: 2rem; color: var(--primary-color);">chat</span>
                    </div>
                    <h4 style="color: var(--text-primary); margin-bottom: 0.5rem;">Live Chat</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Available 24/7</p>
                </div>
                
                <div>
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                        <span class="material-icons" style="font-size: 2rem; color: var(--primary-color);">schedule</span>
                    </div>
                    <h4 style="color: var(--text-primary); margin-bottom: 0.5rem;">Response Time</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Within 24 hours</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-populate form with user data if empty
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    
    if (!nameInput.value) {
        nameInput.value = '<?php echo addslashes($_SESSION['user_name']); ?>';
    }
    
    if (!emailInput.value) {
        emailInput.value = '<?php echo addslashes($_SESSION['user_email']); ?>';
    }
    
    // Focus on subject field since name and email are pre-filled
    const subjectInput = document.getElementById('subject');
    if (subjectInput) {
        subjectInput.focus();
    }
});

// Show success modal if message was sent
<?php if ($success): ?>
document.addEventListener('DOMContentLoaded', function() {
    showSuccess('Your message has been sent successfully! We\'ll get back to you soon.');
    
    // Scroll to submissions section to show the new entry
    setTimeout(() => {
        const submissionsSection = document.querySelector('.submissions-section');
        if (submissionsSection) {
            submissionsSection.scrollIntoView({ behavior: 'smooth' });
        }
    }, 2000);
});
<?php endif; ?>

// Character counter for message textarea
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const maxLength = messageTextarea.maxLength;
    
    // Create character counter
    const counter = document.createElement('div');
    counter.style.cssText = 'text-align: right; font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;';
    messageTextarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - messageTextarea.value.length;
        counter.textContent = `${messageTextarea.value.length}/${maxLength} characters`;
        counter.style.color = remaining < 50 ? 'var(--warning-color)' : 'var(--text-muted)';
    }
    
    messageTextarea.addEventListener('input', updateCounter);
    updateCounter();
});
</script>

<?php
require_once '../includes/footer.php';
?>
