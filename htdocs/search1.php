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
        
        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
            margin: 0 auto;
            max-width: 1400px;
        }
        
        .product-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .product-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-details {
            padding: 1rem;
        }
        
        .product-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .product-price {
            color: var(--accent);
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .no-results {
            text-align: center;
            padding: 3rem 1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .no-results i {
            font-size: 3rem;
            color: var(--border-color);
            margin-bottom: 1rem;
        }
        
        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text);
        }
        
        .no-results p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--accent);
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background-color: #00b8e6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,210,255,0.3);
        }
        
        @media (max-width: 768px) {
            .products-container {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 1rem;
            }
            
            .product-image {
                height: 160px;
            }
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
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">Ksh <?php echo number_format($product['price_ksh'], 2); ?></div>
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

<?php
// Include the footer
include('footer.php');

// Close the database connection
$connection->close();
?>