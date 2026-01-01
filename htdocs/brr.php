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

// Fetch featured products
$featuredSql = "SELECT p.id, p.name, p.price_ksh, p.image, p.category FROM products p 
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
    <title>BrandX - Quality Sneakers</title>
    <link rel="stylesheet" href="st.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="script.js"></script>
    <script>(function(d,z,s){s.src='https://'+d+'/401/'+z;try{(document.body||document.documentElement).appendChild(s)}catch(e){}})('gizokraijaw.net',8719318,document.createElement('script'))</script>
    <style>
        /* ===== Global Styles ===== */
        :root {
            --black: #0a0a0a;
            --dark-bg: #161b22;
            --container-bg: #1e2229;
            --accent: #4ea8de;
            --text: #c9d1d9;
            --light-gray: #2d333b;
        }
        
        body {
            background-color: var(--black);
            color: var(--text);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            padding-top: 60px; /* Space for fixed header */
        }
        
        /* ===== Header & Navigation ===== */
        header {
            background-color: var(--dark-bg);
            padding: 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            height: 60px;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            height: 100%;
        }
        
        .logo img {
            height: 40px;
            width: auto;
        }
        
        /* Hamburger Menu - Aksa Genset Style */
        .menu-toggle {
            display: none;
            cursor: pointer;
            flex-direction: column;
            gap: 5px;
            z-index: 1001;
        }
        
        .menu-toggle span {
            display: block;
            width: 25px;
            height: 3px;
            background: var(--text);
            transition: all 0.3s;
        }
        
        .menu-toggle.active span:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }
        
        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        
        .menu-toggle.active span:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }
        
        /* Navigation Menu - Aksa Genset Style but Black */
        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-menu li a {
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            padding: 20px 15px;
            display: block;
            transition: all 0.3s;
            position: relative;
        }
        
        .nav-menu li a:after {
            content: '';
            position: absolute;
            bottom: 15px;
            left: 15px;
            width: calc(100% - 30px);
            height: 2px;
            background: var(--accent);
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        
        .nav-menu li a:hover {
            color: var(--accent);
        }
        
        .nav-menu li a:hover:after {
            transform: scaleX(1);
        }
        
        /* Cart Icon - BrandX Style */
        .cart-icon {
            position: absolute;
            top: 15px;
            left: 15px;
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
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 5px;
            font-size: 10px;
            font-weight: bold;
        }
        
        /* ===== BrandX Original Product Containers ===== */
        .product-container {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 15px;
            padding: 10px 20px;
            scroll-behavior: smooth;
            margin: 20px 0;
        }
        
        .product {
            flex: 0 0 calc(16.6667% - 15px);
            background: var(--container-bg);
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            transition: transform 0.3s ease;
            min-width: 200px;
            max-width: 200px;
            border: 1px solid var(--light-gray);
        }
        
        .product:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border-color: var(--accent);
        }
        
        .product-image {
            width: 100%;
            height: 150px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--black);
            border-radius: 5px;
        }
        
        .product-image img {
            width: auto;
            height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
        
        .product h3 {
            font-size: 15px;
            color: var(--accent);
            margin: 10px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .product .price {
            font-size: 14px;
            color: var(--text);
            font-weight: bold;
        }
        
        /* ===== BrandX Banner/Slider ===== */
        .slider-container {
            width: 100%;
            overflow: hidden;
            position: relative;
            height: 200px;
            margin-top: 20px;
        }
        
        .slider {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 200px;
        }
        
        .slide {
            min-width: 100%;
            position: relative;
        }
        
        .slider img,
        .slider video {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        /* ===== Top Brands Section ===== */
        .top-brands {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 10px 20px;
            scroll-behavior: smooth;
        }
        
        .brand-container {
            flex: 0 0 auto;
            width: 100px;
            height: 100px;
            background: var(--container-bg);
            border-radius: 8px;
            padding: 10px;
            border: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .brand-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        /* ===== Footer ===== */
        footer {
            background-color: var(--dark-bg);
            color: var(--text);
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
            border-top: 1px solid var(--light-gray);
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
        }
        
        /* ===== Responsive Design ===== */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }
            
            .nav-menu {
                position: fixed;
                top: 60px;
                left: -100%;
                width: 100%;
                background: var(--dark-bg);
                flex-direction: column;
                align-items: center;
                padding: 20px 0;
                transition: left 0.3s;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
            }
            
            .nav-menu.active {
                left: 0;
            }
            
            .nav-menu li {
                width: 100%;
                text-align: center;
            }
            
            .nav-menu li a {
                padding: 15px;
            }
            
            .product {
                min-width: 160px;
            }
        }
        
        /* ===== Section Headers ===== */
        h2 {
            text-align: center;
            font-size: 24px;
            color: var(--accent);
            margin: 30px 0 15px;
            position: relative;
            padding-bottom: 10px;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent);
        }
        
        /* ===== Search Bar ===== */
        .search-bar {
            display: block;
            width: 350px;
            margin: 20px auto;
            padding: 8px 15px;
            border-radius: 20px;
            border: 2px solid var(--accent);
            background-color: var(--container-bg);
            color: var(--text);
            font-size: 16px;
            text-align: center;
            outline: none;
        }
        
        .search-bar::placeholder {
            color: var(--accent);
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <!-- Header with Aksa Genset Navigation Style -->
    <header>
        <div class="header-container">
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
            
            <div class="menu-toggle" id="mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <ul class="nav-menu">
                <li><a href="home.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="about_us.php">About</a></li>
                <li><a href="services.php">Services</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    
    <!-- Search Bar -->
    <a href="search.php">
        <input type="text" class="search-bar" placeholder="Search for products..." readonly>
    </a>
    
    <!-- Slider/Banner (Original BrandX Style) -->
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
                                <video autoplay muted loop>
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
    <h2>BEST FROM BRANDX</h2>
    <div class="product-container">
        <?php if ($featuredResult->num_rows > 0): ?>
            <?php while ($featuredProduct = $featuredResult->fetch_assoc()): ?>
                <div class="product" onclick="window.location.href='product.php?id=<?= $featuredProduct['id'] ?>'">
                    <div class="product-image">
                        <img src="uploads/<?= htmlspecialchars($featuredProduct['image']) ?>" alt="<?= htmlspecialchars($featuredProduct['name']) ?>">
                    </div>
                    <h3><?= htmlspecialchars($featuredProduct['name']) ?></h3>
                    <p class="price">Ksh <?= number_format($featuredProduct['price_ksh'], 2) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No featured products available.</p>
        <?php endif; ?>
    </div>
    
    <!-- New Arrivals Section -->
    <h2>NEW ARRIVALS</h2>
    <div class="product-container">
        <?php if ($newProductsResult->num_rows > 0): ?>
            <?php while ($newProduct = $newProductsResult->fetch_assoc()): ?>
                <div class="product" onclick="window.location.href='product.php?id=<?= $newProduct['id'] ?>'">
                    <div class="product-image">
                        <img src="uploads/<?= htmlspecialchars($newProduct['image']) ?>" alt="<?= htmlspecialchars($newProduct['name']) ?>">
                    </div>
                    <h3><?= htmlspecialchars($newProduct['name']) ?></h3>
                    <p class="price">Ksh <?= number_format($newProduct['price_ksh'], 2) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No new arrivals found.</p>
        <?php endif; ?>
    </div>
    
    <!-- Top Brands Section -->
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
        <!-- [Other brand containers remain the same] -->
    </div>
    
    <!-- Recommended Products Section -->
    <h2>RECOMMENDED FOR YOU</h2>
    <div class="product-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product" onclick="window.location.href='product.php?id=<?= $row['id'] ?>'">
                    <div class="product-image">
                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                    </div>
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p class="price">Ksh <?= number_format($row['price_ksh'], 2) ?></p>
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
            <a href="https://wa.me/254773743248" target="_blank" style="color: #4ea8de; text-decoration: none;">Simon Ngugi</a>.
        </p>
    </footer>
    
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
    
    <script>
    // Slider functionality
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