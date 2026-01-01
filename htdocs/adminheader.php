<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0a0a0a;
            --secondary: #1a1a1a;
            --accent: #00d2ff;
            --text: #ffffff;
            --text-secondary: #888888;
            --danger: #ff4757;
            --success: #2ed573;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --header-height: 70px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--primary);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--header-height);
            background-color: var(--secondary);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            z-index: 1000;
        }
        
        .hamburger {
            cursor: pointer;
            margin-right: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 30px;
            width: 30px;
        }
        
        .hamburger span {
            display: block;
            height: 3px;
            width: 100%;
            background-color: var(--accent);
            margin: 3px 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }
        
        .logo {
            display: flex;
            align-items: center;
            color: var(--accent);
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .logo i {
            margin-right: 0.8rem;
        }
        
        .header-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .notification-bell, .user-profile {
            position: relative;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255,255,255,0.1);
            transition: all 0.3s;
        }
        
        .notification-bell:hover, .user-profile:hover {
            background-color: var(--accent);
            color: var(--primary);
        }
        
        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            height: calc(100vh - var(--header-height));
            width: var(--sidebar-width);
            background-color: var(--secondary);
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 999;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
        }
        
        .menu-title {
            padding: 0.8rem 1.5rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .menu-title i {
            margin-right: 0.8rem;
            min-width: 20px;
        }
        
        .sidebar.collapsed .menu-title {
            justify-content: center;
            padding: 0.8rem 0;
        }
        
        .sidebar.collapsed .menu-title span {
            display: none;
        }
        
        .sidebar.collapsed .menu-title i {
            margin-right: 0;
            font-size: 1.2rem;
        }
        
        .menu-item {
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .menu-item:hover {
            background-color: rgba(0,210,255,0.1);
            color: var(--accent);
        }
        
        .menu-item.active {
            background-color: rgba(0,210,255,0.2);
            color: var(--accent);
            border-left: 3px solid var(--accent);
        }
        
        .menu-item i {
            margin-right: 0.8rem;
            min-width: 20px;
        }
        
        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 0.8rem 0;
        }
        
        .sidebar.collapsed .menu-item span {
            display: none;
        }
        
        .sidebar.collapsed .menu-item i {
            margin-right: 0;
            font-size: 1.2rem;
        }
        
        /* Main Content */
        .main-content {
            margin-top: var(--header-height);
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: all 0.3s ease;
            min-height: calc(100vh - var(--header-height));
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .dashboard-card {
            background-color: var(--secondary);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .card-header i {
            color: var(--accent);
            margin-right: 0.8rem;
            font-size: 1.2rem;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                left: -250px;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
        }
        
        @media (max-width: 576px) {
            .header {
                padding: 0 1rem;
            }
            
            .logo span {
                font-size: 1.2rem;
            }
            
            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <div class="logo">
            <i class="fas fa-cog"></i>
            <span>Admin Panel</span>
        </div>
        
        <div class="header-actions">
            <div class="notification-bell">
                <i class="fas fa-bell"></i>
                <span class="notification-count">3</span>
            </div>
            
            <div class="user-profile">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li class="menu-title">
                <i class="fas fa-tachometer-alt"></i>
                <span>Main</span>
            </li>
            <li>
                <a href="admin_dashboard.php" class="menu-item active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="menu-title">
                <i class="fas fa-shopping-bag"></i>
                <span>Products</span>
            </li>
            <li>
                <a href="admin_products.php" class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>All Products</span>
                </a>
            </li>
            <li>
                <a href="add_product.php" class="menu-item">
                    <i class="fas fa-plus"></i>
                    <span>Add New Product</span>
                </a>
            </li>
            <li>
                <a href="featured_products.php" class="menu-item">
                    <i class="fas fa-star"></i>
                    <span>Featured Products</span>
                </a>
            </li>
            
            <li class="menu-title">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </li>
            <li>
                <a href="orders.php" class="menu-item">
                    <i class="fas fa-list"></i>
                    <span>View Orders</span>
                </a>
            </li>
            <li>
                <a href="pending_orders.php" class="menu-item">
                    <i class="fas fa-clock"></i>
                    <span>Pending Orders</span>
                </a>
            </li>
            
            <li class="menu-title">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </li>
            <li>
                <a href="users.php" class="menu-item">
                    <i class="fas fa-user-friends"></i>
                    <span>Manage Users</span>
                </a>
            </li>
            <li>
                <a href="clients.php" class="menu-item">
                    <i class="fas fa-address-card"></i>
                    <span>Clients</span>
                </a>
            </li>
            
            <li class="menu-title">
                <i class="fas fa-comment"></i>
                <span>Messages</span>
            </li>
            <li>
                <a href="messages.php" class="menu-item">
                    <i class="fas fa-inbox"></i>
                    <span>Inbox</span>
                </a>
            </li>
            
            <li class="menu-title">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </li>
            <li>
                <a href="settings.php" class="menu-item">
                    <i class="fas fa-sliders-h"></i>
                    <span>Site Settings</span>
                </a>
            </li>
            <li>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Your page content will go here -->
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            // Check if we're on a large screen
            const isLargeScreen = window.innerWidth >= 993;
            
            // Set initial state based on screen size
            if (isLargeScreen) {
                // On large screens, sidebar is expanded by default
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            } else {
                // On small screens, sidebar is hidden by default
                sidebar.classList.remove('active');
            }
            
            // Toggle sidebar on hamburger click
            hamburger.addEventListener('click', function() {
                if (isLargeScreen) {
                    // On large screens, toggle between expanded and collapsed
                    this.classList.toggle('active');
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                } else {
                    // On small screens, toggle visibility
                    this.classList.toggle('active');
                    sidebar.classList.toggle('active');
                }
            });
            
            // Handle window resize
            function handleResize() {
                const nowLargeScreen = window.innerWidth >= 993;
                
                if (nowLargeScreen !== isLargeScreen) {
                    // Screen size category changed
                    window.location.reload(); // Simplest solution to reset state
                }
            }
            
            // Listen for window resize
            window.addEventListener('resize', handleResize);
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 993 && 
                    sidebar.classList.contains('active') &&
                    !sidebar.contains(event.target) &&
                    !hamburger.contains(event.target)) {
                    sidebar.classList.remove('active');
                    hamburger.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>