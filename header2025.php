<?php
// Start the session to use cart functionality
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculate cart count from session
$cartCount = count($_SESSION['cart']);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="system">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'BrandX - Quality Sneakers'; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
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
        font-family: 'Poppins', sans-serif;
        background-color: var(--background);
        color: var(--text);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        padding-top: 80px; /* To account for fixed header */
        transition: background-color 0.3s, color 0.3s;
    }

    header {
        background-color: #080f1a;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    .logo {
        font-size: 1.8rem;
        font-weight: bold;
        color: var(--accent);
        text-decoration: none;
        position: relative;
    }

    .glowing-logo {
        text-shadow: 0 0 10px rgba(0, 210, 255, 0.7), 
                     0 0 20px rgba(0, 210, 255, 0.5),
                     0 0 30px rgba(0, 210, 255, 0.3);
        animation: glow-pulse 2s infinite alternate;
    }

    @keyframes glow-pulse {
        from {
            text-shadow: 0 0 10px rgba(0, 210, 255, 0.7), 
                         0 0 20px rgba(0, 210, 255, 0.5),
                         0 0 30px rgba(0, 210, 255, 0.3);
        }
        to {
            text-shadow: 0 0 15px rgba(0, 210, 255, 0.8), 
                         0 0 25px rgba(0, 210, 255, 0.6),
                         0 0 35px rgba(0, 210, 255, 0.4);
        }
    }

    .hamburger {
        display: none;
        background: none;
        border: none;
        color: var(--accent);
        font-size: 1.5rem;
        cursor: pointer;
        z-index: 1001;
    }

    nav {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    nav a {
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    nav a:hover {
        color: var(--accent);
    }

    .mobile-nav {
        display: none;
        flex-direction: column;
        background-color: var(--primary-light);
        position: fixed;
        top: 80px;
        left: 0;
        right: 0;
        z-index: 999;
        box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .mobile-nav.active {
        display: flex;
        max-height: 500px;
        padding: 1rem 0;
    }

    .mobile-nav a {
        padding: 0.8rem 2rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        color: var(--text-secondary);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .mobile-nav a:hover {
        color: var(--accent);
        background-color: rgba(0,0,0,0.1);
    }

    /* Theme toggle */
    .theme-toggle {
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 1.2rem;
        transition: color 0.3s;
        padding: 0.5rem;
        border-radius: 50%;
    }

    .theme-toggle:hover {
        color: var(--accent);
        background-color: rgba(0,0,0,0.1);
    }

    /* Cart icon */
    .cart-icon {
        position: relative;
        cursor: pointer;
    }

    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: var(--accent);
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

    /* Mobile menu toggle */
    .ham-menu {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 24px;
        height: 18px;
        cursor: pointer;
    }

    .ham-menu span {
        display: block;
        width: 100%;
        height: 2px;
        background-color: var(--accent);
        transition: all 0.3s;
    }

    .ham-menu.active span:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }

    .ham-menu.active span:nth-child(2) {
        opacity: 0;
    }

    .ham-menu.active span:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }

    /* Off-screen menu */
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
        height: 30px;
    }

    /* Search bar */
    .search-container {
        position: relative;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }

    .search-bar {
        width: 100%;
        padding: 12px 20px;
        padding-right: 50px;
        border: 2px solid var(--border-color);
        border-radius: 30px;
        font-size: 16px;
        outline: none;
        transition: all 0.3s;
        background-color: var(--card-bg);
        color: var(--text);
    }

    .search-bar:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.2);
    }

    .search-button {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--accent);
        font-size: 18px;
        cursor: pointer;
    }

    /* Cart modal */
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

    /* Quick view modal */
    .quickview-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.8);
        z-index: 1002;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
    }

    .quickview-modal.active {
        opacity: 1;
        visibility: visible;
    }

    .quickview-content {
        background-color: var(--card-bg);
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .quickview-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(0,0,0,0.5);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1;
    }

    .quickview-body {
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    @media (min-width: 768px) {
        .quickview-body {
            flex-direction: row;
        }
    }

    .quickview-image {
        flex: 1;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--background);
    }

    .quickview-image img {
        max-width: 100%;
        max-height: 400px;
        object-fit: contain;
    }

    .quickview-details {
        flex: 1;
        padding: 20px;
    }

    .quickview-title {
        font-size: 1.8rem;
        margin-bottom: 10px;
        color: var(--text);
    }

    .quickview-price {
        font-size: 1.5rem;
        color: var(--accent);
        margin-bottom: 15px;
        font-weight: bold;
    }

    .quickview-description {
        margin-bottom: 20px;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .quickview-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .quickview-btn {
        padding: 12px 20px;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        flex: 1;
    }

    .add-to-cart-btn {
        background-color: var(--accent);
        color: white;
        border: none;
    }

    .wishlist-btn {
        background-color: var(--border-color);
        color: var(--text);
        border: none;
    }

    /* Notification */
    .notification {
        position: fixed;
        bottom: 20px;
        left: 20px;
        background-color: var(--card-bg);
        color: var(--text);
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 1000;
        transform: translateY(100px);
        opacity: 0;
        animation: slideIn 0.5s forwards, fadeOut 0.5s forwards 3s;
        max-width: 350px;
        border-left: 4px solid var(--accent);
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

    @media (max-width: 768px) {
        .hamburger {
            display: block;
        }

        nav {
            display: none;
        }

        body {
            padding-top: 70px;
        }

        header {
            padding: 1rem;
        }
    }
    </style>
</head>
<body>
    <header>
        <div class="ham-menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
        
        <div class="logo">
            <a href="home.php" class="glowing-logo">
                <img src="uploads/brandxlogo.png" alt="BrandX Logo" style="height: 40px;">
            </a>
        </div>
        
        <div class="nav-container">
            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
            
            <div class="cart-icon" id="cartIcon">
                <i class="fas fa-shopping-cart"></i>
                <?php if ($cartCount > 0): ?>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <div class="search-container" style="padding: 10px 20px; background-color: var(--primary-light);">
        <form action="home.php" method="get">
            <input type="text" name="search" class="search-bar" placeholder="Search for products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>
    </div>
    
    <div class="off-screen-menu" id="menu">
        <div class="menu-close" onclick="toggleMenu()">
            <i class="fas fa-times"></i>
        </div>
        <div class="navlogo">
            <a href="home.php">
                <img src="uploads/brandxlogo.png" alt="BrandX Logo">
            </a>
        </div>
        
        <ul>
            <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#" id="mobileCartBtn"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <li><a href="orders.php"><i class="fas fa-box"></i> My Orders</a></li>
            <li><a href="about_us.php"><i class="fas fa-info-circle"></i> About us</a></li>
            <li><a href="services.php"><i class="fas fa-concierge-bell"></i> Services</a></li>
            
            <?php if ($isLoggedIn): ?>
                <li><a href="logout.php" style="color: var(--accent);"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" style="color: #4ea8de;"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <?php endif; ?>
            
            <li><a href="admin_dashboard.php" style="color: skyblue;"><i class="fas fa-user-shield"></i> Admin</a></li>
        </ul>
    </div>
    
    <!-- Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-modal-header">
            <h3 class="cart-modal-title">Your Cart</h3>
            <button class="cart-close" id="cartClose">&times;</button>
        </div>
        
        <div class="cart-items" id="cartItems">
            <!-- Cart items will be loaded here via AJAX -->
        </div>
        
        <div class="cart-total" id="cartTotal">
            Total: Ksh 0.00
        </div>
        
        <div class="cart-actions">
            <a href="cart.php" class="cart-btn view-cart-btn">View Cart</a>
            <a href="checkout.php" class="cart-btn checkout-btn">Checkout</a>
        </div>
    </div>
    
    <!-- Quick View Modal -->
    <div class="quickview-modal" id="quickviewModal">
        <button class="quickview-close" id="quickviewClose">&times;</button>
        <div class="quickview-content">
            <div class="quickview-body" id="quickviewContent">
                <!-- Quick view content will be loaded here via AJAX -->
            </div>
        </div>
    </div>
    
    <!-- Notification -->
    <div class="notification" id="notification">
        <div class="notification-content">
            <img id="notificationImage" src="" alt="Product">
            <span id="notificationMessage"></span>
        </div>
        <button class="close-notification" id="closeNotification">&times;</button>
    </div>
    
    <script>
    // Theme toggle functionality
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    
    // Check for saved theme preference or use system preference
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
    
    // Cart modal functionality
    const cartIcon = document.getElementById('cartIcon');
    const cartModal = document.getElementById('cartModal');
    const cartClose = document.getElementById('cartClose');
    const mobileCartBtn = document.getElementById('mobileCartBtn');
    
    function toggleCartModal() {
        cartModal.classList.toggle('active');
        loadCartItems();
    }
    
    cartIcon.addEventListener('click', toggleCartModal);
    cartClose.addEventListener('click', toggleCartModal);
    mobileCartBtn.addEventListener('click', toggleCartModal);
    
    // Quick view modal functionality
    const quickviewModal = document.getElementById('quickviewModal');
    const quickviewClose = document.getElementById('quickviewClose');
    
    quickviewClose.addEventListener('click', () => {
        quickviewModal.classList.remove('active');
    });
    
    // Load cart items via AJAX
    function loadCartItems() {
        fetch('ajax/get_cart.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('cartItems').innerHTML = data;
                updateCartTotal();
            });
    }
    
    // Update cart total
    function updateCartTotal() {
        const items = document.querySelectorAll('.cart-item');
        let total = 0;
        
        items.forEach(item => {
            const price = parseFloat(item.querySelector('.cart-item-price').textContent.replace('Ksh ', ''));
            const quantity = parseInt(item.querySelector('.quantity-value').textContent);
            total += price * quantity;
        });
        
        document.getElementById('cartTotal').textContent = `Total: Ksh ${total.toFixed(2)}`;
    }
    
    // Show notification
    function showNotification(message, imageUrl) {
        const notification = document.getElementById('notification');
        document.getElementById('notificationMessage').textContent = message;
        document.getElementById('notificationImage').src = imageUrl;
        
        notification.style.display = 'flex';
        notification.style.animation = 'none';
        notification.offsetHeight; // Trigger reflow
        notification.style.animation = 'slideIn 0.5s forwards, fadeOut 0.5s forwards 3s';
    }
    
    // Close notification
    document.getElementById('closeNotification').addEventListener('click', () => {
        document.getElementById('notification').style.display = 'none';
    });
    
    // Toggle mobile menu
    function toggleMenu() {
        const menu = document.getElementById('menu');
        const hamMenu = document.querySelector('.ham-menu');
        menu.classList.toggle('active');
        hamMenu.classList.toggle('active');
        
        // Toggle body scroll when menu is open
        if (menu.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
    
    // Add to cart via AJAX
    function addToCart(productId, productName, price, image) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('product_name', productName);
        formData.append('price', price);
        formData.append('image', image);
        
        fetch('ajax/add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                document.querySelector('.cart-count').textContent = data.cartCount;
                if (data.cartCount > 0) {
                    document.querySelector('.cart-count').style.display = 'flex';
                } else {
                    document.querySelector('.cart-count').style.display = 'none';
                }
                
                // Show notification
                showNotification(`${productName} has been added to your cart!`, `uploads/${image}`);
                
                // Update cart modal if open
                if (cartModal.classList.contains('active')) {
                    loadCartItems();
                }
            }
        });
    }
    
    // Quick view product
    function quickView(productId) {
        fetch(`ajax/quick_view.php?id=${productId}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('quickviewContent').innerHTML = data;
                quickviewModal.classList.add('active');
            });
    }
    
    // Remove item from cart
    function removeCartItem(productId, size = '', color = '') {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('size', size);
        formData.append('color', color);
        
        fetch('ajax/remove_from_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCartItems();
                // Update cart count
                document.querySelector('.cart-count').textContent = data.cartCount;
                if (data.cartCount > 0) {
                    document.querySelector('.cart-count').style.display = 'flex';
                } else {
                    document.querySelector('.cart-count').style.display = 'none';
                }
            }
        });
    }
    
    // Update cart quantity
    function updateQuantity(productId, change, size = '', color = '') {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('change', change);
        formData.append('size', size);
        formData.append('color', color);
        
        fetch('ajax/update_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCartItems();
            }
        });
    }
    </script>