<?php
/**
 * StudyLink - Home Page
 * Welcome page with navigation and call-to-action buttons
 */

require_once '../includes/config.php';
require_once '../includes/header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Welcome to StudyLink</h1>
            <p class="hero-subtitle">
                Your ultimate connection to academic success. 
                Join our community to access resources, share knowledge, and excel together.
            </p>
            
            <div class="cta-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="profile.php" class="btn btn-primary btn-lg">View Profile</a>
                    <a href="contact.php" class="btn btn-outline btn-lg">Contact Us</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="login.php" class="btn btn-outline btn-lg">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="section">
        <div class="section-content">
            <h2 class="section-title">Why Choose StudyLink?</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">school</span></div>
                    <h3>Quality Education</h3>
                    <p>Access high-quality educational resources and connect with experienced educators from around the world.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">people</span></div>
                    <h3>Community Support</h3>
                    <p>Join a supportive community of learners and educators who are passionate about knowledge sharing.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">touch_app</span></div>
                    <h3>Easy to Use</h3>
                    <p>Our platform is designed with simplicity in mind, making it easy for everyone to navigate and use.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">security</span></div>
                    <h3>Secure Platform</h3>
                    <p>Your privacy and security are our top priorities. We use industry-standard security measures.</p>
                </div>
                
               
                
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section class="section" style="background-color: var(--bg-secondary);">
        <div class="section-content">
            <h2 class="section-title">About StudyLink</h2>
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <p style="font-size: 1.125rem; margin-bottom: 2rem;">
                    StudyLink is a modern educational platform designed to bridge the gap between students and educators. 
                    Our mission is to create an inclusive learning environment where knowledge flows freely and everyone 
                    has the opportunity to grow.
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 3rem;">
                    <div>
                        <h3 style="color: var(--primary-color); font-size: 2rem; margin-bottom: 0.5rem;">1000+</h3>
                        <p>Active Students</p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary-color); font-size: 2rem; margin-bottom: 0.5rem;">50+</h3>
                        <p>Expert Educators</p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary-color); font-size: 2rem; margin-bottom: 0.5rem;">100+</h3>
                        <p>Courses Available</p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary-color); font-size: 2rem; margin-bottom: 0.5rem;">24/7</h3>
                        <p>Support Available</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Call to Action Section -->
    <?php if (!isLoggedIn()): ?>
    <section class="section">
        <div class="section-content" style="text-align: center;">
            <h2 class="section-title">Ready to Get Started?</h2>
            <p style="font-size: 1.125rem; margin-bottom: 2rem; color: var(--text-secondary);">
                Join thousands of students and educators who are already part of the StudyLink community.
            </p>
            
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-primary btn-lg">Create Account</a>
                <a href="login.php" class="btn btn-secondary btn-lg">Already have an account?</a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Logged-in User Dashboard Preview -->
    <?php if (isLoggedIn()): ?>
    <section class="section">
        <div class="section-content">
            <h2 class="section-title">Your Dashboard</h2>
            
            <div class="features-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">person</span></div>
                    <h3>Profile</h3>
                    <p>View and manage your personal information and account settings.</p>
                    <a href="profile.php" class="btn btn-primary" style="margin-top: 1rem;">View Profile</a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">contact_mail</span></div>
                    <h3>Contact</h3>
                    <p>Get in touch with our support team or provide feedback about your experience.</p>
                    <a href="contact.php" class="btn btn-primary" style="margin-top: 1rem;">Contact Us</a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><span class="material-icons" style="font-size: 3rem; color: var(--primary-color);">library_books</span></div>
                    <h3>Resources</h3>
                    <p>Access educational materials, guides, and helpful resources.</p>
                    <button class="btn btn-secondary" style="margin-top: 1rem;" onclick="showModal('Coming Soon', 'This feature will be available in the next update!', 'info')">Browse Resources</button>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php
require_once '../includes/footer.php';
?>
