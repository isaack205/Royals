<?php
// Include the database connection
include('db.php');

// Start the session to use cart functionality
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']); // Check if 'user_id' session variable exists

// Include the authentication handler
include('auth.php');

// Check if the user is logged in via cookie
$user_data = checkAuth();

// Initialize cart count to 0 by default
$cartCount = 0;

// If the user is logged in, fetch the cart count from the database
if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $cartQuery = "SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = $userId";
    $cartResult = $connection->query($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > 0) {
        $row = $cartResult->fetch_assoc();
        $cartCount = $row['cart_count']; // Total quantity of items in the cart
    }
}

// Query to fetch all products from the database in random order
$sql = "SELECT * FROM products ORDER BY RAND()";
$result = $connection->query($sql);

// Fetch the latest products based on the highest id values
$newProductsSql = "SELECT * FROM products ORDER BY id DESC LIMIT 20";
$newProductsResult = $connection->query($newProductsSql);

// Fetch featured products
$featuredSql = "SELECT p.id, p.name, p.price_ksh, p.image FROM products p 
                INNER JOIN featured_products fp ON p.id = fp.product_id
                LIMIT 20";
$featuredResult = $connection->query($featuredSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="monetag" content="1e245ad9a82232c848e85dcf06fa59a7">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandX - quality sneakers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <style>
    :root {
        --primary: #0d1117;
        --primary-light: #f5f5f5;
        --secondary: #3a7bd5;
        --accent: #00d2ff;
        --text: #333333;
        --text-secondary: #666666;
        --footer-dark: #222222;
        --discount: #ff4757;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--primary);
        color: white;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        padding-top: 80px;
    }

    header {
        background-color: var(--primary);
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        text-shadow: 0 0 10px rgba(0, 210, 255, 0.7);
        animation: glow-pulse 2s infinite alternate;
    }

    @keyframes glow-pulse {
        from { text-shadow: 0 0 10px rgba(0, 210, 255, 0.7); }
        to { text-shadow: 0 0 15px rgba(0, 210, 255, 0.8); }
    }

    /* Hamburger menu styles */
    .ham-menu {
        height: 50px;
        width: 50px;
        margin-left: auto;
        position: relative;
        cursor: pointer;
        z-index: 1000;
    }

    .ham-menu span {
        height: 5px;
        width: 100%;
        background-color: var(--text);
        border-radius: 25px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .ham-menu span:nth-child(1) { top: 25%; }
    .ham-menu span:nth-child(2) { top: 50%; }
    .ham-menu span:nth-child(3) { top: 75%; }

    .ham-menu.active span:nth-child(1) {
        top: 50%;
        transform: translate(-50%, -50%) rotate(45deg);
    }

    .ham-menu.active span:nth-child(2) { opacity: 0; }
    .ham-menu.active span:nth-child(3) {
        top: 50%;
        transform: translate(-50%, 50%) rotate(-45deg);
    }

    /* Off-screen menu */
    .off-screen-menu {
        background: var(--primary);
        color: var(--text);
        height: 100vh;
        width: 250px;
        position: fixed;
        top: 0;
        right: -250px;
        z-index: 999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        font-size: 1.5rem;
        transition: right 0.3s ease;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .off-screen-menu.active { right: 0; }

    .off-screen-menu ul {
        list-style: none;
        padding: 0;
    }

    .off-screen-menu li {
        margin: 20px 0;
        display: block;
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
    }

    .off-screen-menu a {
        color: var(--text);
        text-decoration: none;
        font-size: 1.2rem;
    }

    /* Cart icon styles */
    .cart-icon {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 1000;
    }

    .cart-icon a {
        position: relative;
        display: inline-block;
    }

    .cart-icon img {
        width: 30px;
        height: 30px;
    }

    .cart-count {
        position: absolute;
        top: -6px;
        right: -20px;
        background-color: var(--discount);
        color: white;
        border-radius: 50%;
        padding: 2px 5px;
        font-weight: bold;
        font-size: 10px;
        min-width: 10px;
        min-height: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Search bar styles */
    .search-bar {
        display: flex;
        align-items: center;
        margin: 20px auto;
        width: 350px;
        height: 40px;
        padding: 0 15px;
        border-radius: 20px;
        border: 2px solid var(--accent);
        background-color: var(--primary);
        color: var(--text);
        font-size: 16px;
        outline: none;
        transition: all 0.3s ease;
        justify-content: center;
        text-align: center;
    }

    /* Section titles */
    .section-title {
        font-size: 1.5rem;
        margin: 2rem 0 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--accent);
        display: inline-block;
        color: var(--text);
    }

    /* Product containers */
    .products-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.8rem;
        margin: 0 1rem 3rem;
    }

    .product-card {
        background-color: #161b22;
        
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }

    .product-image {
        position: relative;
    }

    .product-image img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .discount-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: var(--discount);
        color: white;
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
        border-radius: 4px;
        font-weight: 600;
        z-index: 2;
    }

    .product-details {
        padding: 1rem;
        position: relative;
    }

    .product-title {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--text);
    }

    .price-container {
        margin-bottom: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .original-price {
        text-decoration: line-through;
        color: var(--text-secondary);
        font-size: 0.8rem;
    }

    .current-price {
        color: var(--accent);
        font-weight: bold;
        font-size: 1rem;
    }

    .cart-icon-form {
        position: relative;
        z-index: 2;
    }

    .cart-icon {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        background-color: rgba(0, 210, 255, 0.1);
        color: var(--accent);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid var(--accent);
    }

    .cart-icon:hover {
        background-color: var(--accent);
        color: var(--primary);
        transform: scale(1.1);
    }

    /* Top brands section */
    .top-brands {
        display: flex;
        justify-content: flex-start;
        overflow-x: auto;
        gap: 10px;
        padding: 5px 10px;
        scroll-snap-type: x mandatory;
        margin: 1rem;
    }

    .brand-container {
        flex: 0 0 auto;
        max-width: 100px;
        min-width: 100px;
        text-align: center;
        height: 100px;
    }

    .brand-container img {
        width: 100px;
        border-radius: 8px;
        transition: transform 0.3s ease;
        height: 100px;
        object-fit: contain;
        background-color: white;
        padding: 5px;
    }

    .brand-container img:hover {
        transform: translateY(-5px);
    }

    /* Footer styles */
    footer {
        background-color: var(--footer-dark);
        color: white;
        padding: 20px 0;
        text-align: center;
        margin-top: 50px;
    }

    .footer-icons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 50px;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        padding: 10px 0;
    }

    .footer-icons img {
        width: 50px;
        height: auto;
    }

    .footer p {
        margin: 0;
    }

    .footer a {
        color: var(--accent);
        text-decoration: none;
    }

    .footer-icons-text p {
        text-align: center;
        font-size: 16px;
        color: var(--accent);
        margin-bottom: 10px;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .products-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 900px) {
        .products-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 600px) {
        .products-container {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .product-image img {
            height: 150px;
        }
    }
    </style>
</head>
<body>
    <header>
        <div class="ham-menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
        
        <!-- Cart icon -->
        <div class="cart-icon">
            <a href="cart.php">
                <img src="uploads/cart.png" alt="Cart">
                <?php if (isset($cartCount) && $cartCount > 0): ?>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Logo -->
        <div class="logo">
            <a href="home.php" class="glowing-logo">
                <img src="uploads/brandxlogo.png" alt="BrandX Logo" style="height: 50px;">
            </a>
        </div>
        
        <!-- Off-screen menu -->
        <div class="off-screen-menu" id="menu">
            <div class="navlogo">
                <a href="home.php">
                    <img src="uploads/brandxlogo.png" alt="BrandX Logo" style="height: 60px;">
                </a>
            </div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="about_us.php">About us</a></li>
                <li><a href="services.php">Services</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php" style="font-weight: bold; color: var(--discount);">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" style="font-weight: bold; color: var(--accent);">Login</a></li>
                <?php endif; ?>
                <li><a href="admin_dashboard.php" style="font-weight: bold; color: var(--accent);">ADM</a></li>
            </ul>
        </div>
    </header>
    
    <!-- Search bar -->
    <a href="search.php">
        <input type="text" class="search-bar" placeholder="Search for products..." readonly>
    </a>
  
    <!-- Featured Products Section -->
    <h2 class="section-title">BEST FROM BRANDX</h2>
    <div class="products-container">
        <?php if ($featuredResult->num_rows > 0): ?>
            <?php while ($featuredProduct = $featuredResult->fetch_assoc()): ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?= $featuredProduct['id'] ?>'">
                    <div class="product-image">
                        <img src="uploads/<?= htmlspecialchars($featuredProduct['image']) ?>" alt="<?= htmlspecialchars($featuredProduct['name']) ?>">
                        <!-- Example discount badge (you would need to add discount data to your database) -->
                        <span class="discount-badge">20% OFF</span>
                    </div>
                    <div class="product-details">
                        <div class="product-title"><?= htmlspecialchars($featuredProduct['name']) ?></div>
                        <div class="price-container">
                            <span class="current-price">KSh <?= number_format($featuredProduct['price_ksh'] * 0.8, 2) ?></span>
                            <span class="original-price">KSh <?= number_format($featuredProduct['price_ksh'], 2) ?></span>
                        </div>
                        
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No featured products available at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- New Arrivals Section -->
    <h2 class="section-title">NEW ARRIVALS</h2>
    <div class="products-container">
        <?php if ($newProductsResult->num_rows > 0): ?>
            <?php while ($newProduct = $newProductsResult->fetch_assoc()): ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?= $newProduct['id'] ?>'">
                    <div class="product-image">
                        <img src="uploads/<?= htmlspecialchars($newProduct['image']) ?>" alt="<?= htmlspecialchars($newProduct['name']) ?>">
                    </div>
                    <div class="product-details">
                        <div class="product-title"><?= htmlspecialchars($newProduct['name']) ?></div>
                        <div class="price-container">
                            <span class="current-price">KSh <?= number_format($newProduct['price_ksh'], 2) ?></span>
                        </div>
                      
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No newly added products found.</p>
        <?php endif; ?>
    </div>

    <!-- Top Brands Section -->
    <h2 class="section-title">TOP BRANDS</h2>
    <div class="top-brands">
        <div class="brand-container">
            <a href="category.php?category=nike">
                <img src="uploads/nike.png" alt="nike">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=vans">
                <img src="uploads/vans.png" alt="vans">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=jordan">
                <img src="uploads/jordan.png" alt="jordan">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=newbalance">
                <img src="uploads/nb.png" alt="newbalance">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=adidas">
                <img src="uploads/adidas.png" alt="adidas">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=puma">
                <img src="uploads/puma.png" alt="puma">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=clarks">
                <img src="uploads/clarks.png" alt="clarks">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=fila">
                <img src="uploads/fila.png" alt="fila">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=converse">
                <img src="uploads/converse.png" alt="converse">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=timberland">
                <img src="uploads/timbaland.png" alt="timberland">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=gucci">
                <img src="uploads/gucci.png" alt="gucci">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=lv">
                <img src="uploads/lv.png" alt="lv">
            </a>
        </div>
    </div>

    <!-- Recommended Products Section -->
    <h2 class="section-title">RECOMMENDED FOR YOU</h2>
    <div class="products-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?= $product['id'] ?>'">
                    <div class="product-image">
                        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <!-- Random discount badge for demonstration -->
                        <?php if (rand(0, 1)): ?>
                            <span class="discount-badge"><?= rand(10, 30) ?>% OFF</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-details">
                        <div class="product-title"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="price-container">
                            <?php if (rand(0, 1)): ?>
                                <span class="current-price">KSh <?= number_format($product['price_ksh'] * 0.8, 2) ?></span>
                                <span class="original-price">KSh <?= number_format($product['price_ksh'], 2) ?></span>
                            <?php else: ?>
                                <span class="current-price">KSh <?= number_format($product['price_ksh'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                       
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-icons-text">
            <p>You may reach us on:</p>
        </div>
        <div class="footer-icons">
            <a href="tel:+254773184426" target="_blank">
                <img src="uploads/call.png" alt="Call Us">
            </a>
            <a href="https://wa.me/254773184426" target="_blank">
                <img src="uploads/whatsapp.png" alt="WhatsApp">
            </a>
            <a href="mailto:michaelngugi448@gmail.com" target="_blank">
                <img src="uploads/gmail.png" alt="Email">
            </a>
            <a href="https://www.instagram.com/top_brand_x" target="_blank">
                <img src="uploads/ig.png" alt="Instagram">
            </a>
        </div>
        <p>&copy; 2024-2025 BrandX Online Store | All Rights Reserved <br>Developed and maintained by 
            <a href="https://wa.me/254773743248" target="_blank">Simon Ngugi</a>
        </p>
    </footer>

    <script>
        // Toggle mobile menu
        function toggleMenu() {
            const menu = document.getElementById('menu');
            const hamMenu = document.querySelector('.ham-menu');
            menu.classList.toggle('active');
            hamMenu.classList.toggle('active');
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>