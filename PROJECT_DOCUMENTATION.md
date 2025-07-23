# StudyLink Web Application - Project Documentation

## Table of Contents
1. [Project Overview](#1-project-overview)
2. [Folder Structure](#2-folder-structure)
3. [Page Descriptions](#3-page-descriptions)
4. [JavaScript Functions](#4-javascript-functions)
5. [PHP Backend Logic](#5-php-backend-logic)
6. [Challenges and Improvements](#6-challenges-and-improvements)
7. [How to Run the Project](#7-how-to-run-the-project)
8. [Screenshots](#8-screenshots)

---

## 1. Project Overview

### Purpose
StudyLink is a modern educational web platform designed to bridge the gap between students and educators. The application provides a secure user authentication system with registration, login, and profile management capabilities. It serves as a foundation for an educational community where users can connect, share knowledge, and access learning resources.

### Main Features Implemented
- **User Registration System**: Secure account creation with form validation
- **User Authentication**: Login/logout functionality with session management
- **Protected User Profiles**: Personalized user dashboards with account information
- **Contact System**: Message submission and storage functionality
- **Responsive Design**: Mobile-friendly interface that adapts to different screen sizes
- **Theme Toggle**: Light/dark mode switching with local storage persistence
- **Form Validation**: Client-side and server-side validation for data integrity
- **Security Features**: CSRF protection, input sanitization, and session management

### Technologies Used
- **HTML5**: Semantic markup for page structure and content
- **CSS3**: Modern styling with CSS variables, flexbox, and grid layouts
- **JavaScript (ES6)**: Interactive functionality and form validation
- **PHP 7.4+**: Server-side logic and data processing
- **JSON**: Data storage for users and contact submissions
- **Material Icons**: Icon library for enhanced user interface

---

## 2. Folder Structure

```
educonnect_project/
├── .htaccess                    # Apache configuration for clean URLs
├── includes/                    # PHP includes and configuration
│   ├── config.php              # Application configuration and constants
│   ├── functions.php           # Helper functions and utilities
│   ├── header.php              # Common header template
│   └── footer.php              # Common footer template
├── public/                      # Public web files
│   ├── index.php               # Homepage
│   ├── login.php               # User login page
│   ├── register.php            # User registration page
│   ├── profile.php             # User profile dashboard
│   ├── contact.php             # Contact form page
│   ├── logout.php              # Logout handler
│   ├── css/                    # Stylesheets
│   │   ├── main.css           # Main stylesheet import
│   │   ├── variables.css      # CSS custom properties
│   │   ├── base.css           # Base styles and typography
│   │   ├── layout.css         # Layout and container styles
│   │   ├── navigation.css     # Header and navigation styles
│   │   ├── components.css     # Reusable component styles
│   │   ├── forms.css          # Form styling
│   │   ├── pages.css          # Page-specific styles
│   │   └── responsive.css     # Media queries and responsive design
│   └── js/                     # JavaScript files
│       ├── script.js          # Main JavaScript functionality
│       ├── validation.js      # Form validation functions
│       └── passwordToggle.js  # Password visibility toggle
└── storage/                     # Data storage (JSON files)
    ├── users.json              # User account data
    └── contacts.json           # Contact form submissions
```

### Component Locations
- **HTML**: Embedded within PHP files in the `/public` directory
- **CSS**: Modular stylesheets in `/public/css/` for maintainable styling
- **JavaScript**: Interactive scripts in `/public/js/` for client-side functionality
- **PHP**: Server-side logic distributed across `/public` and `/includes` directories

---

## 3. Page Descriptions

### Home Page (`index.php`)
- **Purpose**: Landing page and application introduction
- **Features**: 
  - Hero section with call-to-action buttons
  - Feature showcase highlighting platform benefits
  - About section with platform statistics
  - Conditional content based on user login status
  - Dashboard preview for logged-in users

### Registration Page (`register.php`)
- **Purpose**: New user account creation
- **Features**:
  - Registration form with name, email, and password fields
  - Password confirmation validation
  - Real-time form validation
  - Email uniqueness checking
  - Success/error message display
  - Automatic redirect to login upon successful registration

### Login Page (`login.php`)
- **Purpose**: User authentication and session establishment
- **Features**:
  - Login form with email and password fields
  - Remember login state across sessions
  - Password visibility toggle
  - CSRF token protection
  - Failed login attempt handling
  - Redirect to intended page after login

### Profile Page (`profile.php`)
- **Purpose**: User dashboard and account management
- **Features**:
  - Tabbed interface (Profile Overview / Edit Profile)
  - User information display with avatar
  - Profile editing capabilities
  - Account statistics and activity overview
  - Quick action buttons for common tasks
  - Session-protected access

### Contact Page (`contact.php`)
- **Purpose**: User communication and feedback collection
- **Features**:
  - Contact form with name, email, subject, and message fields
  - Rating system for user experience
  - Form validation and submission handling
  - Message storage in JSON format
  - Success confirmation upon submission
  - Contact submission history for logged-in users

### Logout Handler (`logout.php`)
- **Purpose**: Session termination and security cleanup
- **Features**:
  - Session destruction and cleanup
  - Security token invalidation
  - Redirect to login page with confirmation message

---

## 4. JavaScript Functions

### Theme Management (`script.js`)
- **Dark/Light Mode Toggle**: Persistent theme switching using localStorage
- **Theme Preference Storage**: Saves user theme choice between sessions
- **Dynamic Icon Updates**: Changes theme toggle icon based on current mode

### Form Validation (`validation.js`)
- **Real-time Validation**: Instant feedback during form input
- **Custom Validation Rules**: Email format, password strength, field requirements
- **Error Message Display**: Clear, user-friendly validation messages
- **Form Submission Prevention**: Blocks invalid form submissions

### Password Security (`passwordToggle.js`)
- **Password Visibility Toggle**: Show/hide password functionality
- **Icon State Management**: Dynamic eye icon updates
- **Multiple Password Field Support**: Handles password and confirm password fields

### User Interface Interactions (`script.js`)
- **Modal System**: Reusable modal dialogs for alerts and confirmations
- **Mobile Menu**: Responsive hamburger menu for mobile navigation
- **Form Enhancement**: Loading states and submission feedback
- **Tab Switching**: Profile page tab functionality

### Utility Functions
- **Input Sanitization**: Client-side data cleaning
- **Copy to Clipboard**: User ID copying functionality
- **Animation Triggers**: Subtle page load animations
- **Error Handling**: Graceful error message display

---

## 5. PHP Backend Logic

### User Registration Process
```php
// Data validation and sanitization
$name = sanitizeInput($_POST['name']);
$email = sanitizeInput($_POST['email']);
$password = $_POST['password'];

// Email uniqueness check
if (userExists($email)) {
    $errors[] = 'Account already exists';
}

// Password hashing and user creation
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$userId = generateUniqueId();
```

### Authentication System
- **Password Hashing**: Uses PHP's `password_hash()` with PASSWORD_DEFAULT algorithm
- **Login Verification**: Secure password verification with `password_verify()`
- **Session Management**: Secure session handling with user ID, name, and email storage
- **CSRF Protection**: Token-based protection against cross-site request forgery

### Session Management
```php
// Session initialization
session_start();

// User authentication check
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Session security
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
```

### Access Control
- **Protected Pages**: Profile and contact pages require authentication
- **Redirect Handling**: Automatic redirection to login for unauthorized access
- **Post-login Redirection**: Returns users to originally requested page
- **Session Validation**: Continuous session state checking

### Data Storage
- **JSON File System**: Lightweight data storage using structured JSON files
- **User Data**: Secure storage in `/storage/users.json` with hashed passwords
- **Contact Data**: Message storage in `/storage/contacts.json` with timestamps
- **Data Integrity**: File existence checks and automatic initialization

---

## 6. Challenges and Improvements

### Development Challenges
1. **Form Validation Consistency**: Ensuring client-side and server-side validation alignment
2. **Session Security**: Implementing proper session management and CSRF protection
3. **Responsive Design**: Creating a mobile-friendly interface across all devices
4. **File Permission Issues**: Managing JSON file read/write permissions on different servers
5. **Browser Compatibility**: Ensuring consistent behavior across different browsers

### Implemented Improvements
1. **Enhanced Security**:
   - CSRF token implementation for form protection
   - Input sanitization to prevent XSS attacks
   - Secure password hashing using PHP's built-in functions

2. **User Experience Enhancements**:
   - Theme toggle with persistent storage
   - Real-time form validation feedback
   - Loading states for form submissions
   - Responsive design for mobile devices

3. **Code Organization**:
   - Modular CSS architecture for maintainability
   - Separation of concerns with includes structure
   - Reusable JavaScript functions
   - Consistent naming conventions

4. **Performance Optimizations**:
   - CSS and JavaScript minification ready structure
   - Efficient JSON data handling
   - Optimized image and icon usage
   - Progressive enhancement approach

---

## 7. How to Run the Project

### Prerequisites
- Local server environment (XAMPP, WAMP, MAMP, or LAMP)
- PHP 7.4 or higher
- Apache web server with mod_rewrite enabled

### Step-by-Step Setup

1. **Download and Install Server Environment**
   ```bash
   # Download XAMPP from https://www.apachefriends.org/
   # Install and start Apache and MySQL services
   ```

2. **Project Installation**
   ```bash
   # Copy project folder to server directory
   # For XAMPP: Copy to C:/xampp/htdocs/
   # For WAMP: Copy to C:/wamp64/www/
   ```

3. **Set Folder Permissions** (Linux/Mac)
   ```bash
   chmod 755 educonnect_project/
   chmod 777 educonnect_project/storage/
   chmod 666 educonnect_project/storage/*.json
   ```

4. **Access the Application**
   ```
   Open browser and navigate to:
   http://localhost/educonnect_project/public/
   ```

### Default Test Accounts
Since the application uses dynamic registration, create test accounts through the registration page:

**Test User 1:**
- Name: John Doe
- Email: john@example.com
- Password: password123

**Test User 2:**
- Name: Jane Smith
- Email: jane@example.com
- Password: password123

### Configuration Notes
- Ensure `storage/` directory has write permissions
- Verify `.htaccess` is enabled for clean URLs
- Check PHP error logs for troubleshooting
- Disable production settings in development (error reporting, etc.)

### Troubleshooting Common Issues
1. **File Permission Errors**: Ensure web server can write to storage directory
2. **Session Issues**: Check PHP session configuration
3. **404 Errors**: Verify .htaccess and mod_rewrite are enabled
4. **JSON Parse Errors**: Check JSON file format and permissions

---

## 8. Screenshots

### Key Pages and Features

#### Homepage - Hero Section
![Homepage Hero](screenshots/homepage-hero.png)
*Landing page with call-to-action buttons and platform introduction*

#### Registration Page
![Registration Form](screenshots/registration-form.png)
*User registration with real-time validation and error handling*

#### Login Page
![Login Form](screenshots/login-form.png)
*Secure login interface with password visibility toggle*

#### User Profile Dashboard
![Profile Dashboard](screenshots/profile-dashboard.png)
*Protected user profile with tabbed interface and account information*

#### Contact Form
![Contact Form](screenshots/contact-form.png)
*Contact submission with rating system and form validation*

#### Theme Toggle Functionality
![Dark Mode](screenshots/dark-mode.png)
*Dark mode interface showing theme consistency across components*

#### Mobile Responsive Design
![Mobile Navigation](screenshots/mobile-menu.png)
*Responsive hamburger menu and mobile-optimized layout*

#### Form Validation
![Validation Messages](screenshots/form-validation.png)
*Real-time validation feedback with clear error messages*

### Feature Annotations
- **✅ Success States**: Green confirmation messages and success indicators
- **❌ Error Handling**: Red error messages with clear instructions
- **🔒 Security Features**: CSRF tokens and secure session management
- **📱 Responsive Design**: Mobile-first approach with touch-friendly interfaces
- **🎨 Theme System**: Consistent theming across light and dark modes

---

## Conclusion

StudyLink represents a comprehensive web application built with modern web technologies and best practices. The project demonstrates proficiency in full-stack web development, including secure user authentication, responsive design, and maintainable code architecture. The modular structure and documented codebase provide a solid foundation for future enhancements and scalability.

### Future Development Possibilities
- Database integration (MySQL/PostgreSQL)
- Email verification system
- Advanced user roles and permissions
- Course management features
- Real-time messaging system
- API development for mobile applications

---

*Documentation prepared for StudyLink Web Application*  
*Version 1.0 - July 2025*
