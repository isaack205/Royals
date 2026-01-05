<?php
// Include the database connection
include('db.php');

// Start the session to use cart functionality
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']); // Check if 'user_id' session variable exists

// Include the authentication handler
include('auth.php');

// Check if the user is logged in via cookie
$user_data = checkAuth();

// Calculate cart count from session
$cartCount = count($_SESSION['cart']);

// Handle search functionality
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$sectionFilter = isset($_GET['section']) ? $_GET['section'] : null;
$brandFilter = isset($_GET['brand']) ? $_GET['brand'] : null;
$searchResults = false;

if (!empty($searchQuery)) {
    $searchSql = "SELECT * FROM products WHERE name LIKE '%" . $connection->real_escape_string($searchQuery) . "%' ORDER BY id DESC";
    $result = $connection->query($searchSql);
    $searchResults = true;
} elseif ($sectionFilter) {
    switch($sectionFilter) {
        case 'featured':
            $searchSql = "SELECT p.* FROM products p INNER JOIN featured_products fp ON p.id = fp.product_id ORDER BY p.id DESC";
            break;
        case 'new':
            $searchSql = "SELECT * FROM products ORDER BY id DESC";
            break;
        case 'recommended':
            $searchSql = "SELECT * FROM products ORDER BY RAND()";
            break;
        default:
            $searchSql = "SELECT * FROM products ORDER BY RAND()";
    }
    $result = $connection->query($searchSql);
    $searchResults = true;
} elseif ($brandFilter) {
    $brandSql = "SELECT * FROM products WHERE category = '" . $connection->real_escape_string($brandFilter) . "' ORDER BY id DESC";
    $result = $connection->query($brandSql);
    $searchResults = true;
} else {
    // Query to fetch all products from the database in random order
    $sql = "SELECT * FROM products ORDER BY RAND()";
    $result = $connection->query($sql);
}

// Fetch limited products for sections
$featuredSql = "SELECT p.id, p.name, p.price_ksh, p.image FROM products p 
                INNER JOIN featured_products fp ON p.id = fp.product_id
                ORDER BY p.id DESC LIMIT 10";
$featuredResult = $connection->query($featuredSql);

$newProductsSql = "SELECT * FROM products ORDER BY id DESC LIMIT 12";
$newProductsResult = $connection->query($newProductsSql);

$recommendedSql = "SELECT * FROM products ORDER BY RAND() LIMIT 84";
$recommendedResult = $connection->query($recommendedSql);

// Include the header
include('header.php');
?>

<main>
<style>
.product-card {
    position: relative;
    background-color: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    position: relative;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    transition: transform 0.3s;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-hover-actions {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    opacity: 0;
    transition: opacity 0.3s;
}

.product-card:hover .product-hover-actions {
    opacity: 1;
}


.quickview-button{
    border-radius: 50%;
     width: 40px;
    height: 40px;
    border: none;
     transition: all 0.3s;
     cursor: pointer;
}

.quickview-button:hover {
    background-color: var(--accent);
    color: white;
    transform: scale(1.1);
}
.quickview-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background-color: rgba(255,255,255,0.9);
    color: var(--text);
   
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
}

.quickview-button{
    border-radius: 50%;
     width: 40px;
    height: 40px;
    border: none;
     transition: all 0.3s;
     cursor: pointer;
}

.quickview-button:hover {
    background-color: var(--accent);
    color: white;
    transform: scale(1.1);
}

.wishlist-btn {
     width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background-color: rgba(255,255,255,0.9);
    color: black;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
}

.quickview-btn:hover {
    background-color: var(--accent);
    color: white;
    transform: scale(1.1);
}

.wishlist-btn:hover {
    background-color: #ff4757;
    color: white;
    transform: scale(1.1);
}

.product-details {
    padding: 1rem;
}

.product-title {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none !important;
    color: var(--text);
}

