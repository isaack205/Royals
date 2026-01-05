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
$isLoggedIn = isset($_SESSION['user_id']);

// Calculate cart count from session
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'] ?? 1;
}

// Get the search query from URL
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

// Include the header
include('header.php');
?>

<main>
    <style>
        /* Updated CSS to match index.php styling */
        .search-results-header {
            text-align: center;
            margin: 2rem 0;
            padding: 0 1rem;
        }
        
        .search-results-header h2 {
            font-size: 1.8rem;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        
        .search-query {
            color: var(--accent);
            font-weight: bold;
        }
        
        .results-count {
            font-size: 1rem;
            color: var(--text-secondary);
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

        .product-card {
            position: relative;
            cursor: pointer;
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

        .product-card a.product-link {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
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
            z-index: 2;
        }

        .product-card:hover .product-hover-actions {
            opacity: 1;
        }

        .quickview-btn {
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
        }

        .quickview-btn:hover {
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

        .wishlist-btn:hover {
            background-color: #ff4757;
            color: white;
            transform: scale(1.1);
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

    <div class="search-results-header">
        <h2>Search Results for <span class="search-query">"<?php echo htmlspecialchars($searchQuery); ?>"</span></h2>
        <?php
        // Query to fetch products matching the search term
        if (!empty($searchQuery)) {
            $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC";
            $stmt = $connection->prepare($sql);
            $searchTerm = '%' . $searchQuery . '%';
            $stmt->bind_param('ss', $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            $numResults = $result->num_rows;
            
            echo '<p class="results-count">' . $numResults . ' products found</p>';
        }
        ?>
    </div>

    <?php if (!empty($searchQuery) && $numResults > 0): ?>
        <div class="products-container">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="product-link"></a>
                    <div class="product-image">
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                        <div class="product-hover-actions">
                            <button class="quickview-btn" onclick="event.stopPropagation(); quickView(<?php echo $product['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="wishlist-btn" onclick="event.stopPropagation()">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <?php if ($product['rating']): ?>
                            <div class="product-rating">
                                <?php 
                                $fullStars = floor($product['rating']);
                                $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                                
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
                            <span class="current-price">Ksh <?php echo number_format($product['price_ksh'], 2); ?></span>
                        </div>
                    </div>
                    <button class="cart-icon" onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price_ksh']; ?>, '<?php echo $product['image']; ?>')">
                        <i class="fas fa-cart-plus"></i>
                    </button>
                </div>
            <?php endwhile; ?>
        </div>
    <?php elseif (!empty($searchQuery)): ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h3>No products found</h3>
            <p>We couldn't find any products matching your search for "<?php echo htmlspecialchars($searchQuery); ?>"</p>
            <a href="index.php" class="btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h3>Search for products</h3>
            <p>Enter a search term in the box above to find products</p>
            <a href="index.php" class="btn">Browse All Products</a>
        </div>
    <?php endif; ?>
</main>

<script>
// Quick view function
function quickView(productId) {
    // Implement your quick view functionality here
    console.log("Quick view for product ID: " + productId);
    // You might want to open a modal or redirect to a quick view page
}

// Add to cart function
function addToCart(productId, productName, productPrice, productImage) {
    // Prevent the default link behavior
    event.preventDefault();
    
    // Create an AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_to_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            // Update the cart count
            var response = JSON.parse(this.responseText);
            document.getElementById('cart-count').textContent = response.cartCount;
            
            // Show a notification
            alert(productName + " has been added to your cart!");
        }
    };
    
    // Send the request with the product data
    xhr.send("product_id=" + productId + 
             "&product_name=" + encodeURIComponent(productName) + 
             "&product_price=" + productPrice + 
             "&product_image=" + encodeURIComponent(productImage) + 
             "&quantity=1");
}
</script>

<?php
// Include the footer
include('footer.php');

// Close the database connection
$connection->close();
?>