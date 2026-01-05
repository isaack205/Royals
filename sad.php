<?php
require_once 'db.php';

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        // Set cookie to remember user for 30 days
        setcookie('remember_user', $user['id'], time() + (30 * 24 * 60 * 60), '/');
        
        // Redirect to prevent form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $loginError = "Invalid username or password";
    }
}

// Handle registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        
        // Auto-login after registration
        $userId = $pdo->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        
        // Redirect to prevent form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $registerError = "Registration failed: " . (strpos($e->getMessage(), 'Duplicate entry') ? "Username or email already exists" : "Database error");
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('remember_user', '', time() - 3600, '/');
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle search
$searchResults = [];
if (isset($_GET['search'])) {
    $searchTerm = '%'.$_GET['search'].'%';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE title LIKE ? OR description LIKE ? LIMIT 10");
    $stmt->execute([$searchTerm, $searchTerm]);
    $searchResults = $stmt->fetchAll();
}

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    if (!$isLoggedIn) {
        $showLoginModal = true;
        $productToAdd = $_POST['product_id'];
    } else {
        $productId = $_POST['product_id'];
        
        // Check if product already in cart
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $productId]);
        $existingItem = $stmt->fetch();
        
        if ($existingItem) {
            // Update quantity
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
            $stmt->execute([$existingItem['id']]);
        } else {
            // Add new item
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $productId]);
        }
        
        $cartSuccess = "Product added to cart!";
    }
}

// Get products for different sections with pagination
$limit = 24;
$trendingPage = isset($_GET['trending_page']) ? (int)$_GET['trending_page'] : 1;
$newArrivalsPage = isset($_GET['new_arrivals_page']) ? (int)$_GET['new_arrivals_page'] : 1;
$hotDealsPage = isset($_GET['hot_deals_page']) ? (int)$_GET['hot_deals_page'] : 1;

// Fetch trending products
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY rating DESC, review_count DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, ($trendingPage - 1) * $limit, PDO::PARAM_INT);
$stmt->execute();
$trendingProducts = $stmt->fetchAll();

// Fetch new arrivals
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, ($newArrivalsPage - 1) * $limit, PDO::PARAM_INT);
$stmt->execute();
$newArrivals = $stmt->fetchAll();

