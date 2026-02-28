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
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;
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
} elseif ($categoryFilter) {
    $categorySql = "SELECT * FROM products WHERE gender_category = '" . $connection->real_escape_string($categoryFilter) . "' ORDER BY id DESC";
    $result = $connection->query($categorySql);
    $searchResults = true;
} else {
    // Query to fetch all products from the database in random order
    $sql = "SELECT * FROM products ORDER BY RAND()";
    $result = $connection->query($sql);
}

// Fetch limited products for sections
$newProductsSql = "SELECT * FROM products ORDER BY id DESC LIMIT 12";
$newProductsResult = $connection->query($newProductsSql);

// Include the header
include('header.php');
?>
<main>
<style>
/* Added CSS for clickable product cards */
/* Added CSS for category filters */

.mainContent {
    display: flex;
    padding-top: 70px;
}

@media (max-width: 486px) {
    .mainContent {
        padding-top: 0;
    }
}

.category-filters {
    
    display: flex;
    flex-wrap: nowrap; /* Force horizontal layout */
    overflow-x: auto;
    align-items: center;
    overflow-y: hidden;
    gap: 20px;
    margin: 20px 3px;
    padding: 0 20px;
    justify-content: center;
    white-space: nowrap;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE 10+ */
    width: 100%;
}

.category-filters::-webkit-scrollbar {
    display: none; /* Chrome, Safari */
}

.category-filter {
    flex: 0 0 auto; /* Prevent shrinking and maintain size */
    padding: 8px 15px;
    background-color: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    color: var(--text);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s;
    cursor: pointer;
}

.category-filter:hover, .category-filter.active {
    background-color: var(--accent);
    color: var(--primary);
    border-color: var(--accent);
}

.product-card {
    position: relative;
    cursor: pointer;
}

