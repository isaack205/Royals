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
    <meta name="monetag" content="1e245ad9a82232c848e85dcf06fa59a7">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandX - Quality Sneakers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --black: #0a192f;
            --white: #FFFFFF;
            --accent: #FF8C00;
            --gray: #222222;
            --light-gray: #f5f5f5;
            --dark-bg: #161b22;
            --text-color: #c9d1d9;
            --blue-accent: #4ea8de;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--dark-bg);
            color: var(--text-color);
            padding-top: 60px;
        }
        
        header {
            background-color: var(--black);
            height: 60px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
        
        /* Logo styles */
        .logo {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .logo img {
            height: 50px;
            width: auto;
        }
        
        /* Hamburger menu styles */
        .ham-menu {
            height: 30px;
            width: 30px;
            margin-left: auto;
            position: relative;
            cursor: pointer;
            z-index: 1000;
        }
        
        .ham-menu span {
            height: 3px;
            width: 100%;
            background-color: var(--white);
            border-radius: 25px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        
        .ham-menu span:nth-child(1) {
            top: 25%;
        }
        
        .ham-menu span:nth-child(2) {
            top: 50%;
        }
        
        .ham-menu span:nth-child(3) {
            top: 75%;
        }
        
        .ham-menu.active span:nth-child(1) {
            top: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
        }
        
        .ham-menu.active span:nth-child(2) {
            opacity: 0;
        }
        
        .ham-menu.active span:nth-child(3) {
            top: 50%;
            transform: translate(-50%, 50%) rotate(-45deg);
        }
        
        /* Off-screen menu styles */
        .off-screen-menu {
            background: var(--black);
            color: var(--white);
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            right: -250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 1.2rem;
            transition: right 0.3s ease;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 999;
        }
        
        .off-screen-menu.active {
            right: 0;
        }
        
        .off-screen-menu ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        
        .off-screen-menu li {
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .off-screen-menu a {
            color: var(--white);
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        
        .off-screen-menu a:hover {
            color: var(--accent);
        }
        
        /* Cart icon styles */
        .cart-icon {
            position: absolute;
            top: 15px;
            left: 20px;
            z-index: 1000;
        }
        
        .cart-icon a {
            position: relative;
            display: inline-block;
        }
        
        .cart-icon img {
            width: 25px;
            height: 25px;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: var(--accent);
            color: var(--white);
            border-radius: 50%;
            padding: 2px 5px;
            font-size: 10px;
            min-width: 15px;
            min-height: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* Search bar styles */
        .search-bar-container {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        
        .search-bar {
            width: 80%;
            max-width: 500px;
            height: 40px;
            padding: 0 15px;
            border-radius: 20px;
            border: 2px solid var(--accent);
            background-color: var(--dark-bg);
            color: var(--white);
            font-size: 16px;
            outline: none;
        }
        
        /* Section headers */
        h2 {
            text-align: center;
            color: var(--accent);
            margin: 30px 0 20px;
            font-size: 1.8rem;
            position: relative;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent);
        }
        
        /* Product containers */
        .product-container,
        .best-from-brandx,
        .new-products,
        .top-brands {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 20px;
            scroll-snap-type: x mandatory;
            margin: 0 auto;
            max-width: 1200px;
        }
        
        /* Product cards */
        .product,
        .new-product-card {
            flex: 0 0 calc(16.6667% - 20px);
            background: black;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
            min-width: 200px;
            scroll-snap-align: start;
            position: relative;
            border-left: 4px solid var(--accent);
        }
        
        .product:hover,
        .new-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .product h3 {
            font-size: 1rem;
            color: var(--blue-accent);
            margin: 10px 0;
        }
        
        .price {
            font-size: 1rem;
            color: var(--white);
            font-weight: bold;
        }
        .products-wrapper {
                max-width: 1200px;
                    margin: 0 auto;
                        padding: 0 15px;
                        }

                        /* Limit the number of visible products on mobile */
                        @media (max-width: 767px) {
                            .product-container {
                                    max-height: calc(2 * (200px + 20px)); /* 2 rows height (item height + margin) */
                                            overflow: hidden;
                                                }
                                                    
                                                        /* Optional: Add a "View More" button */
                                                            .view-more {
                                                                    display: block;
                                                                            text-align: center;
                                                                                    margin: 20px auto;
                                                                                            padding: 10px 20px;
                                                                                                    background: var(--accent);
                                                                                                            color: white;
                                                                                                                    border: none;
                                                                                                                            border-radius: 5px;
                                                                                                                                    cursor: pointer;
                                                                                                                                        }
                                                                                                                                        }
        }
        /* Add these styles to your existing CSS */

        /* For mobile screens (up to 480px) */
        @media (max-width: 480px) {
            .product-container,
                .best-from-brandx,
                    .new-products {
                            flex-wrap: wrap; /* Allow items to wrap to next row */
                                    justify-content: center; /* Center items in the container */
                                            height: auto; /* Let the container height adjust automatically */
                                                    max-height: none; /* Remove any max-height restrictions */
                                                            overflow-y: visible; /* Allow vertical scrolling if needed */
                                                                }

                                                                    .product,
                                                                        .new-product-card {
                                                                                flex: 0 0 calc(50% - 20px); /* Two items per row with gap */
                                                                                        min-width: calc(50% - 20px); /* Ensure consistent width */
                                                                                                margin-bottom: 20px; /* Space between rows */
                                                                                                    }

                                                                                                        /* Adjust brand containers for mobile */
                                                                                                            .top-brands {
                                                                                                                    flex-wrap: wrap;
                                                                                                                            justify-content: center;
                                                                                                                                }

                                                                                                                                    .brand-container {
                                                                                                                                            flex: 0 0 calc(33.333% - 10px); /* Three brands per row on mobile */
                                                                                                                                                    min-width: calc(33.333% - 10px);
                                                                                                                                                            margin-bottom: 10px;
                                                                                                                                                                }
                                                                                                                                                                }

                                                                                                                                                                /* For slightly larger mobile screens (481px to 767px) */
                                                                                                                                                                @media (min-width: 481px) and (max-width: 767px) {
                                                                                                                                                                    .product,
                                                                                                                                                                        .new-product-card {
                                                                                                                                                                                flex: 0 0 calc(33.333% - 20px); /* Three items per row */
                                                                                                                                                                                        min-width: calc(33.333% - 20px);
                                                                                                                                                                                            }
                                                                                                                                                                                            }
        .category-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--accent);
            color: var(--white);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        /* Brand container styles */
        .brand-container {
            flex: 0 0 auto;
            min-width: 100px;
            text-align: center;
            background: none;
        }
        
        .brand-container img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: contain;
            background: var(--white);
            padding: 10px;
        }
        
        /* Slider styles */
        .slider-container {
            width: 100%;
            overflow: hidden;
            position: relative;
            height: 200px;
            margin: 20px 0;
        }
        
        .slider {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 200px;
        }
        
        .slide {
            min-width: 100%;
            transition: opacity 0.5s ease-in-out;
        }
        
        .slider img,
        .slider video {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        /* Footer styles */
        footer {
            background-color: var(--black);
            color: var(--text-color);
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
        }
        
        .footer-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
        }
        
        .footer-icons img {
            width: 40px;
            height: 40px;
            transition: transform 0.3s ease;
        }
        
        .footer-icons img:hover {
            transform: scale(1.1);
        }
        
        footer p {
            margin: 10px 0;
        }
        
        footer a {
            color: var(--blue-accent);
            text-decoration: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .product,
            .new-product-card {
                flex: 0 0 calc(33.333% - 20px);
            }
            
            .search-bar {
                width: 90%;
            }
        }
        
        @media (max-width: 480px) {
            .product,
            .new-product-card {
                flex: 0 0 calc(50% - 20px);
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="cart-icon">
            <a href="cart.php">
                <img src="uploads/cart.png" alt="Cart">
                <?php if (isset($cartCount) && $cartCount > 0): ?>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="logo">
            <a href="home.php">
                <img src="uploads/brandxlogo.png" alt="BrandX Logo">
            </a>
        </div>
        
        <div class="ham-menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
        
        <div class="off-screen-menu" id="menu">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="about_us.php">About us</a></li>
                <li><a href="services.php">Services</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php" style="color: var(--accent);">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" style="color: var(--blue-accent);">Login</a></li>
                <?php endif; ?>
                <li><a href="admin_dashboard.php" style="color: var(--blue-accent);">Admin</a></li>
            </ul>
        </div>
    </header>
    
    <div class="search-bar-container">
        <a href="search.php">
            <input type="text" class="search-bar" placeholder="Search for sneakers..." readonly>
        </a>
    </div>
    
    <div class="slider-container">
        <div class="slider">
            <?php
            $adsDirectory = 'ads/';
            $ads = scandir($adsDirectory);
            
            foreach ($ads as $ad) {
                $filePath = $adsDirectory . $ad;
                
                if ($ad !== '.' && $ad !== '..') {
                    $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    
                    if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo "<div class='slide'><img src='$filePath' alt='Ad'></div>";
                    } elseif (in_array($fileExt, ['mp4', 'webm', 'ogg', 'avi', 'mov'])) {
                        echo "<div class='slide'>
                                <video class='ad-video' autoplay muted loop>
                                    <source src='$filePath' type='video/$fileExt'>
                                </video>
                              </div>";
                    }
                }
            }
            ?>
        </div>
    </div>
    
    <h2>BEST FROM BRANDX</h2>
    <div class="best-from-brandx">
        <?php
        $featuredSql = "SELECT p.id, p.name, p.price_ksh, p.image, p.category FROM products p 
                        INNER JOIN featured_products fp ON p.id = fp.product_id
                        LIMIT 20";
        
        $featuredResult = $connection->query($featuredSql);
        
        if ($featuredResult->num_rows > 0) {
            while ($featuredProduct = $featuredResult->fetch_assoc()) {
                echo '<div class="product">';
                echo '<span class="category-tag">' . htmlspecialchars($featuredProduct['category']) . '</span>';
                echo '<a href="product.php?id=' . $featuredProduct['id'] . '">';
                echo '<img src="uploads/' . htmlspecialchars($featuredProduct['image']) . '" alt="' . htmlspecialchars($featuredProduct['name']) . '" class="product-image">';
                echo '</a>';
                echo '<h3>' . htmlspecialchars($featuredProduct['name']) . '</h3>';
                echo '<p class="price">Ksh ' . number_format($featuredProduct['price_ksh'], 2) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No featured products available at the moment.</p>';
        }
        ?>
    </div>
    
    <h2>NEW ARRIVALS</h2>
    <div class="new-products">
        <?php
        if ($newProductsResult->num_rows > 0) {
            while ($newProduct = $newProductsResult->fetch_assoc()) {
                echo '<div class="new-product-card">';
                echo '<span class="category-tag">' . htmlspecialchars($newProduct['category']) . '</span>';
                echo '<a href="product.php?id=' . $newProduct['id'] . '">';
                echo '<img src="uploads/' . htmlspecialchars($newProduct['image']) . '" alt="' . htmlspecialchars($newProduct['name']) . '" class="product-image">';
                echo '</a>';
                echo '<h3>' . htmlspecialchars($newProduct['name']) . '</h3>';
                echo '<p class="price">Ksh ' . number_format($newProduct['price_ksh'], 2) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No newly added products found.</p>';
        }
        ?>
    </div>
    
    <h2>TOP BRANDS</h2>
    <div class="top-brands">
        <div class="brand-container">
            <a href="category.php?category=nike">
                <img src="uploads/nike.png" alt="Nike">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=vans">
                <img src="uploads/vans.png" alt="Vans">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=jordan">
                <img src="uploads/jordan.png" alt="Jordan">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=newbalance">
                <img src="uploads/nb.png" alt="New Balance">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=adidas">
                <img src="uploads/adidas.png" alt="Adidas">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=puma">
                <img src="uploads/puma.png" alt="Puma">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=clarks">
                <img src="uploads/clarks.png" alt="Clarks">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=fila">
                <img src="uploads/fila.png" alt="Fila">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=converse">
                <img src="uploads/converse.png" alt="Converse">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=timberland">
                <img src="uploads/timbaland.png" alt="Timberland">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=gucci">
                <img src="uploads/gucci.png" alt="Gucci">
            </a>
        </div>
        <div class="brand-container">
            <a href="category.php?category=lv">
                <img src="uploads/lv.png" alt="Louis Vuitton">
            </a>
        </div>
    </div>
    
    <!-- Replace your product containers with this structure -->
    <div class="products-wrapper">
        <h2>RECOMMENDED FOR YOU</h2>
            <div class="product-container">
                    <?php
                            if ($result->num_rows > 0) {
                                        $count = 0;
                                                    while ($row = $result->fetch_assoc()) {
                                                                    if ($count < 8) { // Limit to 8 items (2 rows of 4 on desktop, 2 rows of 2 on mobile)
                                                                                        echo '<div class="product">';
                                                                                                            echo '<span class="category-tag">' . htmlspecialchars($row['category']) . '</span>';
                                                                                                                                echo '<a href="product.php?id=' . $row['id'] . '"><img src="uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" class="product-image"></a>';
                                                                                                                                                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                                                                                                                                                                        echo '<p class="price">Ksh ' . number_format($row['price_ksh'], 2) . '</p>';
                                                                                                                                                                                            echo '</div>';
                                                                                                                                                                                                                $count++;
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                echo '<p>No products available.</p>';
                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                ?>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                    </div>
    
    <!-- Chat icon -->
    <div class="chat-icon-container">
        <div class="chat-icon">
            <img src="uploads/chat-icon.png" alt="Chat with us">
        </div>
    </div>
    
    <footer>
        <div class="footer-icons-text">
            <p>You may reach us on:</p>
        </div>
        
        <div class="footer-icons">
            <a href="tel:+254773184426" target="_blank">
                <img src="uploads/call.png" alt="Call Us">
            </a>
            <a href="https://wa.me/254773184426?text=Hello%20there,%20I%20would%20like%20to%20learn%20more%20about%20the%20sneakers%20from%20BrandX." target="_blank">
                <img src="uploads/whatsapp.png" alt="WhatsApp">
            </a>
            <a href="mailto:michaelngugi448@gmail.com?subject=Inquiry%20about%20BrandX%20Sneakers&body=Hello%20there,%20I%20would%20like%20to%20inquire%20about%20the%20sneakers%20available%20on%20BrandX%20Online%20Store." target="_blank">
                <img src="uploads/gmail.png" alt="Email">
            </a>
            <a href="https://www.instagram.com/top_brand_x?igsh=MWFsajhibHVrOXFtcg==" target="_blank">
                <img src="uploads/ig.png" alt="Instagram">
            </a>
        </div>
        
        <p>&copy; 2024-2025 BrandX Online Store | All Rights Reserved <br>Developed and maintained by 
            <a href="https://wa.me/254773743248" target="_blank">Simon Ngugi</a>.
        </p>
    </footer>
    
    <script>
        // Variables for slider
        document.addEventListener("DOMContentLoaded", function() {
            const slides = document.querySelectorAll('.slide');
            const slider = document.querySelector('.slider');
            const totalSlides = slides.length;
            let currentIndex = 0;
            let slideInterval;
            let isAdPlaying = false;
            
            function goToSlide(index) {
                if (index >= totalSlides) {
                    currentIndex = 0;
                } else if (index < 0) {
                    currentIndex = totalSlides - 1;
                } else {
                    currentIndex = index;
                }
                
                slider.style.transform = `translateX(-${currentIndex * 100}%)`;
                
                const currentSlide = slides[currentIndex];
                const video = currentSlide.querySelector('video');
                if (video) {
                    isAdPlaying = true;
                    video.play();
                    video.onended = function() {
                        isAdPlaying = false;
                        autoSlide();
                    };
                } else {
                    isAdPlaying = false;
                }
            }
            
            function autoSlide() {
                if (!isAdPlaying) {
                    goToSlide(currentIndex + 1);
                }
            }
            
            slideInterval = setInterval(autoSlide, 6000);
            goToSlide(currentIndex);
        });
        
        // Menu toggle function
        function toggleMenu() {
            const menu = document.getElementById('menu');
            const hamMenu = document.querySelector('.ham-menu');
            menu.classList.toggle('active');
            hamMenu.classList.toggle('active');
        }
    </script>
    
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/6797e4d93a84273260757d95/1iiklbtg5';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>