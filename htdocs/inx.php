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
    $adDirectory = 'ads/';
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
    <title>BrandX - Premium Sneakers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <style>
    :root {
      --primary: #0a1220;
      --primary-light: #15253f;
      --secondary: #3a7bd5;
      --accent: #00d2ff;
      --text: #f5f5f5;
      --text-secondary: #cccccc;
      --footer-dark: #080f1a;
      --hot-deal: #ff4757;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--primary);
      color: var(--text);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      padding-top: 80px; /* To account for fixed header */
    }

    header {
      background-color: var(--primary-light);
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

    .logo img {
      height: 50px;
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

    /* Slider styles */
    .slider-container {
      width: 100%;
      overflow: hidden;
      position: relative;
    }

    .slider {
      display: flex;
      transition: transform 0.5s ease-in-out;
      height: 400px;
    }

    .slide {
      min-width: 100%;
      position: relative;
    }

    .slide img,
    .slide video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .slide-content {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(10, 18, 32, 0.9), transparent);
      padding: 2rem;
      color: white;
    }

    .slide-content h2 {
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    .slide-content p {
      font-size: 1rem;
      margin-bottom: 1rem;
    }

    .slide-button {
      background-color: var(--accent);
      color: var(--primary);
      border: none;
      padding: 0.7rem 1.5rem;
      border-radius: 5px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }

    .slide-button:hover {
      background-color: white;
      transform: translateY(-2px);
    }

    /* Section titles */
    .section-title {
      font-size: 1.5rem;
      margin: 2rem 0 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid var(--accent);
      display: inline-block;
    }

    .section-title i {
      margin-right: 0.8rem;
      color: var(--accent);
    }

    main {
      flex: 1;
      padding: 2rem;
      max-width: 1600px;
      margin: 0 auto;
      width: 100%;
    }

    /* Product grid styles */
    .products-container {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

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
    }

    .product-card {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      transition: transform 0.3s ease;
      position: relative;
    }

    .product-card:hover {
      transform: translateY(-5px);
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
      background-color: var(--accent);
      color: black;
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
    }

    .product-rating {
      color: gold;
      margin-bottom: 0.5rem;
      font-size: 0.8rem;
    }

    .price-container {
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
    }

    .original-price {
      text-decoration: line-through;
      color: var(--text-secondary);
      font-size: 0.8rem;
      margin-right: 0.5rem;
    }

    .current-price {
      color: var(--accent);
      font-weight: bold;
      font-size: 1rem;
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

    /* Hot deals styling */
    .hot-deals .product-card {
      border: 2px solid var(--hot-deal);
      position: relative;
    }

    .hot-tag {
      position: absolute;
      top: 10px;
      right: 10px;
      background-color: var(--hot-deal);
      color: white;
      padding: 0.3rem 0.6rem;
      border-radius: 4px;
      font-weight: bold;
      font-size: 0.8rem;
      z-index: 2;
    }

    /* Brands styling */
    .brands-container {
      display: flex;
      overflow-x: auto;
      gap: 0.8rem;
      padding: 1rem 0.5rem;
      scrollbar-width: thin;
    }
    
    .brand-card {
      flex: 0 0 auto;
      width: 90px;
      height: 90px;
      background-color: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 10px;
      padding: 0.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }
    
    .brand-card:hover {
      border-color: var(--accent);
      transform: translateY(-3px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .brand-logo {
      width: 40px;
      height: 40px;
      object-fit: contain;
      margin-bottom: 0.3rem;
      background-color: white;
      padding: 0.2rem;
      border-radius: 5px;
    }
    
    .brand-name {
      text-align: center;
      font-size: 0.7rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
    }

    /* View more button */
    .view-more-btn {
      background: linear-gradient(135deg, var(--accent) 0%, var(--secondary) 100%);
      border: none;
      color: white;
      padding: 0.8rem 2.5rem;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      font-size: 1rem;
      box-shadow: 0 4px 12px rgba(0, 210, 255, 0.3);
      display: block;
      margin: 1.5rem auto 3rem;
    }

    .view-more-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 210, 255, 0.4);
    }

    /* Footer styles */
    footer {
      background-color: var(--footer-dark);
      color: var(--text-secondary);
      padding: 3rem 2rem;
      text-align: left;
      margin-top: auto;
    }

    .footer-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .footer-section {
      margin-bottom: 1.5rem;
    }

    .footer-section h3 {
      color: var(--accent);
      margin-bottom: 1.5rem;
      font-size: 1.2rem;
    }

    .footer-section p, .footer-section a {
      color: var(--text-secondary);
      margin-bottom: 1rem;
      display: block;
      text-decoration: none;
      line-height: 1.6;
    }

    .footer-section a:hover {
      color: var(--accent);
    }

    .copyright {
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      text-align: center;
      color: var(--text-secondary);
      font-size: 0.9rem;
    }

    /* Social icons */
    .footer-icons {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      margin: 1.5rem 0;
    }

    .footer-icons a {
      color: var(--text-secondary);
      font-size: 1.5rem;
      transition: color 0.3s;
    }

    .footer-icons a:hover {
      color: var(--accent);
    }

    /* Mobile menu toggle */
    .ham-menu {
      height: 50px;
      width: 50px;
      margin-left: auto;
      position: relative;
      cursor: pointer;
      z-index: 1000;
    }

    .ham-menu span {
      height: 3px;
      width: 100%;
      background-color: var(--accent);
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

    /* When the hamburger menu is active */
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

    /* Off-screen menu */
    .off-screen-menu {
      background: var(--primary-light);
      color: var(--text);
      height: 100vh;
      width: 250px;
      position: fixed;
      top: 0;
      right: -250px;
      z-index: 10;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      transition: right 0.3s ease;
      box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
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
      display: block;
      padding: 10px 15px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .off-screen-menu a {
      color: var(--text);
      text-decoration: none;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .off-screen-menu a:hover {
      color: var(--accent);
    }

    .menu-logo {
      margin-bottom: 2rem;
    }

    .menu-logo img {
      height: 60px;
    }

    /* Cart icon styles */
    .header-cart {
      position: relative;
      display: flex;
      align-items: center;
    }

    .cart-count {
      position: absolute;
      top: -8px;
      right: -8px;
      background-color: var(--hot-deal);
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 0.7rem;
      font-weight: bold;
    }

    /* Search bar */
    .search-bar {
      display: flex;
      align-items: center;
      margin: 1rem auto;
      width: 100%;
      max-width: 500px;
      height: 40px;
      padding: 0 15px;
      border-radius: 20px;
      border: 1px solid rgba(255,255,255,0.2);
      background-color: rgba(255,255,255,0.05);
      color: var(--text);
      font-size: 0.9rem;
      outline: none;
      transition: all 0.3s ease;
    }

    .search-bar:focus {
      border-color: var(--accent);
      background-color: rgba(255,255,255,0.1);
    }

    .search-bar::placeholder {
      color: var(--text-secondary);
    }

    /* Responsive adjustments */
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

      .slider {
        height: 300px;
      }

      .products-container {
        grid-template-columns: repeat(2, 1fr);
      }

      .footer-content {
        grid-template-columns: 1fr;
      }
    }

    /* Chat icon */
    .chat-icon-container {
      position: fixed;
      left: 20px;
      bottom: 20px;
      z-index: 1000;
    }

    .chat-icon img {
      width: 60px;
      height: 60px;
      cursor: pointer;
      border-radius: 50%;
      box-shadow: 0 0 15px rgba(0, 210, 255, 0.5);
      transition: all 0.3s;
    }

    .chat-icon img:hover {
      transform: scale(1.1);
      box-shadow: 0 0 20px rgba(0, 210, 255, 0.7);
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script>(function(d,z,s){s.src='https://'+d+'/401/'+z;try{(document.body||document.documentElement).appendChild(s)}catch(e){}})('gizokraijaw.net',8719318,document.createElement('script'))</script>
</head>
<body>
    <header>
        <div class="ham-menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
        
        <div class="logo">
            <a href="home.php" class="glowing-logo">
                <img src="uploads/brandxlogo.png" alt="BrandX Logo">
            </a>
        </div>
        
        <div class="header-cart">
            <a href="cart.php">
                <i class="fas fa-shopping-cart"></i>
                <?php if (isset($cartCount) && $cartCount > 0): ?>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="off-screen-menu" id="menu">
            <div class="menu-logo">
                <img src="uploads/brandxlogo.png" alt="BrandX Logo">
            </div>
            <ul>
                <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <li><a href="orders.php"><i class="fas fa-clipboard-list"></i> My Orders</a></li>
                <li><a href="about_us.php"><i class="fas fa-info-circle"></i> About us</a></li>
                <li><a href="services.php"><i class="fas fa-concierge-bell"></i> Services</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php" style="color: var(--hot-deal);"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" style="color: var(--accent);"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>
                <li><a href="admin_dashboard.php" style="color: var(--accent);"><i class="fas fa-user-shield"></i> Admin</a></li>
            </ul>
        </div>
    </header>

    <main>
        <!-- Search Bar -->
        <a href="search.php">
            <input type="text" class="search-bar" placeholder="Search for products..." readonly>
        </a>

        <!-- Slider Banner -->
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

        <!-- Featured Products Section -->
        <h2 class="section-title"><i class="fas fa-fire"></i> Best from BrandX</h2>
        <div class="products-container">
            <?php
            $featuredSql = "SELECT p.id, p.name, p.price_ksh, p.image FROM products p 
                            INNER JOIN featured_products fp ON p.id = fp.product_id
                            LIMIT 6";
            $featuredResult = $connection->query($featuredSql);

            if ($featuredResult->num_rows > 0) {
                while ($featuredProduct = $featuredResult->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<div class="product-image">';
                    echo '<a href="product.php?id=' . $featuredProduct['id'] . '">';
                    echo '<img src="uploads/' . htmlspecialchars($featuredProduct['image']) . '" alt="' . htmlspecialchars($featuredProduct['name']) . '">';
                    echo '</a>';
                    echo '</div>';
                    echo '<div class="product-details">';
                    echo '<div class="product-title">' . htmlspecialchars($featuredProduct['name']) . '</div>';
                    echo '<div class="price-container">';
                    echo '<span class="current-price">Ksh ' . number_format($featuredProduct['price_ksh'], 2) . '</span>';
                    echo '</div>';
                    echo '<a href="product.php?id=' . $featuredProduct['id'] . '" class="cart-icon">';
                    echo '<i class="fas fa-shopping-cart"></i>';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No featured products available at the moment.</p>';
            }
            ?>
        </div>

        <!-- New Arrivals Section -->
        <h2 class="section-title"><i class="fas fa-star"></i> New Arrivals</h2>
        <div class="products-container">
            <?php
            if ($newProductsResult->num_rows > 0) {
                while ($newProduct = $newProductsResult->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<div class="product-image">';
                    echo '<a href="product.php?id=' . $newProduct['id'] . '">';
                    echo '<img src="uploads/' . htmlspecialchars($newProduct['image']) . '" alt="' . htmlspecialchars($newProduct['name']) . '">';
                    echo '</a>';
                    echo '</div>';
                    echo '<div class="product-details">';
                    echo '<div class="product-title">' . htmlspecialchars($newProduct['name']) . '</div>';
                    echo '<div class="price-container">';
                    echo '<span class="current-price">Ksh ' . number_format($newProduct['price_ksh'], 2) . '</span>';
                    echo '</div>';
                    echo '<a href="product.php?id=' . $newProduct['id'] . '" class="cart-icon">';
                    echo '<i class="fas fa-shopping-cart"></i>';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No newly added products found.</p>';
            }
            ?>
        </div>

        <!-- Top Brands Section -->
        <h2 class="section-title"><i class="fas fa-crown"></i> Top Brands</h2>
        <div class="brands-container">
            <div class="brand-card">
                <a href="category.php?category=nike">
                    <img src="uploads/nike.png" alt="Nike" class="brand-logo">
                    <span class="brand-name">Nike</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=vans">
                    <img src="uploads/vans.png" alt="Vans" class="brand-logo">
                    <span class="brand-name">Vans</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=jordan">
                    <img src="uploads/jordan.png" alt="Jordan" class="brand-logo">
                    <span class="brand-name">Jordan</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=newbalance">
                    <img src="uploads/nb.png" alt="New Balance" class="brand-logo">
                    <span class="brand-name">New Balance</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=adidas">
                    <img src="uploads/adidas.png" alt="Adidas" class="brand-logo">
                    <span class="brand-name">Adidas</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=puma">
                    <img src="uploads/puma.png" alt="Puma" class="brand-logo">
                    <span class="brand-name">Puma</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=clarks">
                    <img src="uploads/clarks.png" alt="Clarks" class="brand-logo">
                    <span class="brand-name">Clarks</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=fila">
                    <img src="uploads/fila.png" alt="Fila" class="brand-logo">
                    <span class="brand-name">Fila</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=converse">
                    <img src="uploads/converse.png" alt="Converse" class="brand-logo">
                    <span class="brand-name">Converse</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=timberland">
                    <img src="uploads/timbaland.png" alt="Timberland" class="brand-logo">
                    <span class="brand-name">Timberland</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=gucci">
                    <img src="uploads/gucci.png" alt="Gucci" class="brand-logo">
                    <span class="brand-name">Gucci</span>
                </a>
            </div>
            <div class="brand-card">
                <a href="category.php?category=lv">
                    <img src="uploads/lv.png" alt="Louis Vuitton" class="brand-logo">
                    <span class="brand-name">Louis Vuitton</span>
                </a>
            </div>
        </div>

        <!-- Recommended Products Section -->
        <h2 class="section-title"><i class="fas fa-thumbs-up"></i> Recommended For You</h2>
        <div class="products-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<div class="product-image">';
                    echo '<a href="product.php?id=' . $row['id'] . '">';
                    echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '</a>';
                    echo '</div>';
                    echo '<div class="product-details">';
                    echo '<div class="product-title">' . htmlspecialchars($row['name']) . '</div>';
                    echo '<div class="price-container">';
                    echo '<span class="current-price">Ksh ' . number_format($row['price_ksh'], 2) . '</span>';
                    echo '</div>';
                    echo '<a href="product.php?id=' . $row['id'] . '" class="cart-icon">';
                    echo '<i class="fas fa-shopping-cart"></i>';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products available.</p>';
            }
            ?>
        </div>

        <button class="view-more-btn">View More Products <i class="fas fa-chevron-right"></i></button>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About BrandX</h3>
                <p>Your premier destination for the latest and greatest in footwear fashion. We offer premium sneakers with the best quality and style.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="home.php"><