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

    // Query to count the total quantity of products in the user's cart
    $cartQuery = "SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = $userId";
    $cartResult = $connection->query($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > 0) {
        $row = $cartResult->fetch_assoc();
        $cartCount = $row['cart_count']; // Total quantity of items in the cart
    }
}

// Query to fetch all products from the database in random order
$sql = "SELECT * FROM products ORDER BY RAND()";  // Added ORDER BY RAND() to fetch products randomly
$result = $connection->query($sql);  // Execute the query

// Fetch the latest products based on the highest id values
$newProductsSql = "SELECT * FROM products ORDER BY id DESC LIMIT 20";
$newProductsResult = $connection->query($newProductsSql);

// Function to get random ad banners
function getRandomAd() {
    // Directory containing ads
    $adDirectory = 'assets/ads/';
    $adFiles = glob($adDirectory . '*.{jpg,jpeg,png,gif,webp,mp4,webm}', GLOB_BRACE);

    // Check if there are any ad files
    if (empty($adFiles)) {
        return ''; // No ads to display
    }

    // Select a random ad
    $randomAd = $adFiles[array_rand($adFiles)];
    $fileExtension = strtolower(pathinfo($randomAd, PATHINFO_EXTENSION));

    // Display ad based on type
    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        // Display image ad
        return '<div class="ad-banner">
                    <img src="' . $randomAd . '" alt="Ad Banner" style="width:100%; max-height:300px; object-fit:cover;">
                </div>';
    } elseif (in_array($fileExtension, ['mp4', 'webm'])) {
        // Display video ad
        return '<div class="ad-banner">
                    <video autoplay muted loop style="width:100%; max-height:300px; object-fit:cover;">
                        <source src="' . $randomAd . '" type="video/' . $fileExtension . '">
                        Your browser does not support the video tag.
                    </video>
                </div>';
    }

    return '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandX - Premium Sneakers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== Global Styles ===== */
        :root {
            --primary: #1a73e8; /* Vibrant blue */
            --primary-dark: #0d5bbc;
            --accent: #34a853; /* Google green */
            --dark: #121212;
            --darker: #0a0a0a;
            --light: #ffffff;
            --gray: #b3b3b3;
            --light-gray: #2a2a2a;
            --card-bg: #1e1e1e;
            --success: #00c853;
            --header-height: 70px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        
        body {
            background-color: var(--dark);
            color: var(--light);
            line-height: 1.6;
            padding-top: var(--header-height);
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }
        
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* ===== Header Styles ===== */
        header {
            background-color: rgba(10, 10, 10, 0.98);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            height: var(--header-height);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            padding: 0 20px;
        }
        
        .logo img {
            height: 60px;
            transition: transform 0.3s;
        }
        
        .logo:hover img {
            transform: scale(1.05);
        }
        
        /* Header Icons */
        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .header-icon {
            color: var(--light);
            font-size: 1.2rem;
            position: relative;
            transition: color 0.3s;
        }
        
        .header-icon:hover {
            color: var(--primary);
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        /* Mobile Menu Button */
        .menu-toggle {
            background: none;
            border: none;
            color: var(--light);
            font-size: 1.4rem;
            cursor: pointer;
            padding: 5px;
            margin-left: 15px;
        }
        
        /* Mobile Menu */
        .mobile-menu {
            position: fixed;
            top: var(--header-height);
            right: -100%;
            width: 280px;
            height: calc(100vh - var(--header-height));
            background: var(--darker);
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.2);
            transition: right 0.3s ease-out;
            z-index: 999;
            overflow-y: auto;
        }
        
        .mobile-menu.active {
            right: 0;
        }
        
        .mobile-menu ul {
            list-style: none;
            padding: 20px 0;
        }
        
        .mobile-menu li {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .mobile-menu a {
            display: block;
            padding: 15px 25px;
            color: var(--gray);
            transition: all 0.3s;
        }
        
        .mobile-menu a:hover {
            color: var(--light);
            background: rgba(255, 255, 255, 0.03);
            padding-left: 30px;
        }
        
        .mobile-menu a.active {
            color: var(--primary);
        }
        
        /* ===== Hero Banner ===== */
        .hero-banner {
            position: relative;
            width: 100%;
            height: 60vh;
            min-height: 400px;
            max-height: 700px;
            overflow: hidden;
            margin-bottom: 40px;
        }
        
        .hero-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .hero-slide.active {
            opacity: 1;
        }
        
        .hero-slide img, 
        .hero-slide video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        
        .hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 40px;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
        }
        
        .slider-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 3;
        }
        
        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .slider-dot.active {
            background: var(--primary);
            transform: scale(1.2);
        }
        
        /* ===== Product Sections ===== */
        .section {
            margin-bottom: 60px;
        }
        
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: var(--light);
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--primary);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .product-card {
            background: var(--card-bg);
            border-radius: 5px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .product-link {
            display: block;
            height: 100%;
        }
        
        .product-image-container {
            height: 250px;
            overflow: hidden;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-name {
            font-size: 1rem;
            margin-bottom: 8px;
            color: var(--light);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .product-price {
            color: var(--primary);
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        /* ===== Brands Section ===== */
        .brands-container {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 20px 0;
            scroll-snap-type: x mandatory;
        }
        
        .brand-card {
            flex: 0 0 auto;
            scroll-snap-align: start;
            width: 120px;
            height: 80px;
            background: var(--card-bg);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            transition: all 0.3s;
        }
        
        .brand-card:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.05);
        }
        
        .brand-card img {
            max-width: 100%;
            max-height: 100%;
            filter: grayscale(100%) brightness(0.8);
            transition: filter 0.3s;
        }
        
        .brand-card:hover img {
            filter: grayscale(0%) brightness(1);
        }
        
        /* ===== Footer ===== */
        footer {
            background: var(--darker);
            padding: 50px 0 20px;
            margin-top: 50px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .footer-column h3 {
            color: var(--light);
            margin-bottom: 20px;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--primary);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: var(--gray);
            transition: color 0.3s;
            font-size: 0.9rem;
        }
        
        .footer-links a:hover {
            color: var(--primary);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light-gray);
            color: var(--light);
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .footer-bottom a {
            color: var(--primary);
        }
        
        /* ===== Responsive Design ===== */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .hero-banner {
                height: 50vh;
                min-height: 350px;
            }
            
            .hero-content {
                padding: 25px;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-banner {
                height: 45vh;
                min-height: 300px;
            }
            
            .hero-title {
                font-size: 1.8rem;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }
            
            .product-image-container {
                height: 180px;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <!-- Logo on top left -->
            <div class="logo">
                <a href="home.php">
                    <img src="uploads/brandxlogo.png" alt="BrandX Logo">
                </a>
            </div>
            
            <!-- Header Icons on top right -->
            <div class="header-icons">
                <a href="login.php" class="header-icon">
                    <i class="fas fa-user"></i>
                </a>
                
                <a href="cart.php" class="header-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if (isset($cartCount) && $cartCount > 0): ?>
                        <span class="cart-count"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
                
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <ul>
                <li><a href="home.php" class="active">Home</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="new-arrivals.php">New Arrivals</a></li>
                <li><a href="featured.php">Featured</a></li>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php" style="color: #ff6b6b;">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <?php
        $adsDirectory = 'ads/';
        $ads = scandir($adsDirectory);
        $active = 'active';
        
        foreach ($ads as $ad) {
            if ($ad !== '.' && $ad !== '..') {
                $filePath = $adsDirectory . $ad;
                $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                
                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo "<div class='hero-slide $active'>
                            <img src='$filePath' alt='BrandX Sneakers'>
                            <div class='hero-content'>
                                <h2 class='hero-title'>Premium Sneakers</h2>
                                <p class='hero-subtitle'>Discover our latest collection of exclusive footwear</p>
                            </div>
                          </div>";
                } elseif (in_array($fileExt, ['mp4', 'webm', 'ogg', 'avi', 'mov'])) {
                    echo "<div class='hero-slide $active'>
                            <video autoplay muted loop>
                                <source src='$filePath' type='video/$fileExt'>
                            </video>
                            <div class='hero-content'>
                                <h2 class='hero-title'>New Arrivals</h2>
                                <p class='hero-subtitle'>Limited edition sneakers now available</p>
                            </div>
                          </div>";
                }
                $active = '';
            }
        }
        ?>
        
        <div class="slider-dots">
            <?php
            $slideCount = count($ads) - 2; // Subtract 2 for . and ..
            for ($i = 0; $i < $slideCount; $i++) {
                echo '<div class="slider-dot' . ($i === 0 ? ' active' : '') . '"></div>';
            }
            ?>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="container section">
        <h2 class="section-title">BEST FROM BRANDX</h2>
        <div class="products-grid">
            <?php
            if ($featuredResult->num_rows > 0) {
                while ($featuredProduct = $featuredResult->fetch_assoc()) {
                    echo '<div class="product-card">
                            <a href="product.php?id=' . $featuredProduct['id'] . '" class="product-link">
                                <div class="product-image-container">
                                    <img src="uploads/' . htmlspecialchars($featuredProduct['image']) . '" alt="' . htmlspecialchars($featuredProduct['name']) . '" class="product-image">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">' . htmlspecialchars($featuredProduct['name']) . '</h3>
                                    <p class="product-price">Ksh ' . number_format($featuredProduct['price_ksh'], 2) . '</p>
                                </div>
                            </a>
                          </div>';
                }
            } else {
                echo '<p>No featured products available at the moment.</p>';
            }
            ?>
        </div>
    </div>

    <!-- New Arrivals -->
    <div class="container section">
        <h2 class="section-title">NEW ARRIVALS</h2>
        <div class="products-grid">
            <?php
            if ($newProductsResult->num_rows > 0) {
                while ($newProduct = $newProductsResult->fetch_assoc()) {
                    echo '<div class="product-card">
                            <a href="product.php?id=' . $newProduct['id'] . '" class="product-link">
                                <div class="product-image-container">
                                    <img src="uploads/' . htmlspecialchars($newProduct['image']) . '" alt="' . htmlspecialchars($newProduct['name']) . '" class="product-image">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">' . htmlspecialchars($newProduct['name']) . '</h3>
                                    <p class="product-price">Ksh ' . number_format($newProduct['price_ksh'], 2) . '</p>
                                </div>
                            </a>
                          </div>';
                }
            } else {
                echo '<p>No newly added products found.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Top Brands -->
    <div class="container section">
        <h2 class="section-title">TOP BRANDS</h2>
        <div class="brands-container">
            <?php
            $brands = [
                'nike' => 'nike.png',
                'vans' => 'vans.png',
                'jordan' => 'jordan.png',
                'newbalance' => 'nb.png',
                'adidas' => 'adidas.png',
                'puma' => 'puma.png',
                'clarks' => 'clarks.png',
                'fila' => 'fila.png',
                'converse' => 'converse.png',
                'timberland' => 'timbaland.png',
                'gucci' => 'gucci.png',
                'lv' => 'lv.png'
            ];
            
            foreach ($brands as $category => $image) {
                echo '<a href="category.php?category=' . $category . '" class="brand-card">
                        <img src="uploads/' . $image . '" alt="' . $category . '">
                      </a>';
            }
            ?>
        </div>
    </div>

    <!-- Recommended Products -->
    <div class="container section">
        <h2 class="section-title">RECOMMENDED FOR YOU</h2>
        <div class="products-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">
                            <a href="product.php?id=' . $row['id'] . '" class="product-link">
                                <div class="product-image-container">
                                    <img src="uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" class="product-image">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">' . htmlspecialchars($row['name']) . '</h3>
                                    <p class="product-price">Ksh ' . number_format($row['price_ksh'], 2) . '</p>
                                </div>
                            </a>
                          </div>';
                }
            } else {
                echo '<p>No products available.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>BrandX</h3>
                    <p>Premium sneakers and streetwear for the modern lifestyle. Quality products with worldwide shipping.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Shop</h3>
                    <ul class="footer-links">
                        <li><a href="products.php">All Products</a></li>
                        <li><a href="categories.php">Categories</a></li>
                        <li><a href="new-arrivals.php">New Arrivals</a></li>
                        <li><a href="featured.php">Featured</a></li>
                        <li><a href="sale.php">Sale</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Help</h3>
                    <ul class="footer-links">
                        <li><a href="faq.php">FAQs</a></li>
                        <li><a href="shipping.php">Shipping & Returns</a></li>
                        <li><a href="size-guide.php">Size Guide</a></li>
                        <li><a href="track-order.php">Track Order</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone"></i> +254 777 992 666</li>
                        <li><i class="fas fa-envelope"></i> info@brandx.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> Nairobi, Kenya</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 BrandX. All Rights Reserved. | Designed by <a href="#">Simon Ngugi</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });
        
        // Hero Slider Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.slider-dot');
            let currentSlide = 0;
            
            function showSlide(n) {
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));
                
                currentSlide = (n + slides.length) % slides.length;
                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');
            }
            
            function nextSlide() {
                showSlide(currentSlide + 1);
            }
            
            // Auto slide every 5 seconds
            setInterval(nextSlide, 5000);
            
            // Dot navigation
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                });
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                    mobileMenu.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>