.price-container {
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.current-price {
    color: var(--accent);
    font-weight: bold;
    font-size: 1rem;
    text-decoration: none !important;
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
    z-index: 2;
}

.cart-icon:hover {
    background-color: var(--accent);
    color: white;
    transform: scale(1.1);
}




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
      border-radius: 5px;
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
      background-color: orange;
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
      color: orange;
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

    /* Further reduced screen margin on mobile */
@media (max-width: 600px) {
  main {
    padding: 1rem 0.25rem;
  }
}

/* Product grid with tighter spacing */
.products-container {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 0.5rem;
  margin: 0 0.25rem 1rem; /* top | horizontal | bottom */
}

/* Responsive adjustments */
@media (max-width: 1200px) {
  .products-container {
    grid-template-columns: repeat(4, 1fr);
    margin: 0 0.25rem 1rem;
  }
}

@media (max-width: 900px) {
  .products-container {
    grid-template-columns: repeat(3, 1fr);
    margin: 0 0.25rem 1rem;
  }
}

@media (max-width: 600px) {
  .products-container {
    grid-template-columns: repeat(2, 1fr);
    margin: 0 0.1rem 0.75rem; /* Even tighter on mobile */
  }
}

     /* New sections styling */
  .section-title {
    font-size: 1.5rem;
    margin: 1.5rem 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--accent);
    display: flex;
    align-items: center;
  }

  .section-title i {
    margin-right: 0.8rem;
    color: var(--accent);
  }

  /* Special Hot Deals styling */
  .hot-deals .product-card {
    border: 2px solid #ff4757;
    position: relative;
  }

  .hot-deals .hot-tag {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #ff4757;
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.8rem;
  }

  /* Scrollable brand/category columns */
  .scroll-column {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 400px;
    overflow-y: auto;
    padding-right: 0.5rem;
  }

  .brands-container {
    display: flex;
    overflow-x: auto;
    gap: 0.8rem;
    padding: 1rem 0.5rem;
    scrollbar-width: thin;
  }
  
  .brand-card {
    flex: 0 0 auto;
    width: 90px; /* Reduced size */
    height: 90px;
    background-color: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1); /* Added border */
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
    width: 40px; /* Smaller logo */
    height: 40px;
    object-fit: contain;
    margin-bottom: 0.3rem;
    padding: 0.2rem;
    border-radius: 5px;
  }
  
  .brand-name {
    text-align: center;
    font-size: 0.7rem; /* Smaller text */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
  }

  .columns-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
  }

  /* Custom scrollbar */
  .scroll-column::-webkit-scrollbar {
    width: 6px;
  }

  .scroll-column::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.05);
  }

  .scroll-column::-webkit-scrollbar-thumb {
    background: var(--accent);
    border-radius: 3px;
  }

  
  /* Slider styles */
  .slider-container {
    position: relative;
    width: 95%;
    margin: 20px auto;
    height: 200px;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  .slider {
    display: flex;
    transition: transform 0.5s ease;
    height: 100%;
  }

  .slide {
    min-width: 100%;
    position: relative;
  }

  .slide img, .slide video {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .slibuttonv {
    position: absolute;
    top: 50%;
    width: 100%;
    display: flex;
    justify-content: space-between;
    transform: translateY(-50%);
    padding: 0 20px;
  }

  .slibuttonv button {
    background: rgba(0,0,0,0.5);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;
  }

  .slibuttonv button:hover {
    background: rgba(0,0,0,0.7);
  }

  /* Section banner */
  .section-banner-container {
    width: 95%;
    margin: 20px auto;
  }

  

  .section-banner {
    position: relative;
    width: 100%;
    height: 150px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    background-size: cover;
    background-position: center;
    transition: transform 0.3s;
  }

  .section-banner:hover {
    transform: translateY(-3px);
  }

  .banner-content {
    position: absolute;
    bottom: 0;
    left: 0;
    padding: 20px;
    color: white;
    width: 100%;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
  }

  .banner-content h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
  }

  .banner-content p {
    margin: 5px 0 0;
    font-size: 0.9rem;
    opacity: 0.9;
  }

  /* No results styling */
  .no-results {
    text-align: center;
    padding: 50px 20px;
    color: #666;
  }

  .no-results i {
    font-size: 40px;
    margin-bottom: 15px;
    color: #ddd;
  }

  .no-results h3 {
    margin-bottom: 10px;
    color: #333;
  }

  .no-results p {
    margin-bottom: 20px;
  }

  .btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: var(--accent);
    color: white;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
  }

  .btn:hover {
    background-color: #00b8e6;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }
</style>
    <?php if ($searchResults || $sectionFilter || $brandFilter): ?>
        <!-- Filtered Products View -->
        <?php if ($brandFilter): ?>
            <div class="search-results-header">
                <h2>Products from <?php echo htmlspecialchars(ucfirst($brandFilter)); ?></h2>
            </div>
            <div class="section-banner-container">
                <div class="section-banner" style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('uploads/brands.jpeg')">
                    <div class="banner-content">
                        <h3>Premium Brands</h3>
                        <p>Shop by your favorite labels →</p>
                    </div>
                </div>
            </div>
        <?php elseif ($sectionFilter): ?>
            <div class="search-results-header">
                <h2><?php echo htmlspecialchars(ucfirst($sectionFilter)); ?> Products</h2>
            </div>
            <?php 
            $banners = [
                'featured' => [
                    'image' => 'uploads/best1.jpeg',
                    'title' => 'Premium Selection',
                    'text' => 'Discover our featured collection →'
                ],
                'new' => [
                    'image' => 'uploads/best.png',
                    'title' => 'Just Dropped',
                    'text' => 'Explore the latest arrivals →'
                ],
                'recommended' => [
                    'image' => 'uploads/recommended.jpeg',
                    'title' => 'Personal Picks',
                    'text' => 'Selected just for you →'
                ]
            ];
            
            if (isset($banners[$sectionFilter])): ?>
            <div class="section-banner-container">
                <div class="section-banner" style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('<?php echo htmlspecialchars($banners[$sectionFilter]['image']); ?>')">
                    <div class="banner-content">
                        <h3><?php echo htmlspecialchars($banners[$sectionFilter]['title']); ?></h3>
                        <p><?php echo htmlspecialchars($banners[$sectionFilter]['text']); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="search-results-header">
                <h2>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
            </div>
        <?php endif; ?>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="products-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" loading="lazy">
                            <div class="product-hover-actions">
                                <button class="quickview-btn" onclick="quickView(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="wishlist-btn">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-details">
                            <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <?php if ($row['rating']): ?>
                                <div class="product-rating">
                                    <?php 
                                    $fullStars = floor($row['rating']);
                                    $halfStar = ($row['rating'] - $fullStars) >= 0.5;
                                    
                                    for ($i = 0; $i < 5; $i++) {
                                        if ($i < $fullStars) {
                                            echo '<i class="fas fa-star"></i>';
                                        } elseif ($i == $fullStars && $halfStar) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            <div class="price-container">
                                <span class="current-price">Ksh <?php echo number_format($row['price_ksh'], 2); ?></span>
                            </div>
                        </div>
                        <button class="cart-icon" onclick="addToCart(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', <?php echo $row['price_ksh']; ?>, '<?php echo $row['image']; ?>')">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No products found</h3>
                <p>We couldn't find any products matching your search</p>
                <a href="home.php" class="btn">Continue Shopping</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Main Content (Default View) -->
        <div id="mainContent">
            <!-- Fixed Slider Section -->
            <div class="slider-container">
                <div class="slider">
                    <?php
                    $adsDirectory = 'ads/';
                    $defaultBanner = 'uploads/default-banner.jpg';
                    
                    // Check if directory exists
                    if (is_dir($adsDirectory)) {
                        $ads = array_diff(scandir($adsDirectory), ['.', '..']);
                        
                        if (count($ads) > 0) {
                            foreach ($ads as $ad) {
                                $filePath = $adsDirectory . $ad;
                                $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                
                                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    echo "<div class='slide'><img src='$filePath' alt='Ad Banner' loading='lazy'></div>";
                                } 
                                elseif (in_array($fileExt, ['mp4', 'webm', 'ogg'])) {
                                    echo "<div class='slide'>
                                            <video autoplay muted loop playsinline>
                                                <source src='$filePath' type='video/$fileExt'>
                                            </video>
                                          </div>";
                                }
                            }
                        } else {
                            // Fallback if no ads found
                            echo "<div class='slide'><img src='$defaultBanner' alt='Default Banner'></div>";
                        }
                    } else {
                        // Fallback if ads directory doesn't exist
                        echo "<div class='slide'><img src='$defaultBanner' alt='Default Banner'></div>";
                        echo "<div class='slide'><img src='uploads/banner2.jpg' alt='Fallback Banner'></div>";
                    }
                    ?>
                </div>
                <div class="slibuttonv">
                    <button class="prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <!-- Featured Products Section -->
            <h2 class="section-title">
                <i class="fas fa-star"></i> BEST FROM BRANDX
            </h2>
            <div class="section-banner-container">
                <div class="section-banner" onclick="filterBySection('featured')" style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('uploads/best1.jpeg')">
                    <div class="banner-content">
                        <h3>Premium Selection</h3>
                        <p>Discover our featured collection →</p>
                    </div>
                </div>
            </div>
            <div class="products-container">
                <?php if ($featuredResult && $featuredResult->num_rows > 0): ?>
                    <?php while ($featuredProduct = $featuredResult->fetch_assoc()): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="uploads/<?php echo htmlspecialchars($featuredProduct['image']); ?>" alt="<?php echo htmlspecialchars($featuredProduct['name']); ?>" loading="lazy">
                                <div class="product-hover-actions">
                                    <button class="quickview-button" onclick="quickView(<?php echo $featuredProduct['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="wishlist-btn">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-details">
                                <h3 class="product-title"><?php echo htmlspecialchars($featuredProduct['name']); ?></h3>
                                <div class="price-container">
                                    <span class="current-price">Ksh <?php echo number_format($featuredProduct['price_ksh'], 2); ?></span>
                                </div>
                            </div>
                            <button class="cart-icon" onclick="addToCart(<?php echo $featuredProduct['id']; ?>, '<?php echo addslashes($featuredProduct['name']); ?>', <?php echo $featuredProduct['price_ksh']; ?>, '<?php echo $featuredProduct['image']; ?>')">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-products">No featured products available at the moment.</p>
                <?php endif; ?>
            </div>

            <!-- New Arrivals Section -->
            <h2 class="section-title">
                <i class="fas fa-fire"></i> NEW ARRIVALS
            </h2>
            <div class="section-banner-container">
                <div class="section-banner" onclick="filterBySection('new')" style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('uploads/best.png')">
                    <div class="banner-content">
                        <h3>Just Dropped</h3>
                        <p>Explore the latest arrivals →</p>
                    </div>
                </div>
            </div>
            <div class="products-container">
                <?php if ($newProductsResult && $newProductsResult->num_rows > 0): ?>
                    <?php while ($newProduct = $newProductsResult->fetch_assoc()): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="uploads/<?php echo htmlspecialchars($newProduct['image']); ?>" alt="<?php echo htmlspecialchars($newProduct['name']); ?>" loading="lazy">
                                <div class="product-hover-actions">
                                    <button class="quickview-button" onclick="quickView(<?php echo $newProduct['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="wishlist-btn">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-details">
                                <h3 class="product-title"><?php echo htmlspecialchars($newProduct['name']); ?></h3>
                                <div class="price-container">
                                    <span class="current-price">Ksh <?php echo number_format($newProduct['price_ksh'], 2); ?></span>
                                </div>
                            </div>
                            <button class="cart-icon" onclick="addToCart(<?php echo $newProduct['id']; ?>, '<?php echo addslashes($newProduct['name']); ?>', <?php echo $newProduct['price_ksh']; ?>, '<?php echo $newProduct['image']; ?>')">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-products">No newly added products found.</p>
                <?php endif; ?>
            </div>

            <!-- Top Brands Section -->
            <h2 class="section-title">
                <i class="fas fa-tags"></i> TOP BRANDS
            </h2>
            <div class="section-banner-container">
                <div class="section-banner" onclick="showAllBrands()" style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('uploads/brands.jpeg')">
                    <div class="banner-content">
                        <h3>Premium Brands</h3>
                        <p>Shop by your favorite labels →</p>
                    </div>
                </div>
            </div>
            <div class="brands-container">
                <div class="brand-card" onclick="filterByBrand('nike')">
                    <img src="uploads/nike.png" alt="Nike" class="brand-logo">
                    <span class="brand-name">Nike</span>
                </div>
                <div class="brand-card" onclick="filterByBrand('adidas')">
                    <img src="uploads/adidas.png" alt="Adidas" class="brand-logo">
                    <span class="brand-name">Adidas</span>
                </div>
                <div class="brand-card" onclick="filterByBrand('jordan')">
                    <img src="uploads/jordan.png" alt="Jordan" class="brand-logo">
                    <span class="brand-name">Jordan</span>
                </div>
                <div class="brand-card" onclick="filterByBrand('puma')">
                    <img src="uploads/puma.png" alt="Puma" class="brand-logo">
                    <span class="brand-name">Puma</span>
                </div>
                <div class="brand-card" onclick="filterByBrand('newbalance')">
                    <img src="uploads/nb.png" alt="New Balance" class="brand-logo">
                    <span class="brand-name">New Balance</span>
                </div>
                <div class="brand-card" onclick="filterByBrand('vans')">
                    <img src="uploads/vans.png" alt="Vans" class="brand-logo">
                    <span class="brand-name">Vans</span>
                </div>
            </div>

            <!-- Recommended Products Section -->
            <h2 class="section-title">
                <i class="fas fa-thumbs-up"></i> RECOMMENDED FOR YOU
            </h2>
            <div class="section-banner-container">
                <div class="section-banner" onclick="filterBySection('recommended')" style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('uploads/recommended.jpeg')">
                    <div class="banner-content">
                        <h3>Personal Picks</h3>
                        <p>Selected just for you →</p>
                    </div>
                </div>
            </div>
            <div class="products-container">
                <?php if ($recommendedResult && $recommendedResult->num_rows > 0): ?>
                    <?php while ($recommendedProduct = $recommendedResult->fetch_assoc()): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="uploads/<?php echo htmlspecialchars($recommendedProduct['image']); ?>" alt="<?php echo htmlspecialchars($recommendedProduct['name']); ?>" loading="lazy">
                                <div class="product-hover-actions">
                                    <button class="quickview-button" onclick="quickView(<?php echo $recommendedProduct['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="wishlist-btn">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-details">
                                <h3 class="product-title"><?php echo htmlspecialchars($recommendedProduct['name']); ?></h3>
                                <?php if ($recommendedProduct['rating']): ?>
                                    <div class="product-rating">
                                        <?php 
                                        $fullStars = floor($recommendedProduct['rating']);
                                        $halfStar = ($recommendedProduct['rating'] - $fullStars) >= 0.5;
                                        
                                        for ($i = 0; $i < 5; $i++) {
                                            if ($i < $fullStars) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i == $fullStars && $halfStar) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <div class="price-container">
                                    <span class="current-price">Ksh <?php echo number_format($recommendedProduct['price_ksh'], 2); ?></span>
                                </div>
                            </div>
                            <button class="cart-icon" onclick="addToCart(<?php echo $recommendedProduct['id']; ?>, '<?php echo addslashes($recommendedProduct['name']); ?>', <?php echo $recommendedProduct['price_ksh']; ?>, '<?php echo $recommendedProduct['image']; ?>')">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-products">No recommended products found.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php
// Include the footer
include('footer.php');

// Close the database connection
$connection->close();
?>