.product-card a.product-link {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.product-hover-actions button,
.cart-icon {
    position: relative;
    z-index: 2;
}

/* Prevent quickview and cart buttons from triggering the product link */
.product-hover-actions button {
    pointer-events: auto;
}

/* Existing CSS remains unchanged */
.product-card {
    background-color: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: transform 0.3s ease;
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
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
    white-space: wrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: var(--accent);
    font: sans-serif;
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
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
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
    background-color: rgba(14, 33, 65, 0.5);
    cursor: pointer;
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
    height: 300px;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Media query for small screens */
@media only screen and (max-width: 768px) {
    .slider-container {
        height: 200px; /* Reduced height for smaller screens */
        margin: 10px auto; /* Smaller margin */
        border-radius: 5px; /* Smaller border radius */
    }
}

/* Optional: Further adjustments for very small screens */
@media only screen and (max-width: 480px) {
    .slider-container {
        height: 150px;
        width: 98%; /* Slightly wider to use more screen space */
    }
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
    height: 450px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    background-size: cover;
    background-position: center;
    transition: transform 0.3s;
}

/* Media query for small screens */
@media only screen and (max-width: 768px) {
    .section-banner {
        width: 100%;
        height: 150px;
    }
}

/* Optional: Further adjustments for very small screens */
@media only screen and (max-width: 480px) {
    .section-banner {
        width: 100%;
        height: 150px;
    }
}

.section-banner:hover {
    transform: translateY(-3px);
}

.banner-content {
    position: absolute;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
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

/* Product hover actions */
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

/* Updated Quick View Button - Centered Perfectly */
.quickview-button {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background-color: rgba(255,255,255,0.9);
    color: var(--text);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.quickview-button:hover {
    background-color: var(--accent);
    color: white;
    transform: translate(-50%, -50%) scale(1.1);
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
                        <a href="product.php?id=<?php echo $row['id']; ?>" class="product-link"></a>
                        <div class="product-image">
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" loading="lazy">
                            <div class="product-hover-actions">
                                <button class="quickview-button" onclick="event.stopPropagation(); quickView(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-details">
                            <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                           
                            <div class="price-container">
                                <span class="current-price">Ksh <?php echo number_format($row['price_ksh'], 2); ?></span>
                            </div>
                        </div>
                        <button class="cart-icon" onclick="event.stopPropagation(); addToCart(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', <?php echo $row['price_ksh']; ?>, '<?php echo $row['image']; ?>')">
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
                <a href="index.php" class="btn">Continue Shopping</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Main Content (Default View) -->
        <div class="mainContent">
            <!-- Category Filters -->
            <div class="category-filters">
                <a href="?category=men" class="category-filter <?php echo ($categoryFilter == 'men') ? 'active' : ''; ?>">
                    <i class="fas fa-male"></i> Men
                </a>
                <a href="?category=women" class="category-filter <?php echo ($categoryFilter == 'women') ? 'active' : ''; ?>">
                    <i class="fas fa-female"></i> Women
                </a>
                <a href="?category=children" class="category-filter <?php echo ($categoryFilter == 'children') ? 'active' : ''; ?>">
                    <i class="fas fa-child"></i> Children
                </a>
                <a href="?category=unisex" class="category-filter <?php echo ($categoryFilter == 'unisex') ? 'active' : ''; ?>">
                    <i class="fas fa-tshirt"></i> Unisex
                </a>
                <a href="?category=casual" class="category-filter <?php echo ($categoryFilter == 'casual') ? 'active' : ''; ?>">
                    <i class="fas fa-tshirt"></i> Casual
                </a>
                <a href="?category=formal" class="category-filter <?php echo ($categoryFilter == 'formal') ? 'active' : ''; ?>">
                    <i class="fas fa-suitcase"></i> Formal
                </a>
                <a href="?category=sports" class="category-filter <?php echo ($categoryFilter == 'sports') ? 'active' : ''; ?>">
                    <i class="fas fa-running"></i> Sports
                </a>
                <!-- <a href="index.php" class="category-filter <?php echo (!$categoryFilter) ? 'active' : ''; ?>">
                    <i class="fas fa-times"></i> Clear Filters
                </a> -->
            </div>
        </div>            

        <!-- New Arrivals Section -->
        <div class="section-banner-container">
            <div class="section-banner" onclick="filterBySection('new')" style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('uploads/best.jpeg')">
                <div class="banner-content">
                    <h3>Just Dropped</h3>
                    <p>Explore the latest arrivals →</p>
                </div>
            </div>
        </div>

<script>
    // Initialize horizontal scroller for mobile
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth <= 768) {
            const container = document.getElementById('newArrivalsContainer');
            const products = container.querySelectorAll('.new-arrivals-product');
            const productWidth = products[0].offsetWidth + 10; // width + margin
            const totalProducts = products.length;
            let currentIndex = 0;
            
            // Auto-scroll functionality
            function autoScroll() {
                currentIndex = (currentIndex + 1) % totalProducts;
                container.scrollTo({
                    left: currentIndex * productWidth,
                    behavior: 'smooth'
                });
            }
            
            // Auto-scroll every 5 seconds
            const scrollInterval = setInterval(autoScroll, 5000);
            
            // Pause auto-scroll on hover
            container.addEventListener('mouseenter', () => {
                clearInterval(scrollInterval);
            });
            
            container.addEventListener('mouseleave', () => {
                scrollInterval = setInterval(autoScroll, 5000);
            });
            
            // Manual scroll detection
            let isScrolling;
            container.addEventListener('scroll', () => {
                clearTimeout(isScrolling);
                isScrolling = setTimeout(() => {
                    const scrollPosition = container.scrollLeft;
                    currentIndex = Math.round(scrollPosition / productWidth);
                }, 100);
            }, false);
        }
    });
    // Quickview functionality
function quickView(productId) {
    console.log('QuickView called for product ID:', productId);
    
    // Ensure the modal exists
    let quickviewModal = document.getElementById('quickviewModal');
    if (!quickviewModal) {
        console.log('Creating quickview modal...');
        quickviewModal = document.createElement('div');
        quickviewModal.id = 'quickviewModal';
        quickviewModal.className = 'modal-overlay';
        quickviewModal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 1100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        `;
        quickviewModal.innerHTML = `
            <div class="quickview-modal-content" style="
                background: var(--card-bg);
                width: 100%;
                max-width: 900px;
                max-height: 90vh;
                border-radius: 12px;
                overflow-y: auto;
                position: relative;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                animation: modalFadeIn 0.3s ease-out;
            ">
                <button class="quickview-close" id="quickviewClose" style="
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: rgba(0,0,0,0.5);
                    color: white;
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                    z-index: 10;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s;
                ">×</button>
                <div id="quickviewContent"></div>
            </div>
        `;
        document.body.appendChild(quickviewModal);
        
        // Add event listeners for the new modal
        document.getElementById('quickviewClose').onclick = () => {
            quickviewModal.style.display = 'none';
        };
        
        quickviewModal.addEventListener('click', (e) => {
            if (e.target === quickviewModal) {
                quickviewModal.style.display = 'none';
            }
        });
        
        // Close with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && quickviewModal.style.display === 'flex') {
                quickviewModal.style.display = 'none';
            }
        });
    }
    
    // Show loading state
    const quickviewContent = document.getElementById('quickviewContent');
    quickviewContent.innerHTML = '<div style="padding: 40px; text-align: center; color: var(--text-secondary)"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 15px;"></i><p>Loading product details...</p></div>';
    
    // Show the modal properly
    quickviewModal.style.display = 'flex';
    quickviewModal.style.position = 'fixed';
    quickviewModal.style.top = '0';
    quickviewModal.style.left = '0';
    quickviewModal.style.width = '100%';
    quickviewModal.style.height = '100%';
    quickviewModal.style.zIndex = '1100';
    
    // Fetch product data
    fetch(`ajax/quick_view.php?id=${productId}`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            console.log('Received data length:', data.length);
            
            if (!data || data.trim() === '') {
                throw new Error('Empty response from server');
            }
            
            quickviewContent.innerHTML = data;
            
            // Re-attach event listeners for buttons inside the quickview
            const addToCartBtn = quickviewContent.querySelector('.add-to-cart-btn');
            if (addToCartBtn) {
                // Remove existing onclick attribute to avoid conflicts
                addToCartBtn.removeAttribute('onclick');
                
                // Add new event listener
                addToCartBtn.addEventListener('click', function() {
                    // Extract product info
                    const productId = this.dataset.productId || productId;
                    const productName = this.dataset.productName || document.querySelector('.quickview-title')?.textContent || '';
                    const priceText = document.querySelector('.quickview-price')?.textContent || '';
                    const price = parseFloat(priceText.replace(/[^0-9.]/g, '')) || 0;
                    const image = this.dataset.image || '';
                    
                    if (productId && productName && price > 0) {
                        addToCart(productId, productName, price, image);
                        quickviewModal.style.display = 'none';
                    } else {
                        console.error('Could not extract product details for cart');
                        showNotification('Error adding to cart. Please try again.', '', 'error');
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading quick view:', error);
            quickviewContent.innerHTML = `
                <div style="padding: 40px; text-align: center; color: #ff4757;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <h3>Error Loading Product</h3>
                    <p>Failed to load product details. Please try again.</p>
                    <button onclick="quickView(${productId})" style="margin-top: 15px; padding: 10px 20px; background: var(--accent); color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Retry
                    </button>
                </div>
            `;
            showNotification('Error loading product details', '', 'error');
        });
}
</script>
            <div class="products-container">
                <?php 
                // Fetch all products
                $allProductsSql = "SELECT * FROM products ORDER BY id DESC";
                $allProductsResult = $connection->query($allProductsSql);
                
                if ($allProductsResult && $allProductsResult->num_rows > 0): 
                    while ($product = $allProductsResult->fetch_assoc()): 
                ?>
                    <div class="product-card">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="product-link"></a>
                        <div class="product-image">
                            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                            <div class="product-hover-actions">
                                <button class="quickview-button" onclick="event.stopPropagation(); quickView(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-details">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="price-container">
                                <span class="current-price">Ksh <?php echo number_format($product['price_ksh'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else: 
                ?>
                    <p class="no-products">No products found.</p>
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