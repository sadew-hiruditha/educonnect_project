</main>
    
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title"><?php echo APP_NAME; ?></h3>
                    <p class="footer-description">Your ultimate connection to academic success.</p>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="profile.php">Profile</a></li>
                            <li><a href="contact.php">Contact</a></li>
                        <?php else: ?>
                            <li><a href="register.php">Register</a></li>
                            <li><a href="login.php">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Contact Info</h4>
                    <p class="footer-info">Email: info@studylink.com</p>
                    <p class="footer-info">Phone: (555) 123-4567</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Modal for alerts and confirmations -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
    <script src="js/validation.js"></script>
    <script src="js/passwordToggle.js"></script>
</body>
</html>