// Fetch hot deals
$stmt = $pdo->prepare("SELECT * FROM products WHERE is_hot_deal = TRUE LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, ($hotDealsPage - 1) * $limit, PDO::PARAM_INT);
$stmt->execute();
$hotDeals = $stmt->fetchAll();

// Fetch brands
$brands = $pdo->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Your existing head content -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BrandX - Premium Sneakers</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
  <style>
    /* Your existing CSS styles */
    :root {
      --primary: #0a1220;
      --primary-light: #15253f;
      --secondary: #3a7bd5;
      --accent: #00d2ff;
      --text: #f5f5f5;
      --text-secondary: #cccccc;
      --footer-dark: #080f1a;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    /* Rest of your CSS styles... */
    
    /* Add modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1002;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
    }
    
    .modal-content {
      background-color: var(--primary-light);
      margin: 10% auto;
      padding: 2rem;
      border-radius: 10px;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .close-modal {
      color: var(--text-secondary);
      float: right;
      font-size: 1.5rem;
      cursor: pointer;
    }
    
    .close-modal:hover {
      color: var(--accent);
    }
    
    .modal-title {
      margin-bottom: 1.5rem;
      color: var(--accent);
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--text);
    }
    
    .form-group input {
      width: 100%;
      padding: 0.8rem;
      background-color: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 5px;
      color: var(--text);
    }
    
    .modal-button {
      background-color: var(--accent);
      color: var(--primary);
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 5px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      width: 100%;
      margin-top: 1rem;
    }
    
    .modal-button:hover {
      background-color: white;
    }
    
    .error-message {
      color: #ff4757;
      margin-top: 0.5rem;
      font-size: 0.9rem;
    }
    
    .success-message {
      color: #2ed573;
      margin-top: 0.5rem;
      font-size: 0.9rem;
    }
    
    /* Search modal styles */
    .search-modal {
      display: none;
      position: fixed;
      z-index: 1002;
      left: 0;
      top: 80px;
      width: 100%;
      background-color: var(--primary-light);
      padding: 1rem;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .search-results {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }
    
    .search-result-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.5rem;
      background-color: rgba(255,255,255,0.05);
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .search-result-item:hover {
      background-color: rgba(255,255,255,0.1);
    }
    
    .search-result-image {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 5px;
    }
    
    .search-result-info {
      flex: 1;
    }
    
    .search-result-title {
      font-size: 0.9rem;
      margin-bottom: 0.2rem;
    }
    
    .search-result-price {
      font-size: 0.8rem;
      color: var(--accent);
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <a href="#" class="logo glowing-logo">BrandX</a>
    
    <!-- Search bar -->
    <div class="search-container" style="flex: 1; max-width: 500px; margin: 0 1rem; position: relative;">
      <form id="searchForm" action="" method="GET">
        <input type="text" name="search" id="searchInput" placeholder="Search for products..." 
               style="width: 100%; padding: 0.8rem; background-color: rgba(255,255,255,0.1); 
               border: 1px solid rgba(255,255,255,0.2); border-radius: 25px; color: var(--text);">
      </form>
      <div class="search-modal" id="searchModal">
        <div id="searchResultsContainer"></div>
      </div>
    </div>
    
    <button class="hamburger" id="hamburger">
      <i class="fas fa-bars"></i>
    </button>
    
    <nav>
      <a href="#trending"><i class="fas fa-fire"></i> Trending</a>
      <a href="#new-arrivals"><i class="fas fa-star"></i> New Arrivals</a>
      <a href="#recommended"><i class="fas fa-thumbs-up"></i> Recommended</a>
      <a href="#" id="searchButton"><i class="fas fa-search"></i> Search</a>
      <?php if ($isLoggedIn): ?>
        <a href="?logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <a href="#"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['username']) ?></a>
      <?php else: ?>
        <a href="#" id="loginButton"><i class="fas fa-user"></i> Login</a>
      <?php endif; ?>
      <a href="#"><i class="fas fa-shopping-cart"></i> Cart</a>
    </nav>
  </header>

  <div class="mobile-nav" id="mobileNav">
    <a href="#trending"><i class="fas fa-fire"></i> Trending</a>
    <a href="#new-arrivals"><i class="fas fa-star"></i> New Arrivals</a>
    <a href="#recommended"><i class="fas fa-thumbs-up"></i> Recommended</a>
    <a href="#" id="mobileSearchButton"><i class="fas fa-search"></i> Search</a>
    <?php if ($isLoggedIn): ?>
      <a href="?logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
      <a href="#"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['username']) ?></a>
    <?php else: ?>
      <a href="#" id="mobileLoginButton"><i class="fas fa-user"></i> Login</a>
    <?php endif; ?>
    <a href="#"><i class="fas fa-shopping-cart"></i> Cart</a>
  </div>

  <!-- Login Modal -->
  <div id="loginModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" id="closeLoginModal">&times;</span>
      <h2 class="modal-title">Login</h2>
      <form id="loginForm" method="POST">
        <div class="form-group">
          <label for="loginUsername">Username</label>
          <input type="text" id="loginUsername" name="username" required>
        </div>
        <div class="form-group">
          <label for="loginPassword">Password</label>
          <input type="password" id="loginPassword" name="password" required>
        </div>
        <?php if (isset($loginError)): ?>
          <div class="error-message"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>
        <button type="submit" name="login" class="modal-button">Login</button>
      </form>
      <p style="margin-top: 1rem; text-align: center; color: var(--text-secondary);">
        Don't have an account? <a href="#" id="showRegister" style="color: var(--accent);">Register here</a>
      </p>
    </div>
  </div>

  <!-- Register Modal -->
  <div id="registerModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" id="closeRegisterModal">&times;</span>
      <h2 class="modal-title">Register</h2>
      <form id="registerForm" method="POST">
        <div class="form-group">
          <label for="registerUsername">Username</label>
          <input type="text" id="registerUsername" name="username" required>
        </div>
        <div class="form-group">
          <label for="registerEmail">Email</label>
          <input type="email" id="registerEmail" name="email" required>
        </div>
        <div class="form-group">
          <label for="registerPassword">Password</label>
          <input type="password" id="registerPassword" name="password" required>
        </div>
        <?php if (isset($registerError)): ?>
          <div class="error-message"><?= htmlspecialchars($registerError) ?></div>
        <?php endif; ?>
        <button type="submit" name="register" class="modal-button">Register</button>
      </form>
      <p style="margin-top: 1rem; text-align: center; color: var(--text-secondary);">
        Already have an account? <a href="#" id="showLogin" style="color: var(--accent);">Login here</a>
      </p>
    </div>
  </div>

  <!-- Rest of your HTML content with PHP loops for products -->
  <!-- Slider Banner -->
  <div class="swiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <img src="https://source.unsplash.com/random/1600x600/?sneakers,shoes" alt="Banner 1">
        <div class="slide-content">
          <h2>Summer Collection 2023</h2>
          <p>Discover our latest sneakers with up to 40% discount</p>
          <button class="slide-button">Shop Now</button>
        </div>
      </div>
      <!-- More slides... -->
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>

  <main>
    <!-- Trending Section -->
    <h2 id="trending" class="section-title"><i class="fas fa-fire"></i> Trending Now</h2>
    <div class="products-container">
      <?php foreach ($trendingProducts as $product): ?>
        <div class="product-card">
          <div class="product-image">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
            <?php if ($product['discount_percent'] > 0): ?>
              <span class="discount-badge"><?= $product['discount_percent'] ?>% OFF</span>
            <?php endif; ?>
          </div>
          <div class="product-details">
            <div class="product-title"><?= htmlspecialchars($product['title']) ?></div>
            <div class="product-rating">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php if ($i <= floor($product['rating'])): ?>
                  <i class="fas fa-star"></i>
                <?php elseif ($i - 0.5 <= $product['rating']): ?>
                  <i class="fas fa-star-half-alt"></i>
                <?php else: ?>
                  <i class="far fa-star"></i>
                <?php endif; ?>
              <?php endfor; ?>
              (<?= $product['review_count'] ?>)
            </div>
            <div class="price-container">
              <?php if ($product['original_price'] > $product['price']): ?>
                <span class="original-price">$<?= number_format($product['original_price'], 2) ?></span>
              <?php endif; ?>
              <span class="current-price">$<?= number_format($product['price'], 2) ?></span>
            </div>
            <form method="POST" class="cart-icon-form">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button type="submit" name="add_to_cart" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (count($trendingProducts) >= $limit): ?>
      <div style="text-align: center; margin: 1.5rem 0 3rem;">
        <a href="?trending_page=<?= $trendingPage + 1 ?>#trending" class="view-more-button">View More Trending</a>
      </div>
    <?php endif; ?>

    <!-- Hot Deals Section -->
    <h2 class="section-title"><i class="fas fa-bolt" style="color: #FF5722;"></i> Hot Deals</h2>
    <div class="products-container hot-deals">
      <?php foreach ($hotDeals as $product): ?>
        <div class="product-card">
          <div class="product-image">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
            <span class="hot-tag">HOT DEAL</span>
            <?php if ($product['discount_percent'] > 0): ?>
              <span class="discount-badge"><?= $product['discount_percent'] ?>% OFF</span>
            <?php endif; ?>
          </div>
          <div class="product-details">
            <div class="product-title"><?= htmlspecialchars($product['title']) ?></div>
            <div class="product-rating">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php if ($i <= floor($product['rating'])): ?>
                  <i class="fas fa-star"></i>
                <?php elseif ($i - 0.5 <= $product['rating']): ?>
                  <i class="fas fa-star-half-alt"></i>
                <?php else: ?>
                  <i class="far fa-star"></i>
                <?php endif; ?>
              <?php endfor; ?>
              (<?= $product['review_count'] ?>)
            </div>
            <div class="price-container">
              <?php if ($product['original_price'] > $product['price']): ?>
                <span class="original-price">$<?= number_format($product['original_price'], 2) ?></span>
              <?php endif; ?>
              <span class="current-price" style="color: #FF5722;">$<?= number_format($product['price'], 2) ?></span>
            </div>
            <form method="POST" class="cart-icon-form">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button type="submit" name="add_to_cart" class="cart-icon" style="background-color: rgba(255,87,34,0.1);">
                <i class="fas fa-shopping-cart"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (count($hotDeals) >= $limit): ?>
      <div style="text-align: center; margin: 1.5rem 0 3rem;">
        <a href="?hot_deals_page=<?= $hotDealsPage + 1 ?>#hot-deals" class="view-more-button">View More Hot Deals</a>
      </div>
    <?php endif; ?>

    <!-- Top Brands Section -->
    <h2 class="section-title" style="margin-bottom: 0.5rem;">
      <i class="fas fa-crown" style="color: #FFD700;"></i> 
      Top Brands
    </h2>
    <div class="brands-container">
      <?php foreach ($brands as $brand): ?>
        <div class="brand-card">
          <img src="https://logo.clearbit.com/<?= strtolower(str_replace(' ', '', $brand['brand'])) ?>.com" 
               alt="<?= htmlspecialchars($brand['brand']) ?>" class="brand-logo">
          <span class="brand-name"><?= htmlspecialchars($brand['brand']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- New Arrivals Section -->
    <h2 id="new-arrivals" class="section-title"><i class="fas fa-star"></i> New Arrivals</h2>
    <div class="products-container">
      <?php foreach ($newArrivals as $product): ?>
        <div class="product-card">
          <div class="product-image">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
            <?php if ($product['discount_percent'] > 0): ?>
              <span class="discount-badge"><?= $product['discount_percent'] ?>% OFF</span>
            <?php endif; ?>
          </div>
          <div class="product-details">
            <div class="product-title"><?= htmlspecialchars($product['title']) ?></div>
            <div class="product-rating">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php if ($i <= floor($product['rating'])): ?>
                  <i class="fas fa-star"></i>
                <?php elseif ($i - 0.5 <= $product['rating']): ?>
                  <i class="fas fa-star-half-alt"></i>
                <?php else: ?>
                  <i class="far fa-star"></i>
                <?php endif; ?>
              <?php endfor; ?>
              (<?= $product['review_count'] ?>)
            </div>
            <div class="price-container">
              <?php if ($product['original_price'] > $product['price']): ?>
                <span class="original-price">$<?= number_format($product['original_price'], 2) ?></span>
              <?php endif; ?>
              <span class="current-price">$<?= number_format($product['price'], 2) ?></span>
            </div>
            <form method="POST" class="cart-icon-form">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button type="submit" name="add_to_cart" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (count($newArrivals) >= $limit): ?>
      <div style="text-align: center; margin: 1.5rem 0 3rem;">
        <a href="?new_arrivals_page=<?= $newArrivalsPage + 1 ?>#new-arrivals" class="view-more-button">View More New Arrivals</a>
      </div>
    <?php endif; ?>

    <!-- Recommended Section -->
    <h2 id="recommended" class="section-title"><i class="fas fa-thumbs-up"></i> Recommended For You</h2>
    <div class="products-container">
      <?php 
      // For demo, we'll just show a mix of products
      $recommendedProducts = array_merge($trendingProducts, $hotDeals, $newArrivals);
      shuffle($recommendedProducts);
      $recommendedProducts = array_slice($recommendedProducts, 0, $limit);
      ?>
      <?php foreach ($recommendedProducts as $product): ?>
        <div class="product-card">
          <div class="product-image">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
            <?php if ($product['discount_percent'] > 0): ?>
              <span class="discount-badge"><?= $product['discount_percent'] ?>% OFF</span>
            <?php endif; ?>
          </div>
          <div class="product-details">
            <div class="product-title"><?= htmlspecialchars($product['title']) ?></div>
            <div class="product-rating">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php if ($i <= floor($product['rating'])): ?>
                  <i class="fas fa-star"></i>
                <?php elseif ($i - 0.5 <= $product['rating']): ?>
                  <i class="fas fa-star-half-alt"></i>
                <?php else: ?>
                  <i class="far fa-star"></i>
                <?php endif; ?>
              <?php endfor; ?>
              (<?= $product['review_count'] ?>)
            </div>
            <div class="price-container">
              <?php if ($product['original_price'] > $product['price']): ?>
                <span class="original-price">$<?= number_format($product['original_price'], 2) ?></span>
              <?php endif; ?>
              <span class="current-price">$<?= number_format($product['price'], 2) ?></span>
            </div>
            <form method="POST" class="cart-icon-form">
              <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
              <button type="submit" name="add_to_cart" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align: center; margin: 2rem 0 3rem;">
      <button style="
        background: transparent;
        border: 2px solid var(--accent);
        color: var(--accent);
        padding: 0.8rem 2rem;
        border-radius: 30px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 1rem;
      ">
        View More Recommendations <i class="fas fa-chevron-right" style="margin-left: 0.5rem;"></i>
      </button>
    </div>
  </main>

  <footer>
    <!-- Your existing footer content -->
    <div class="footer-content">
      <div class="footer-section">
        <h3>About BrandX</h3>
        <p>Your premier destination for the latest and greatest in footwear fashion. We offer premium sneakers with the best quality and style.</p>
      </div>
      <div class="footer-section">
        <h3>Quick Links</h3>
        <a href="#"><i class="fas fa-home"></i> Home</a>
        <a href="#"><i class="fas fa-shopping-bag"></i> Shop</a>
        <a href="#new-arrivals"><i class="fas fa-star"></i> New Arrivals</a>
        <a href="#"><i class="fas fa-tag"></i> Sale</a>
      </div>
      <div class="footer-section">
        <h3>Customer Service</h3>
        <a href="#"><i class="fas fa-envelope"></i> Contact Us</a>
        <a href="#"><i class="fas fa-truck"></i> Shipping Policy</a>
        <a href="#"><i class="fas fa-exchange-alt"></i> Returns & Exchanges</a>
        <a href="#"><i class="fas fa-question-circle"></i> FAQs</a>
      </div>
      <div class="footer-section">
        <h3>Connect With Us</h3>
        <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
        <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
        <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
        <a href="#"><i class="fab fa-tiktok"></i> TikTok</a>
      </div>
    </div>
    <div class="copyright">
      <p>&copy; 2023 BrandX. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
  <script>
    // Initialize Swiper
    const swiper = new Swiper('.swiper', {
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });

    // Mobile menu toggle
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');

    hamburger.addEventListener('click', () => {
      mobileNav.classList.toggle('active');
      hamburger.innerHTML = mobileNav.classList.contains('active') 
        ? '<i class="fas fa-times"></i>' 
        : '<i class="fas fa-bars"></i>';
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.mobile-nav a').forEach(link => {
      link.addEventListener('click', () => {
        mobileNav.classList.remove('active');
        hamburger.innerHTML = '<i class="fas fa-bars"></i>';
      });
    });

    // Modal functionality
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const loginButton = document.getElementById('loginButton');
    const mobileLoginButton = document.getElementById('mobileLoginButton');
    const closeLoginModal = document.getElementById('closeLoginModal');
    const closeRegisterModal = document.getElementById('closeRegisterModal');
    const showRegister = document.getElementById('showRegister');
    const showLogin = document.getElementById('showLogin');
    const searchModal = document.getElementById('searchModal');
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const mobileSearchButton = document.getElementById('mobileSearchButton');
    const searchResultsContainer = document.getElementById('searchResultsContainer');

    // Show login modal
    if (loginButton) loginButton.addEventListener('click', (e) => {
      e.preventDefault();
      loginModal.style.display = 'block';
    });

    if (mobileLoginButton) mobileLoginButton.addEventListener('click', (e) => {
      e.preventDefault();
      loginModal.style.display = 'block';
      mobileNav.classList.remove('active');
      hamburger.innerHTML = '<i class="fas fa-bars"></i>';
    });

    // Show register modal
    showRegister.addEventListener('click', (e) => {
      e.preventDefault();
      loginModal.style.display = 'none';
      registerModal.style.display = 'block';
    });

    // Show login modal from register
    showLogin.addEventListener('click', (e) => {
      e.preventDefault();
      registerModal.style.display = 'none';
      loginModal.style.display = 'block';
    });

    // Close modals
    closeLoginModal.addEventListener('click', () => {
      loginModal.style.display = 'none';
    });

    closeRegisterModal.addEventListener('click', () => {
      registerModal.style.display = 'none';
    });

    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
      if (e.target === loginModal) {
        loginModal.style.display = 'none';
      }
      if (e.target === registerModal) {
        registerModal.style.display = 'none';
      }
      if (e.target === searchModal) {
        searchModal.style.display = 'none';
      }
    });

    // Search functionality
    if (searchButton) searchButton.addEventListener('click', (e) => {
      e.preventDefault();
      searchModal.style.display = 'block';
      searchInput.focus();
    });

    if (mobileSearchButton) mobileSearchButton.addEventListener('click', (e) => {
      e.preventDefault();
      searchModal.style.display = 'block';
      searchInput.focus();
      mobileNav.classList.remove('active');
      hamburger.innerHTML = '<i class="fas fa-bars"></i>';
    });

    // Live search
    searchInput.addEventListener('input', async (e) => {
      const query = e.target.value.trim();
      
      if (query.length < 2) {
        searchResultsContainer.innerHTML = '';
        return;
      }
      
      try {
        const response = await fetch(`search.php?query=${encodeURIComponent(query)}`);
        const results = await response.json();
        
        if (results.length > 0) {
          let html = '<div class="search-results">';
          results.forEach(product => {
            html += `
              <a href="#" class="search-result-item">
                <img src="${product.image_url}" alt="${product.title}" class="search-result-image">
                <div class="search-result-info">
                  <div class="search-result-title">${product.title}</div>
                  <div class="search-result-price">$${product.price.toFixed(2)}</div>
                </div>
              </a>
            `;
          });
          html += '</div>';
          searchResultsContainer.innerHTML = html;
        } else {
          searchResultsContainer.innerHTML = '<p style="color: var(--text-secondary); padding: 1rem;">No results found</p>';
        }
      } catch (error) {
        console.error('Search error:', error);
        searchResultsContainer.innerHTML = '<p style="color: var(--text-secondary); padding: 1rem;">Error loading results</p>';
      }
    });

    // Handle add to cart when not logged in
    document.querySelectorAll('.cart-icon-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        <?php if (!$isLoggedIn): ?>
          e.preventDefault();
          loginModal.style.display = 'block';
          // Store product ID to add after login
          const productId = this.querySelector('input[name="product_id"]').value;
          document.getElementById('loginForm').insertAdjacentHTML('beforeend', 
            `<input type="hidden" name="product_to_add" value="${productId}">`);
        <?php endif; ?>
      });
    });

    // If there's a product to add after login
    <?php if (isset($productToAdd)): ?>
      document.addEventListener('DOMContentLoaded', () => {
        loginModal.style.display = 'block';
        document.getElementById('loginForm').insertAdjacentHTML('beforeend', 
          `<input type="hidden" name="product_to_add" value="<?= $productToAdd ?>">`);
      });
    <?php endif; ?>

    // Show success message if product was added to cart
    <?php if (isset($cartSuccess)): ?>
      document.addEventListener('DOMContentLoaded', () => {
        alert('<?= $cartSuccess ?>');
      });
    <?php endif; ?>
  </script>
</body>
</html>