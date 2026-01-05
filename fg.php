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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --gradient: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --glow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* Header Styles */
        header {
            background-color: var(--white);
            box-shadow: var(--shadow);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 1rem 2rem;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .header-icons {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .header-icons i {
            font-size: 1.25rem;
            cursor: pointer;
            color: var(--text-dark);
            transition: color 0.3s;
        }

        .header-icons i:hover {
            color: var(--primary-color);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .cart-icon-container {
            position: relative;
        }

        .hamburger {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Search Modal */
        .search-modal {
            position: fixed;
            top: -100%;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: var(--white);
            z-index: 2000;
            transition: top 0.5s ease;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .search-modal.active {
            top: 0;
        }

        .search-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .search-header h2 {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .close-search {
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
        }

        .search-input-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: var(--glow);
        }

        .search-results {
            margin-top: 2rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .search-item {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-item:hover {
            background-color: #f3f4f6;
        }

        .search-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .search-item-info h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .search-item-info p {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Login Modal */
        .login-modal {
            position: fixed;
            bottom: -100%;
            left: 0;
            width: 100%;
            background-color: var(--white);
            z-index: 2000;
            transition: bottom 0.5s ease;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-height: 90vh;
            overflow-y: auto;
        }

        .login-modal.active {
            bottom: 0;
        }

        .login-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .login-header h2 {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .close-login {
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
        }

        .login-tabs {
            display: flex;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-light);
            position: relative;
        }

        .tab-btn.active {
            color: var(--primary-color);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            box-shadow: var(--glow);
            outline: none;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-me input {
            width: auto;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
        }

        .login-btn {
            width: 100%;
            padding: 0.75rem;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .social-login {
            margin-top: 1.5rem;
            text-align: center;
        }

        .social-login p {
            margin-bottom: 1rem;
            color: var(--text-light);
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background-color: #e5e7eb;
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            color: var(--text-dark);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .social-icon:hover {
            background-color: #e5e7eb;
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            height: 500px;
            position: relative;
            overflow: hidden;
        }

        .hero-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1s ease;
        }

        .hero-slide.active {
            opacity: 1;
        }

        .hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 2rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
        }

        .hero-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-content p {
            font-size: 1.25rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: var(--gradient);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .hero-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .hero-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .hero-dot.active {
            background-color: white;
        }

        /* Section Styles */
        .section {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 700;
            position: relative;
            padding-left: 1rem;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 80%;
            background: var(--gradient);
            border-radius: 4px;
        }

        .section-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-link i {
            transition: transform 0.3s;
        }

        .section-link:hover i {
            transform: translateX(5px);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background-color: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-image-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .product-wishlist {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--white);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1;
            transition: background-color 0.3s, color 0.3s;
        }

        .product-wishlist:hover {
            color: red;
        }

        .product-wishlist.active {
            color: red;
        }

        .product-info {
            padding: 1rem;
        }

        .product-category {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 0.25rem;
        }

        .product-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-rating {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .stars {
            color: #fbbf24;
            margin-right: 0.5rem;
        }

        .rating-count {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .product-sold {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .product-prices {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-price {
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-original-price {
            font-size: 0.875rem;
            color: var(--text-light);
            text-decoration: line-through;
        }

        .product-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .add-to-cart {
            background-color: var(--primary-color);
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .add-to-cart:hover {
            background-color: var(--primary-dark);
            transform: scale(1.1);
        }

        /* Categories Section */
        .categories {
            background: var(--gradient);
            padding: 3rem 2rem;
            color: white;
        }

        .categories .section-title {
            color: white;
        }

        .categories .section-title::before {
            background: white;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .category-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1.5rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .category-card:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }

        .category-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .category-name {
            font-weight: 600;
        }

        /* Brands Section */
        .brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1.5rem;
        }

        .brand-card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .brand-card:hover {
            transform: scale(1.05);
        }

        .brand-logo {
            max-width: 100%;
            max-height: 50px;
            object-fit: contain;
        }

        /* Footer */
        footer {
            background-color: var(--text-dark);
            color: white;
            padding: 3rem 2rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .footer-about {
            margin-bottom: 1rem;
            color: #9ca3af;
        }

        .footer-social {
            display: flex;
            gap: 1rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .social-link:hover {
            background-color: var(--primary-color);
        }

        .footer-heading {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary-color);
        }

        .footer-links {
            list-style: none;
        }

        .footer-link {
            margin-bottom: 0.75rem;
        }

        .footer-link a {
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-link a:hover {
            color: var(--primary-color);
        }

        .footer-contact p {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            color: #9ca3af;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 2rem auto 0;
            padding-top: 2rem;
            border-top: 1px solid #374151;
            text-align: center;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .hero {
                height: 400px;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-content p {
                font-size: 1rem;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }

            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }

            .brands-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .slide-up {
            animation: slideUp 0.5s ease forwards;
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <a href="index.php" class="logo">
                <img src="logo.png" alt="BrandX Logo">
                BrandX
            </a>
            
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="shop.php">Shop</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
            </div>
            
            <div class="header-icons">
                <i class="fas fa-search" id="search-btn"></i>
                <div class="cart-icon-container">
                    <i class="fas fa-shopping-cart" id="cart-btn"></i>
                    <span class="cart-count"><?= $cartCount ?></span>
                </div>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <i class="fas fa-user" id="login-btn"></i>
                <?php endif; ?>
                <div class="hamburger">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Search Modal -->
    <div class="search-modal" id="search-modal">
        <div class="search-header">
            <h2>Search Products</h2>
            <button class="close-search" id="close-search">&times;</button>
        </div>
        <div class="search-input-container">
            <input type="text" class="search-input" placeholder="Search for sneakers, brands, etc...">
        </div>
        <div class="search-results" id="search-results">
            <!-- Search results will be populated here -->
        </div>
    </div>

    <!-- Login Modal -->
    <div class="login-modal" id="login-modal">
        <div class="login-header">
            <h2>Welcome Back</h2>
            <button class="close-login" id="close-login">&times;</button>
        </div>
        
        <div class="login-tabs">
            <button class="tab-btn active" data-tab="login">Login</button>
            <button class="tab-btn" data-tab="register">Register</button>
        </div>
        
        <div class="tab-content active" id="login">
            <form id="login-form">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" required>
                </div>
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember-me">
                        <label for="remember-me">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            
            <div class="social-login">
                <p>Or login with</p>
                <div class="social-icons">
                    <div class="social-icon">
                        <i class="fab fa-google"></i>
                    </div>
                    <div class="social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div class="social-icon">
                        <i class="fab fa-apple"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tab-content" id="register">
            <form id="register-form">
                <div class="form-group">
                    <label for="register-name">Full Name</label>
                    <input type="text" id="register-name" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" required>
                </div>
                <div class="form-group">
                    <label for="register-confirm">Confirm Password</label>
                    <input type="password" id="register-confirm" required>
                </div>
                <button type="submit" class="login-btn">Register</button>
            </form>
            
            <div class="register-link">
                Already have an account? <a href="#" class="switch-tab" data-tab="login">Login</a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-slide active" style="background-image: url('hero1.jpg');">
            <div class="hero-content">
                <h1>Premium Sneakers Collection</h1>
                <p>Discover the latest trends in footwear with our exclusive collection</p>
                <a href="shop.php" class="hero-btn">Shop Now</a>
            </div>
        </div>
        <div class="hero-slide" style="background-image: url('hero2.jpg');">
            <div class="hero-content">
                <h1>Summer Sale - Up to 50% Off</h1>
                <p>Limited time offer on selected items. Don't miss out!</p>
                <a href="shop.php?discount=true" class="hero-btn">Shop Sale</a>
            </div>
        </div>
        <div class="hero-slide" style="background-image: url('hero3.jpg');">
            <div class="hero-content">
                <h1>New Arrivals</h1>
                <p>Fresh styles just landed in our store</p>
                <a href="shop.php?new=true" class="hero-btn">Explore</a>
            </div>
        </div>
        
        <div class="hero-dots">
            <div class="hero-dot active"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Shop by Category</h2>
                <a href="categories.php" class="section-link">
                    View All
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="category-grid">
                <div class="category-card fade-in delay-1">
                    <div class="category-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <h3 class="category-name">Running</h3>
                </div>
                <div class="category-card fade-in delay-2">
                    <div class="category-icon">
                        <i class="fas fa-basketball-ball"></i>
                    </div>
                    <h3 class="category-name">Basketball</h3>
                </div>
                <div class="category-card fade-in delay-3">
                    <div class="category-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3 class="category-name">Casual</h3>
                </div>
                <div class="category-card fade-in delay-4">
                    <div class="category-icon">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                    <h3 class="category-name">Lifestyle</h3>
                </div>
                <div class="category-card fade-in delay-1">
                    <div class="category-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 class="category-name">Premium</h3>
                </div>
                <div class="category-card fade-in delay-2">
                    <div class="category-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3 class="category-name">Kids</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Featured Products</h2>
            <a href="shop.php?featured=true" class="section-link">
                View All
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="product-grid">
            <?php foreach($featured as $product): ?>
                <div class="product-card slide-up">
                    <div class="product-badge">Featured</div>
                    <div class="product-image-container">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-wishlist" data-product="<?= $product['id'] ?>">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="product-category"><?= htmlspecialchars($product['category']) ?></p>
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="product-rating">
                            <div class="stars">
                                <?php 
                                $rating = $product['rating'] ?? 0;
                                $fullStars = floor($rating);
                                $halfStar = ($rating - $fullStars) >= 0.5;
                                
                                for ($i = 1; $i <= 5; $i++): 
                                    if ($i <= $fullStars): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($halfStar && $i == $fullStars + 1): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-count"><?= $rating ? number_format($rating, 1) : '0.0' ?></span>
                        </div>
                        <p class="product-sold"><?= $product['sold'] ?? 0 ?> sold</p>
                        <div class="product-prices">
                            <span class="product-price">Ksh <?= number_format($product['price_ksh']) ?></span>
                            <?php if($product['price_ksh'] < 3500): ?>
                                <span class="product-original-price">Ksh <?= number_format($product['price_ksh'] + 500) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <button class="add-to-cart" data-product="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- New Arrivals -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">New Arrivals</h2>
            <a href="shop.php?new=true" class="section-link">
                View All
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="product-grid">
            <?php foreach($newArrivals as $product): ?>
                <div class="product-card slide-up">
                    <div class="product-badge">New</div>
                    <div class="product-image-container">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-wishlist" data-product="<?= $product['id'] ?>">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="product-category"><?= htmlspecialchars($product['category']) ?></p>
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="product-rating">
                            <div class="stars">
                                <?php 
                                $rating = $product['rating'] ?? 0;
                                $fullStars = floor($rating);
                                $halfStar = ($rating - $fullStars) >= 0.5;
                                
                                for ($i = 1; $i <= 5; $i++): 
                                    if ($i <= $fullStars): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($halfStar && $i == $fullStars + 1): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-count"><?= $rating ? number_format($rating, 1) : '0.0' ?></span>
                        </div>
                        <p class="product-sold"><?= $product['sold'] ?? 0 ?> sold</p>
                        <div class="product-prices">
                            <span class="product-price">Ksh <?= number_format($product['price_ksh']) ?></span>
                            <?php if($product['price_ksh'] < 3500): ?>
                                <span class="product-original-price">Ksh <?= number_format($product['price_ksh'] + 500) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <button class="add-to-cart" data-product="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Hot Deals -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Hot Deals</h2>
            <a href="shop.php?discount=true" class="section-link">
                View All
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="product-grid">
            <?php foreach($hotDeals as $product): ?>
                <div class="product-card slide-up">
                    <div class="product-badge">Sale</div>
                    <div class="product-image-container">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-wishlist" data-product="<?= $product['id'] ?>">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="product-category"><?= htmlspecialchars($product['category']) ?></p>
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="product-rating">
                            <div class="stars">
                                <?php 
                                $rating = $product['rating'] ?? 0;
                                $fullStars = floor($rating);
                                $halfStar = ($rating - $fullStars) >= 0.5;
                                
                                for ($i = 1; $i <= 5; $i++): 
                                    if ($i <= $fullStars): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($halfStar && $i == $fullStars + 1): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-count"><?= $rating ? number_format($rating, 1) : '0.0' ?></span>
                        </div>
                        <p class="product-sold"><?= $product['sold'] ?? 0 ?> sold</p>
                        <div class="product-prices">
                            <span class="product-price">Ksh <?= number_format($product['price_ksh']) ?></span>
                            <span class="product-original-price">Ksh <?= number_format($product['price_ksh'] + 500) ?></span>
                        </div>
                        <div class="product-actions">
                            <button class="add-to-cart" data-product="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Best Sellers -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Best Sellers</h2>
            <a href="shop.php?bestsellers=true" class="section-link">
                View All
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="product-grid">
            <?php foreach($bestSellers as $product): ?>
                <div class="product-card slide-up">
                    <div class="product-badge">Bestseller</div>
                    <div class="product-image-container">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-wishlist" data-product="<?= $product['id'] ?>">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="product-category"><?= htmlspecialchars($product['category']) ?></p>
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="product-rating">
                            <div class="stars">
                                <?php 
                                $rating = $product['rating'] ?? 0;
                                $fullStars = floor($rating);
                                $halfStar = ($rating - $fullStars) >= 0.5;
                                
                                for ($i = 1; $i <= 5; $i++): 
                                    if ($i <= $fullStars): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($halfStar && $i == $fullStars + 1): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-count"><?= $rating ? number_format($rating, 1) : '0.0' ?></span>
                        </div>
                        <p class="product-sold"><?= $product['sold'] ?? 0 ?> sold</p>
                        <div class="product-prices">
                            <span class="product-price">Ksh <?= number_format($product['price_ksh']) ?></span>
                            <?php if($product['price_ksh'] < 3500): ?>
                                <span class="product-original-price">Ksh <?= number_format($product['price_ksh'] + 500) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <button class="add-to-cart" data-product="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Top Brands -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Top Brands</h2>
            <a href="brands.php" class="section-link">
                View All
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="brands-grid">
            <div class="brand-card fade-in delay-1">
                <img src="nike-logo.png" alt="Nike" class="brand-logo">
            </div>
            <div class="brand-card fade-in delay-2">
                <img src="adidas-logo.png" alt="Adidas" class="brand-logo">
            </div>
            <div class="brand-card fade-in delay-3">
                <img src="jordan-logo.png" alt="Jordan" class="brand-logo">
            </div>
            <div class="brand-card fade-in delay-4">
                <img src="puma-logo.png" alt="Puma" class="brand-logo">
            </div>
            <div class="brand-card fade-in delay-1">
                <img src="newbalance-logo.png" alt="New Balance" class="brand-logo">
            </div>
            <div class="brand-card fade-in delay-2">
                <img src="converse-logo.png" alt="Converse" class="brand-logo">
            </div>
            <div class="brand-card fade-in delay-3">
                <img src="vans-logo.png" alt="Vans" class="brand-logo">
            </div>
            <div class="brand-card fade-in delay-4">
                <img src="gucci-logo.png" alt="Gucci" class="brand-logo">
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="section" style="background: var(--gradient); color: white; border-radius: 12px;">
        <div style="max-width: 800px; margin: 0 auto; text-align: center; padding: 2rem;">
            <h2 style="font-size: 1.75rem; margin-bottom: 1rem;">Subscribe to Our Newsletter</h2>
            <p style="margin-bottom: 2rem;">Get the latest updates on new products and upcoming sales</p>
            <form style="display: flex; gap: 1rem;">
                <input type="email" placeholder="Your email address" style="flex-grow: 1; padding: 0.75rem 1rem; border: none; border-radius: 50px;">
                <button type="submit" style="padding: 0.75rem 1.5rem; background: white; color: var(--primary-color); border: none; border-radius: 50px; font-weight: 600; cursor: pointer;">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <a href="index.php" class="footer-logo">BrandX</a>
                <p class="footer-about">Premium sneakers and streetwear for the modern lifestyle. Quality products with fast delivery across Kenya.</p>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h3 class="footer-heading">Quick Links</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="index.php">Home</a></li>
                    <li class="footer-link"><a href="shop.php">Shop</a></li>
                    <li class="footer-link"><a href="about.php">About Us</a></li>
                    <li class="footer-link"><a href="contact.php">Contact</a></li>
                    <li class="footer-link"><a href="blog.php">Blog</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3 class="footer-heading">Customer Service</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="faq.php">FAQs</a></li>
                    <li class="footer-link"><a href="shipping.php">Shipping Policy</a></li>
                    <li class="footer-link"><a href="returns.php">Returns & Refunds</a></li>
                    <li class="footer-link"><a href="size-guide.php">Size Guide</a></li>
                    <li class="footer-link"><a href="privacy.php">Privacy Policy</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3 class="footer-heading">Contact Us</h3>
                <div class="footer-contact">
                    <p><i class="fas fa-map-marker-alt"></i> 123 Sneaker Street, Nairobi, Kenya</p>
                    <p><i class="fas fa-phone-alt"></i> +254 700 123 456</p>
                    <p><i class="fas fa-envelope"></i> info@brandx.co.ke</p>
                    <p><i class="fas fa-clock"></i> Mon-Sat: 9:00 AM - 6:00 PM</p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> BrandX. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Hero Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.hero-dot');
        
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
        
        // Modal Toggle
        const searchBtn = document.getElementById('search-btn');
        const searchModal = document.getElementById('search-modal');
        const closeSearch = document.getElementById('close-search');
        
        const loginBtn = document.getElementById('login-btn');
        const loginModal = document.getElementById('login-modal');
        const closeLogin = document.getElementById('close-login');
        
        if(searchBtn && searchModal && closeSearch) {
            searchBtn.addEventListener('click', () => {
                searchModal.classList.add('active');
            });
            
            closeSearch.addEventListener('click', () => {
                searchModal.classList.remove('active');
            });
        }
        
        if(loginBtn && loginModal && closeLogin) {
            loginBtn.addEventListener('click', () => {
                loginModal.classList.add('active');
            });
            
            closeLogin.addEventListener('click', () => {
                loginModal.classList.remove('active');
            });
        }
        
        // Tab Switching
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        const switchTabs = document.querySelectorAll('.switch-tab');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.getAttribute('data-tab');
                
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                btn.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        switchTabs.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const tabId = link.getAttribute('data-tab');
                
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Search Functionality
        const searchInput = document.querySelector('.search-input');
        const searchResults = document.getElementById('search-results');
        
        if(searchInput && searchResults) {
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                
                if(query.length > 2) {
                    fetch(`search.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            searchResults.innerHTML = '';
                            
                            if(data.length > 0) {
                                data.forEach(product => {
                                    const item = document.createElement('div');
                                    item.className = 'search-item';
                                    item.innerHTML = `
                                        <img src="${product.image}" alt="${product.name}">
                                        <div class="search-item-info">
                                            <h3>${product.name}</h3>
                                            <p>Ksh ${product.price_ksh.toLocaleString()}</p>
                                        </div>
                                    `;
                                    item.addEventListener('click', () => {
                                        window.location.href = `product.php?id=${product.id}`;
                                    });
                                    searchResults.appendChild(item);
                                });
                            } else {
                                searchResults.innerHTML = '<p style="text-align: center; padding: 2rem;">No products found</p>';
                            }
                        });
                } else {
                    searchResults.innerHTML = '<p style="text-align: center; padding: 2rem;">Type at least 3 characters to search</p>';
                }
            });
        }
        
        // Add to Cart
        const addToCartBtns = document.querySelectorAll('.add-to-cart');
        
        addToCartBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const productId = btn.getAttribute('data-product');
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Update cart count
                            const cartCount = document.querySelector('.cart-count');
                            if(cartCount) {
                                cartCount.textContent = data.cart_count;
                            }
                            
                            // Show success message
                            alert('Product added to cart!');
                        } else {
                            alert('Failed to add product to cart: ' + data.message);
                        }
                    });
                <?php else: ?>
                    // Show login modal if not logged in
                    loginModal.classList.add('active');
                <?php endif; ?>
            });
        });
        
        // Wishlist Toggle
        const wishlistBtns = document.querySelectorAll('.product-wishlist');
        
        wishlistBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const productId = btn.getAttribute('data-product');
                const heartIcon = btn.querySelector('i');
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    fetch('toggle_wishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            if(data.action === 'added') {
                                heartIcon.classList.remove('far');
                                heartIcon.classList.add('fas');
                                btn.classList.add('active');
                            } else {
                                heartIcon.classList.remove('fas');
                                heartIcon.classList.add('far');
                                btn.classList.remove('active');
                            }
                        }
                    });
                <?php else: ?>
                    // Show login modal if not logged in
                    loginModal.classList.add('active');
                <?php endif; ?>
            });
        });
        
        // Login Form Submission
        const loginForm = document.getElementById('login-form');
        if(loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const email = document.getElementById('login-email').value;
                const password = document.getElementById('login-password').value;
                const rememberMe = document.getElementById('remember-me').checked;
                
                fetch('login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&remember=${rememberMe}`
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        window.location.reload();
                    } else {
                        alert('Login failed: ' + data.message);
                    }
                });
            });
        }
        
        // Register Form Submission
        const registerForm = document.getElementById('register-form');
        if(registerForm) {
            registerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const name = document.getElementById('register-name').value;
                const email = document.getElementById('register-email').value;
                const password = document.getElementById('register-password').value;
                const confirm = document.getElementById('register-confirm').value;
                
                if(password !== confirm) {
                    alert('Passwords do not match!');
                    return;
                }
                
                fetch('register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        alert('Registration successful! Please login.');
                        document.querySelector('.tab-btn[data-tab="login"]').click();
                    } else {
                        alert('Registration failed: ' + data.message);
                    }
                });
            });
        }
    </script>
</body>
</html>