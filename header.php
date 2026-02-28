<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'] ?? 1;
}
$isEmpty = ($cartCount == 0);

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <title><?php echo isset($pageTitle) ? $pageTitle : 'Royals - Quality Sneakers'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
:root {
    --primary: white;
    --primary-light: #15253f;
    --secondary: #3a7bd5;
    --accent: #00d2ff;
    --text: #333;
    --text-secondary: #666;
    --footer-dark: #080f1a;
    --background: white;
    --card-bg: white;
    --border-color: #e0e0e0;
}

[data-theme="dark"] {
    --primary: #080f1a;
    --primary-light: #15253f;
    --secondary: #3a7bd5;
    --accent: #00d2ff;
    --text: #f5f5f5;
    --text-secondary: #cccccc;
    --footer-dark: #080f1a;
    --background: #080f1a;
    --card-bg: #0e1726;
    --border-color: #1e293b;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
     font-family: 'Exo', sans-serif;
    background-color: var(--background);
    color: var(--text);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    padding-top: 30px;
    transition: background-color 0.3s, color 0.3s;
}

header {
    height: 100px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: black;
    border-bottom: 1px solid var(--border-color);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    padding: 15px 25px;
    transition: all 0.3s ease;
}

.main-header {
    display: flex;
    justify-content: space-between; /* Pushes items to the edges */
    align-items: center;
    padding: 15px 20px;
    width: 100%;
    box-sizing: border-box;
}



.header-left, .header-center, .header-right {
    flex: 1;
    display: flex;
    align-items: center;
}

/* Now align the contents of each slot */
.header-left {
    justify-content: flex-start;
    gap: 50px;
}

.header-center {
    justify-content: center;     /* Forces the logo to be perfectly centered */
}

.header-right {
    justify-content: flex-end;
    gap: 30px;
    margin-left: 10px;
}

.icon-button{
    font-size: 1.3em;
    background: transparent;
    cursor: pointer;
    color: var(--text-secondary);
    border: none;
}

.icon-button:hover {
    color: var(--accent);
}


/* The Underlined Search */
.search-wrapper {
    display: flex;
    align-items: center;
    gap: 1px;
}

.search-input {
    background: transparent;
    border: none;
    border-bottom: 1px solid #333; /* The underline */
    outline: none;
    width: 100px; /* Adjust this width based on your needs */
    padding-bottom: 2px;
    transition: width 0.3s ease;
    font-size: 1.5em;
}

@media (max-width: 992px) {

}

@media (max-width: 768px) {
    .header-left{
        gap: 15px;
    }
    .header-center.img {
        width: 500px;
    }
    .header-right {
        gap: 10px;
    }
    .search-input {
        width: 40px;
        padding-bottom: 2px;
        font-size: 0.8em;
    }
    .icon-button{
        font-size: 0.8em;
    }
}

@media (max-width: 480px) {
    .header-left{
        gap: 15px;
    }
    .header-center.img {
        width: 500px;
    }
    .header-right {
        gap: 10px;
    }
    .search-input {
        width: 40px;
        padding-bottom: 2px;
        font-size: 0.8em;
    }
    .icon-button{
        font-size: 0.8em;
    }
    .main-header {
        display: flex;
        padding: 10px 10px;
    }
}

@media (prefers-color-scheme: dark) {
  .search-input  {
    color: #fff;
  }
}

.search-input:focus {
    width: 120px; /* Grows when clicked */
    border-bottom: 1px solid #000; /* Darker line on focus */
}

.header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1400px;
    margin: 0 auto;
    gap: 25px;
}

.left-section {
    display: flex;
    align-items: center;
    gap: 20px;
}

.ham-menu {
    width: 28px;
    height: 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    position: relative;
}

.ham-menu span {
    display: block;
    width: 100%;
    height: 3px;
    background: var(--text);
    border-radius: 3px;
    transition: all 0.3s ease;
    transform-origin: center;
}

