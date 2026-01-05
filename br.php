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
        /* Global Styles */
        :root {
            --black: #0a192f;
            --white: #FFFFFF;
            --accent: #FF8C00;
            --gray: #222222;
            --light-gray: #f5f5f5;
            --container-bg: #161b22;
            --container-border: #2d3748;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #0a0a0a;
            color: var(--white);
            line-height: 1.7;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        section {
            padding: 40px 0;
        }
        
        h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
            color: var(--accent);
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
            background: var(--accent);
        }
        
        /* Header Styles */
        header {
            background-color: var(--black);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        /* Product Grid Styles */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .product-card {
            background: var(--container-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
            border: 1px solid var(--container-border);
            cursor: pointer;
            margin: 10px;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            border-color: var(--accent);
        }
        
        .product-image {
            width: 100%;
            height: 180px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--white);
            padding: 10px;
        }
        
        .product-image img {
            width: auto;
            max-width: 100%;
            height: auto;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-info h3 {
            margin-bottom: 8px;
            color: var(--white);
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .product-price {
            font-weight: bold;
            color: var(--accent);
            font-size: 1.1rem;
            margin: 10px 0;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            color: #777;
            font-size: 0.8rem;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
            
            .product-image {
                height: 160px;
            }
        }
        
        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .product-image {
                height: 140px;
            }
        }
        
        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
            
            .product-image {
                height: 120px;
            }
        }
        
        /* Existing header styles from original file */
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
            background-color: #fff;
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
        
        .off-screen-menu {
            background: #0d1117;
            color: rgb(248, 245, 245);
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            z-index: 10;
            right: -250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 1.5rem;
            transition: right 0.3s ease;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
        }
        
        .off-screen-menu.active {
            right: 0;
        }
        
        .off-screen-menu ul {
            list-style: none;
            padding: 0;
        }
        
        .off-screen-menu li {
            margin: 20px 0;
            display: block;
            padding: 10px 15px;
            border-bottom: 1px solid #ccc;
        }
        
        .off-screen-menu a {
            color: rgb(255, 255, 255);
            text-decoration: none;
            font-size: 1.5rem;
        }
        
        .logo {
            text-align: center;
            padding: 0;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .logo img {
            height: 70px;
            width: auto;
        }
        
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
            background-color: red;
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
        
        .search-bar {
            display: flex;
            align-items: center;
            margin: 20px auto;
            width: 350px;
            height: 30px;
            padding: 0 15px;
            border-radius: 20px;
            border: 2px solid var(--accent);
            background-color: var(--container-bg);
            color: white;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            justify-content: center;
            text-decoration: none;
            text-align: center;
        }
        
        .search-bar:focus {
            border-color: #287b99;
            background-color: #222;
        }
        
        .search-bar::placeholder {
            color: #4ea8de;
            text-decoration: none;
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
            min-width: calc(100% - 20px);
            margin: 0 10px;
            transition: opacity 0.5s ease-in-out;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .slider img,
        .slider video {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        /* Footer styles */
        footer {
            background-color: var(--container-bg);
            color: #c9d1d9;
            padding: 20px 0;
            text-align: center;
            margin-top: 50px;
            border-top: 1px solid var(--container-border);
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
            font-size: 14px;
        }
        
        .footer a {
            color: #4ea8de;
            text-decoration: none;
        }
        
        .footer-icons-text p {
            text-align: center;
            font-size: 16px;
            color: #4ea8de;
            margin-bottom: 10px;
        }
        
        /* Top brands section */
        .top-brands {
            display: flex;
            justify-content: flex-start;
            overflow-x: auto;
            gap: 10px;
            padding: 5px 10px;
            scroll-snap-type: x mandatory;
        }
        
        .brand-container {
            flex: 0 0 auto;
            max-width: 100px;
            min-width: 100px;
            text-align: center;
            height: 100px;
            background: var(--container-bg);
            border-radius: 8px;
            padding: 10px;
            border: 1px solid var(--container-border);
        }
        
        .brand-container img {
            width: 100px;
            border-radius: 8px;
            transition: transform 0.3s ease;
            height: 100px;
            object-fit: contain;
        }
        
        .brand-container img:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body>
    <header>
        <div class="ham-menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
        
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
        
        <div class="off-screen-menu" id="menu">
            <div class="navlogo">
                <a href="home.php">
                    <img src="uploads/brandxlogo.png" alt="BrandX Logo">
                </a>
            </div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="about_us.php">About us</a></li>
                <li><a href="services.php">Services</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php" style="font-weight: bold; color: red;">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" style="font-weight: bold; color: #4ea8de;">Login</a></li>
                <?php endif; ?>
                <li><a href="admin_dashboard.php" style="font-weight: bold; color: skyblue;">ADM</a></li>
            </ul>
        </div>
    </header>
    
    <a href="search.php">
        <input type="text" class="search-bar" placeholder="Search for products..." readonly>
    </a>
    
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
    
    <div class="container">
        <!-- Featured Products Section -->
        <section>
            <h2>BEST FROM BRANDX</h2>
            <div class="products-grid">
                <?php if ($featuredResult->num_rows > 0): ?>
                    <?php while ($featuredProduct = $featuredResult->fetch_assoc()): ?>
                        <div class="product-card" onclick="window.location.href='product.php?id=<?= $featuredProduct['id'] ?>'">
                            <div class="product-image">
                                <img src="uploads/<?= htmlspecialchars($featuredProduct['image']) ?>" alt="<?= htmlspecialchars($featuredProduct['name']) ?>">
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($featuredProduct['name']) ?></h3>
                                <div class="product-price">Ksh <?= number_format($featuredProduct['price_ksh'], 2) ?></div>
                                <div class="product-meta">
                                    <span><i class="fas fa-tag"></i> <?= ucfirst(htmlspecialchars($featuredProduct['category'])) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No featured products available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- New Arrivals Section -->
        <section>
            <h2>NEW ARRIVALS</h2>
            <div class="products-grid">
                <?php if ($newProductsResult->num_rows > 0): ?>
                    <?php while ($newProduct = $newProductsResult->fetch_assoc()): ?>
                        <div class="product-card" onclick="window.location.href='product.php?id=<?= $newProduct['id'] ?>'">
                            <div class="product-image">
                                <img src="uploads/<?= htmlspecialchars($newProduct['image']) ?>" alt="<?= htmlspecialchars($newProduct['name']) ?>">
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($newProduct['name']) ?></h3>
                                <div class="product-price">Ksh <?= number_format($newProduct['price_ksh'], 2) ?></div>
                                <div class="product-meta">
                                    <span><i class="fas fa-tag"></i> <?= ucfirst(htmlspecialchars($newProduct['category'])) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No newly added products found.</p>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Top Brands Section -->
        <section>
            <h2>TOP BRANDS</h2>
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
        </section>
        
        <!-- All Products Section -->
        <section>
            <h2>RECOMMENDED FOR YOU</h2>
            <div class="products-grid">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="product-card" onclick="window.location.href='product.php?id=<?= $row['id'] ?>'">
                            <div class="product-image">
                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($row['name']) ?></h3>
                                <div class="product-price">Ksh <?= number_format($row['price_ksh'], 2) ?></div>
                                <div class="product-meta">
                                    <span><i class="fas fa-tag"></i> <?= ucfirst(htmlspecialchars($row['category'])) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No products available.</p>
                <?php endif; ?>
            </div>
        </section>
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