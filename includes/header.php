<?php
$currentPage = getCurrentPage();
$pageTitle = APP_NAME;

// Set page-specific titles
switch ($currentPage) {
    case 'index':
        $pageTitle = 'Home - ' . APP_NAME;
        break;
    case 'register':
        $pageTitle = 'Register - ' . APP_NAME;
        break;
    case 'login':
        $pageTitle = 'Login - ' . APP_NAME;
        break;
    case 'profile':
        $pageTitle = 'Profile - ' . APP_NAME;
        break;
    case 'contact':
        $pageTitle = 'Contact - ' . APP_NAME;
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php" class="nav-logo">
                    <span class="logo-text"><?php echo APP_NAME; ?></span>
                </a>
                
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">Home</a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a href="profile.php" class="nav-link <?php echo $currentPage === 'profile' ? 'active' : ''; ?>">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a href="contact.php" class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="logout.php" class="nav-link">Logout</a>
                        </li> -->
                        <li class="nav-item user-info">
                            <span class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="register.php" class="nav-link <?php echo $currentPage === 'register' ? 'active' : ''; ?>">Register</a>
                        </li>
                        <li class="nav-item">
                            <a href="login.php" class="nav-link <?php echo $currentPage === 'login' ? 'active' : ''; ?>">Login</a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">
                            <span class="theme-icon material-icons">dark_mode</span>
                        </button>
                    </li>
                </ul>
                
                <div class="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="main-content">