.ham-menu.active span:nth-child(1) {
    transform: translateY(9px) rotate(45deg);
}

.ham-menu.active span:nth-child(2) {
    opacity: 0;
}

.ham-menu.active span:nth-child(3) {
    transform: translateY(-9px) rotate(-45deg);
}

.logo img {
    height: 90px;
    width: auto;
    border-radius: 100px;
    transition: transform 0.3s ease;

}

.logo:hover img {
    transform: scale(1.05);
}

.search-container {
    flex: 1;
    max-width: 600px;
    position: relative;
}

.search-bar {
    width: 100%;
    padding: 12px 20px 12px 45px;
    border: 1px solid var(--border-color);
    border-radius: 30px;
    font-size: 16px;
    background: var(--card-bg);
    color: var(--text);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.search-bar:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.2);
    outline: none;
}

.search-button {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    font-size: 16px;
}

.search-button:hover {
    color: var(--accent);
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background-color: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    max-height: 400px;
    overflow-y: auto;
    z-index: 999;
    display: none;
}

.search-suggestions.active {
    display: block;
}

.suggestion-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.2s;
    border-bottom: 1px solid var(--border-color);
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item:hover {
    background-color: rgba(0, 210, 255, 0.1);
}

.suggestion-image {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 15px;
}

.suggestion-name {
    font-weight: 500;
    color: var(--text);
    flex-grow: 1;
}

.suggestion-price {
    font-size: 14px;
    color: var(--accent);
    margin-left: 10px;
}

.no-results {
    padding: 15px;
    color: var(--text-secondary);
    text-align: center;
}

.nav-container {
    display: flex;
    align-items: center;
    gap: 20px;
}

.theme-toggle, 
.cart-icon1 {
    background: transparent;
    border: none;
    color: var(--text);
    font-size: 20px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-icon1.cart-empty {
    color: #ff4757 !important;
}


.theme-toggle:hover, 
.cart-icon1:hover {
    color: var(--accent);
    background: rgba(0, 210, 255, 0.1);
}

.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--accent);
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.off-screen-menu {
    position: fixed;
    top: 0;
    left: -100%;
    width: 300px;
    max-width: 90%;
    height: 100vh;
    background-color: var(--card-bg);
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    z-index: 1000;
    transition: left 0.3s;
    padding: 20px;
    overflow-y: auto;
}

.off-screen-menu.active {
    left: 0;
}

.off-screen-menu ul {
    list-style: none;
    margin-top: 50px;
}

.off-screen-menu li {
    margin-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 10px;
}

.off-screen-menu a {
    display: block;
    padding: 8px 0;
    color: var(--text);
    font-weight: 500;
    transition: color 0.3s;
    text-decoration: none;
}

.off-screen-menu a:hover {
    color: var(--accent);
}

.menu-close {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 24px;
    color: var(--text-secondary);
    cursor: pointer;
}

.navlogo {
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 20px;
}

.navlogo img {
    height: 70px;
    width:150px;
    border-radius: 5px;
}

.cart-modal {
    position: fixed;
    top: 0;
    right: -100%;
    width: 400px;
    max-width: 90%;
    height: 100vh;
    background-color: var(--card-bg);
    box-shadow: -2px 0 10px rgba(0,0,0,0.1);
    z-index: 1001;
    transition: right 0.3s;
    padding: 20px;
    overflow-y: auto;
}

.cart-modal.active {
    right: 0;
}

.cart-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.cart-modal-title {
    font-size: 1.5rem;
    color: var(--text);
}

.cart-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-secondary);
    cursor: pointer;
}

.cart-items {
    margin-bottom: 20px;
}

.cart-item {
    display: flex;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.cart-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 15px;
}

.cart-item-details {
    flex: 1;
}

.cart-item-name {
    font-weight: 500;
    margin-bottom: 5px;
    color: var(--text);
}

.cart-item-price {
    color: var(--accent);
    font-weight: bold;
    margin-bottom: 5px;
}

.cart-item-quantity {
    display: flex;
    align-items: center;
}

.quantity-btn {
    background-color: var(--border-color);
    border: none;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.quantity-value {
    margin: 0 10px;
}

.remove-item {
    background: none;
    border: none;
    color: #ff4757;
    cursor: pointer;
    font-size: 0.9rem;
    margin-top: 5px;
}

.cart-total {
    font-size: 1.2rem;
    font-weight: bold;
    margin: 20px 0;
    text-align: right;
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cart-btn {
    padding: 12px;
    border-radius: 5px;
    font-weight: 500;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
}

.view-cart-btn {
    background-color: var(--border-color);
    color: var(--text);
    border: none;
}

.checkout-btn {
    background-color: var(--accent);
    color: white;
    border: none;
}

.empty-cart {
    text-align: center;
    padding: 40px 0;
    color: var(--text-secondary);
}

.empty-cart i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: var(--border-color);
}

.notification {
    position: fixed;
    bottom: 20px;
    left: 20px;
    background-color: var(--card-bg);
    color: var(--text);
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: none;
    align-items: center;
    justify-content: space-between;
    z-index: 1000;
    max-width: 350px;
    border-left: 4px solid var(--accent);
}

.notification.active {
    display: flex;
    animation: slideIn 0.5s forwards, fadeOut 0.5s forwards 3s;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.notification img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.close-notification {
    background: none;
    border: none;
    color: var(--text-secondary);
    font-size: 20px;
    cursor: pointer;
    margin-left: 15px;
}

@keyframes slideIn {
    from {
        transform: translateY(100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeOut {
    to {
        opacity: 0;
        transform: translateY(20px);
    }
}

/* Back to Top Button */
.back-to-top {
    position: fixed;
    left: 20px;
    bottom: 20px;
    width: 50px;
    height: 50px;
    background-color: var(--accent);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    display: none; /* Hidden by default */
    justify-content: center;
    align-items: center;
    z-index: 999;
    box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
    transition: all 0.3s ease;
    opacity: 0.9;
}

.back-to-top:hover {
    opacity: 1;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 210, 255, 0.4);
}

@media (max-width: 992px) {
    .header-inner {
        gap: 15px;
    }
    
    .search-bar {
        padding-left: 40px;
    }
}

@media (max-width: 768px) {
    header {
        padding: 12px 15px;
        height: 80px;
    }
    body{
        padding-top: 1px; /* Increase for mobile */
    }
    .header-inner {
        flex-wrap: wrap;
    }
    
    .left-section {
        order: 1;
    }
    
    .nav-container {
        order: 2;
        margin-left: auto;
    }
    
    .search-container {
        order: 3;
        flex: 0 0 100%;
        margin-top: 15px;
        max-width: 100%;
    }
    
    .logo img {
        height: 70px;
       width:150px;
    }
 
    .back-to-top {
        left: 15px;
        bottom: 45px;
        width: 45px;
        height: 60px;
        font-size: 18px;
    }
}

@media (max-width: 480px) {
        header {
        padding: 12px 15px;
        height: 80px;
    }
    body{
        padding-top: 50px; /* Increase for mobile */
    } 

    .left-section {
        gap: 15px;
    }
    
    .ham-menu {
        width: 20px;
        height: 16px;
    }
    
    .logo img {
        height: 36px;
        width: 60px;
    }
    
    .search-bar {
        padding: 10px 15px 10px 35px;
        font-size: 14px;
    }
    
    .search-button {
        left: 10px;
        font-size: 14px;
    }
    
    .nav-container {
        gap: 150px;
    }
    
    .back-to-top {
        left: 10px;
        bottom: 10px;
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

/* App Modal Styles */
.app-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 2000;
    justify-content: center;
    align-items: center;
    font-family: 'Exo', sans-serif;
}

.modal-content {
    background: rgba(0, 0, 0, 0.7);
    width: 90%;
    max-width: 450px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    animation: modalFadeIn 0.5s ease-out;
    color: white;
    text-align: center;
    position: relative;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-50px) scale(0.9); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.modal-header {
    padding: 20px;
    background: rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.app-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    margin-right: 15px;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.1);
}

.app-info {
    text-align: left;
}

.app-name {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 0 5px 0;
    color: #00d2ff;
}

.app-tagline {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 0;
}

.modal-body {
    padding: 25px;
}

.modal-title {
    font-size: 1.3rem;
    margin: 0 0 15px 0;
    color: #00d2ff;
}

.features {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin: 20px 0;
}

.feature {
    flex: 1;
    min-width: 120px;
    background: rgba(255, 255, 255, 0.05);
    padding: 12px;
    border-radius: 10px;
    transition: all 0.3s;
}

.feature:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-3px);
}

.feature i {
    font-size: 1.5rem;
    color: #00d2ff;
    margin-bottom: 8px;
}

.feature p {
    margin: 0;
    font-size: 0.85rem;
}

.download-btn {
    display: block;
    width: 100%;
    padding: 15px;
    background: linear-gradient(to right, #00d2ff, #3a7bd5);
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
    margin: 20px 0 15px;
}

.download-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 210, 255, 0.4);
    background: linear-gradient(to right, #00c5ef, #2f6ac1);
}

.close-btn {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s;
    width: 100%;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.modal-footer {
    padding: 15px;
    background: rgba(0, 0, 0, 0.2);
    font-size: 0.8rem;
    opacity: 0.7;
}
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <button class="ham-menu" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </button>
            <div class="search-wrapper">
                <form action="search_result.php" method="get" class="search-form-flex">
                    <button type="submit" class="icon-button">
                        <i class="fas fa-search"></i>
                    </button>
                    
                    <input type="text" 
                        name="query" 
                        class="search-input" 
                        id="searchInput" 
                        placeholder="Search" 
                        value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                </form>

                <div class="search-suggestions" id="searchSuggestions">
                    <div class="suggestions-container" id="suggestionsContainer"></div>
                </div>
            </div>
        </div>

        <div class="header-center">
            <a href="index.php" class="logo">
                <img src="uploads/logo.jpeg" alt="Royals Logo">
            </a>
        </div>

        <div class="header-right">
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
           <div class="cart-icon1 <?php echo $isEmpty ? 'cart-empty' : 'cart-filled'; ?>" id="cartIcon">
                <i class="fas fa-shopping-cart"></i>

                <?php if (!$isEmpty): ?>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <div class="off-screen-menu" id="menu">
        <div class="menu-close" onclick="toggleMenu()">
            <i class="fas fa-times"></i>
        </div>
        <div class="navlogo">
            <a href="index.php">
                <img src="uploads/logo.jpeg" alt="Royals Logo">
            </a>
        </div>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#" id="mobileCartBtn"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <li><a href="orders.php"><i class="fas fa-box"></i> My Orders</a></li>
            <li><a href="about_us.php"><i class="fas fa-info-circle"></i> Shipping Policy</a></li>
            <li><a href="services.php"><i class="fas fa-concierge-bell"></i> Returning Policy</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="logout.php" style="color: var(--accent);"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" style="color: #4ea8de;"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <?php endif; ?>
            <li><a href="admin_dashboard.php" style="color: skyblue;"><i class="fas fa-user-shield"></i> Admin</a></li>
        </ul>
    </div>
    
    <div class="cart-modal" id="cartModal">
        <div class="cart-modal-header">
            <h3 class="cart-modal-title">Your Cart</h3>
            <button class="cart-close" id="cartClose">&times;</button>
        </div>
        <div class="cart-items" id="cartItems"></div>
        <div class="cart-total" id="cartTotal">Total: Ksh 0.00</div>
        <div class="cart-actions">
            <a href="cart.php" class="cart-btn view-cart-btn">View Cart</a>
            <a href="checkout.php" class="cart-btn checkout-btn">Checkout</a>
        </div>
    </div>
    
    <div class="notification" id="notification">
        <div class="notification-content">
            <img id="notificationImage" src="" alt="Product">
            <span id="notificationMessage"></span>
        </div>
        <button class="close-notification" id="closeNotification">&times;</button>
    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- App Download Modal -->
    <div class="app-modal" id="appModal">
        <div class="modal-content">
            <div class="modal-header">
                <img src="uploads/logo.jpeg" alt="Royals App Icon" class="app-icon">
                <div class="app-info">
                    <h2 class="app-name">Royals App</h2>
                </div>
            </div>
            
            <!-- <div class="modal-body">
                <h3 class="modal-title">Get the Best Shopping Experience</h3>
                <p>Enjoy our app-exclusive deals, faster checkout, and personalized recommendations.</p>
                
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-bolt"></i>
                        <p>Faster Experience</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-lock"></i>
                        <p>Secure Payments</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-gift"></i>
                        <p>Exclusive Offers</p>
                    </div>
                </div> -->
                
                <a href="Royals.apk" class="download-btn" id="downloadBtn">
                    <i class="fas fa-download"></i> Download App
                </a>
                
                <button class="close-btn" id="closeModalBtn">
                    Close
                </button>
            </div>
            
            <div class="modal-footer">
                <p>By downloading, you agree to our Terms of Service and Privacy Policy</p>
            </div>
        </div>
    </div>

    <script>
// Theme toggle
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

const savedTheme = localStorage.getItem('theme') || 'system';
const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

if (savedTheme === 'dark' || (savedTheme === 'system' && systemDark)) {
    html.setAttribute('data-theme', 'dark');
    themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
} else {
    html.setAttribute('data-theme', 'light');
    themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
}

themeToggle.addEventListener('click', () => {
    const currentTheme = html.getAttribute('data-theme');
    if (currentTheme === 'dark') {
        html.setAttribute('data-theme', 'light');
        localStorage.setItem('theme', 'light');
        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    } else {
        html.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
});

// Cart modal
const cartIcon = document.getElementById('cartIcon');
const cartModal = document.getElementById('cartModal');
const cartClose = document.getElementById('cartClose');
const mobileCartBtn = document.getElementById('mobileCartBtn');

function toggleCartModal() {
    cartModal.classList.toggle('active');
    if (cartModal.classList.contains('active')) {
        loadCartItems();
    }
}

cartIcon.addEventListener('click', toggleCartModal);
cartClose.addEventListener('click', toggleCartModal);
mobileCartBtn.addEventListener('click', toggleCartModal);

function formatPrice(price) {
    return 'Ksh ' + parseFloat(price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function loadCartItems() {
    fetch('ajax/get_cart.php')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(data => {
            document.getElementById('cartItems').innerHTML = data;
            updateCartTotal();
        })
        .catch(error => {
            console.error('Error loading cart:', error);
            showNotification('Error loading cart items', '', 'error');
        });
}

function updateCartTotal() {
    const items = document.querySelectorAll('.cart-item');
    let total = 0;
    items.forEach(item => {
        const priceText = item.querySelector('.cart-item-price').textContent;
        const price = parseFloat(priceText.replace(/[^0-9.-]+/g,""));
        const quantity = parseInt(item.querySelector('.quantity-value').textContent) || 1;
        total += price * quantity;
    });
    document.getElementById('cartTotal').textContent = `Total: ${formatPrice(total)}`;
}

function showNotification(message, imageUrl, type = 'success') {
    const notification = document.getElementById('notification');
    if (!notification || !message) return;
    
    const notificationMessage = document.getElementById('notificationMessage');
    const notificationImage = document.getElementById('notificationImage');
    
    notificationMessage.textContent = message;
    if (imageUrl) {
        notificationImage.src = imageUrl;
        notificationImage.style.display = 'block';
    } else {
        notificationImage.style.display = 'none';
    }
    notification.className = 'notification ' + type;
    notification.classList.add('active');
    setTimeout(() => {
        notification.classList.remove('active');
    }, 3500);
}

document.getElementById('closeNotification')?.addEventListener('click', () => {
    document.getElementById('notification').classList.remove('active');
});

// Mobile menu
function toggleMenu() {
    const menu = document.getElementById('menu');
    const hamMenu = document.querySelector('.ham-menu');
    menu.classList.toggle('active');
    hamMenu.classList.toggle('active');
    document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
}

// Search functionality
const searchInput = document.getElementById('searchInput');
const searchSuggestions = document.getElementById('searchSuggestions');
const suggestionsContainer = document.getElementById('suggestionsContainer');

let searchTimeout;

searchInput.addEventListener('input', function() {
    const query = this.value.trim();
    clearTimeout(searchTimeout);
    
    if (query.length === 0) {
        searchSuggestions.classList.remove('active');
        return;
    }
    
    suggestionsContainer.innerHTML = '<div class="no-results">Searching...</div>';
    searchSuggestions.classList.add('active');
    
    searchTimeout = setTimeout(() => {
        fetchSearchSuggestions(query);
    }, 300);
});

document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
        searchSuggestions.classList.remove('active');
    }
});

function fetchSearchSuggestions(query) {
    fetch(`search.php?query=${encodeURIComponent(query)}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.length > 0) {
                let html = '';
                data.forEach(product => {
                    html += `
                        <div class="suggestion-item" onclick="selectSuggestion('${product.name.replace("'", "\\'")}')">
                            ${product.image ? `<img src="uploads/${product.image}" alt="${product.name}" class="suggestion-image">` : ''}
                            <div class="suggestion-name">${product.name}</div>
                            <div class="suggestion-price">Ksh ${product.price_ksh}</div>
                        </div>
                    `;
                });
                suggestionsContainer.innerHTML = html;
            } else {
                suggestionsContainer.innerHTML = '<div class="no-results">No products found</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching suggestions:', error);
            suggestionsContainer.innerHTML = '<div class="no-results">Error loading suggestions</div>';
        });
}

function selectSuggestion(productName) {
    searchInput.value = productName;
    searchInput.focus();
    searchSuggestions.classList.remove('active');
    document.querySelector('form').submit();
}

// Cart functions
function addToCart(productId, productName, price, image) {
    if (!productId || !productName || !price) {
        console.error('Invalid product data');
        return;
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('price', price);
    formData.append('image', image || '');
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateCartCountDisplay(data.cartCount);
            showNotification(`${productName} has been added to your cart!`, `uploads/${image}`);
            if (cartModal.classList.contains('active')) {
                loadCartItems();
            }
        } else {
            showNotification(data.message || 'Failed to add to cart', '', 'error');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Error adding to cart', '', 'error');
    });
}

function updateCartCountDisplay(count) {
    let cartCountEl = document.querySelector('.cart-count');
    
    if (!cartCountEl && count > 0) {
        cartCountEl = document.createElement('span');
        cartCountEl.className = 'cart-count';
        document.querySelector('.cart-icon1').appendChild(cartCountEl);
    }
    
    if (cartCountEl) {
        cartCountEl.textContent = count;
        cartCountEl.style.display = count > 0 ? 'flex' : 'none';
    }
}

function updateQuantity(productId, change) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('change', change);
    
    fetch('ajax/update_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            loadCartItems();
            updateCartCountDisplay(data.cartCount);
        } else {
            showNotification('Failed to update quantity', '', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
        showNotification('Error updating quantity', '', 'error');
    });
}

function removeCartItem(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch('ajax/remove_from_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            loadCartItems();
            updateCartCountDisplay(data.cartCount);
            showNotification('Item removed from cart', '', 'success');
        } else {
            showNotification(data.message || 'Failed to remove item', '', 'error');
        }
    })
    .catch(error => {
        console.error('Error removing item:', error);
        showNotification('Error removing item', '', 'error');
    });
}

// Quickview functionality
function quickView(productId) {
    console.log('QuickView called for product ID:', productId);
    
    // Ensure the modal exists
    let quickviewModal = document.getElementById('quickviewModal');
    if (!quickviewModal) {
        console.log('Creating quickview modal...');
        quickviewModal = document.createElement('div');
        quickviewModal.id = 'quickviewModal';
        quickviewModal.className = 'modal-overlay';
        quickviewModal.innerHTML = `
            <div class="quickview-modal-content">
                <button class="quickview-close" id="quickviewClose">×</button>
                <div id="quickviewContent"></div>
            </div>
        `;
        document.body.appendChild(quickviewModal);
        
        // Add event listeners for the new modal
        document.getElementById('quickviewClose').onclick = () => {
            quickviewModal.classList.remove('active');
        };
        
        quickviewModal.addEventListener('click', (e) => {
            if (e.target === quickviewModal) {
                quickviewModal.classList.remove('active');
            }
        });
        
        // Close with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && quickviewModal.classList.contains('active')) {
                quickviewModal.classList.remove('active');
            }
        });
    }
    
    // Show loading state
    const quickviewContent = document.getElementById('quickviewContent');
    quickviewContent.innerHTML = '<div style="padding: 40px; text-align: center; color: var(--text-secondary)"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 15px;"></i><p>Loading product details...</p></div>';
    quickviewModal.classList.add('active');
    
    // Fetch product data
    fetch(`ajax/quick_view.php?id=${productId}`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            console.log('Received data length:', data.length);
            
            if (!data || data.trim() === '') {
                throw new Error('Empty response from server');
            }
            
            quickviewContent.innerHTML = data;
            
            // Re-attach event listeners for buttons inside the quickview
            const addToCartBtn = quickviewContent.querySelector('.add-to-cart-btn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    const productId = this.getAttribute('onclick').match(/addToCart\((\d+),/)[1];
                    const productName = this.getAttribute('onclick').match(/addToCart\(\d+, '([^']+)'/)[1];
                    const price = this.getAttribute('onclick').match(/addToCart\(\d+, '[^']+', ([^,]+)/)[1];
                    const image = this.getAttribute('onclick').match(/addToCart\(\d+, '[^']+', [^,]+, '([^']+)'/)[1];
                    
                    addToCart(productId, productName, price, image);
                    quickviewModal.classList.remove('active');
                });
            }
        })
        .catch(error => {
            console.error('Error loading quick view:', error);
            quickviewContent.innerHTML = `
                <div style="padding: 40px; text-align: center; color: #ff4757;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <h3>Error Loading Product</h3>
                    <p>Failed to load product details. Please try again.</p>
                    <button onclick="quickView(${productId})" style="margin-top: 15px; padding: 10px 20px; background: var(--accent); color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Retry
                    </button>
                </div>
            `;
            showNotification('Error loading product details', '', 'error');
        });
}

function changeQuickViewImage(thumbnail) {
    const mainImage = thumbnail.closest('.quickview-image').querySelector('img');
    if (mainImage) {
        mainImage.src = thumbnail.src;
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }
}

// Back to Top Button
const backToTopButton = document.getElementById('backToTop');

// Show/hide button based on scroll position
window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        backToTopButton.style.display = 'flex';
    } else {
        backToTopButton.style.display = 'none';
    }
});

// Scroll to top when clicked
backToTopButton.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// App Download Modal
const appModal = document.getElementById('appModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const downloadBtn = document.getElementById('downloadBtn');

// Check if user has previously closed the modal
if (!localStorage.getItem('appModalClosed')) {
    // Show modal after a short delay
    setTimeout(() => {
        appModal.style.display = 'flex';
    }, 3000);
}

// Close modal when close button is clicked
closeModalBtn.addEventListener('click', function() {
    appModal.style.display = 'none';
    // Save preference to never show again
    localStorage.setItem('appModalClosed', 'true');
});

// Also save preference when downloading the app
downloadBtn.addEventListener('click', function() {
    localStorage.setItem('appModalClosed', 'true');
});

// Close modal if user clicks outside the content
appModal.addEventListener('click', function(e) {
    if (e.target === appModal) {
        appModal.style.display = 'none';
        localStorage.setItem('appModalClosed', 'true');
    }
});

// Event delegation for all click events
document.addEventListener('click', function(e) {
    // Quantity buttons
    if (e.target.classList.contains('quantity-increase') || e.target.closest('.quantity-increase')) {
        const btn = e.target.classList.contains('quantity-increase') ? e.target : e.target.closest('.quantity-increase');
        const item = btn.closest('.cart-item');
        if (item) {
            const productId = item.dataset.productId;
            updateQuantity(productId, 1);
        }
    }
    
    if (e.target.classList.contains('quantity-decrease') || e.target.closest('.quantity-decrease')) {
        const btn = e.target.classList.contains('quantity-decrease') ? e.target : e.target.closest('.quantity-decrease');
        const item = btn.closest('.cart-item');
        if (item) {
            const productId = item.dataset.productId;
            updateQuantity(productId, -1);
        }
    }
    
    // Remove item
    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
        const btn = e.target.classList.contains('remove-item') ? e.target : e.target.closest('.remove-item');
        const item = btn.closest('.cart-item');
        if (item) {
            const productId = item.dataset.productId;
            if (confirm('Are you sure you want to remove this item?')) {
                removeCartItem(productId);
            }
        }
    }
    
    // Quickview buttons - handle both direct clicks and child element clicks
    if (e.target.classList.contains('quickview-btn') || e.target.closest('.quickview-btn')) {
        const btn = e.target.classList.contains('quickview-btn') ? e.target : e.target.closest('.quickview-btn');
        
        // Get product ID from data attribute or onclick
        let productId = btn.dataset.productId;
        
        if (!productId && btn.onclick) {
            // Try to extract from onclick attribute
            const onclickStr = btn.getAttribute('onclick');
            const match = onclickStr?.match(/quickView\((\d+)\)/);
            if (match) {
                productId = match[1];
            }
        }
        
        if (!productId && btn.closest('.product-card')) {
            // Try to get from nearby elements
            const productCard = btn.closest('.product-card');
            const productLink = productCard.querySelector('.product-link');
            if (productLink) {
                const href = productLink.getAttribute('href');
                const match = href.match(/id=(\d+)/);
                if (match) {
                    productId = match[1];
                }
            }
        }
        
        if (productId) {
            e.stopPropagation();
            e.preventDefault();
            quickView(productId);
        } else {
            console.warn('Could not find product ID for quickview');
        }
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Make sure all quickview buttons have proper data attributes
    document.querySelectorAll('.quickview-btn').forEach(button => {
        // If button doesn't have data-product-id, try to extract it
        if (!button.dataset.productId) {
            const productCard = button.closest('.product-card');
            if (productCard) {
                const productLink = productCard.querySelector('.product-link');
                if (productLink) {
                    const href = productLink.getAttribute('href');
                    const match = href.match(/id=(\d+)/);
                    if (match) {
                        button.dataset.productId = match[1];
                    }
                }
            }
        }
    });
});

// Debug helper - add this to test
window.debugQuickview = function() {
    console.log('Testing quickview...');
    // Find first product ID on page
    const firstProductCard = document.querySelector('.product-card');
    if (firstProductCard) {
        const productLink = firstProductCard.querySelector('.product-link');
        if (productLink) {
            const href = productLink.getAttribute('href');
            const match = href.match(/id=(\d+)/);
            if (match) {
                console.log('Testing with product ID:', match[1]);
                quickView(match[1]);
            }
        }
    }
};
</script>

</body>
</html>