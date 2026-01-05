<?php
// Start the session
session_start();

// Include database connection
include('db.php');

// Check if product slug or id is set
if (!isset($_GET['slug'])) {
    if (isset($_GET['id'])) {
        // Fetch product by ID to get the slug, then redirect to slug URL
        $id = (int)$_GET['id'];
        $stmt = $connection->prepare("SELECT slug FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            header("Location: product.php?slug=" . urlencode($product['slug']));
            exit();
        }
    }
    header("Location: index.php");
    exit();
}

$product_slug = trim($_GET['slug']);

// Fetch product details using slug
$product_query = "SELECT * FROM products WHERE slug = ?";
$stmt = $connection->prepare($product_query);
$stmt->bind_param("s", $product_slug);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$product = $product_result->fetch_assoc();
$product_id = $product['id'];

// Determine stock status
$stock_status = 'in_stock';
$stock_badge = '';

if ($product['units_available'] <= 0) {
    $stock_status = 'out_of_stock';
    $stock_badge = '<span class="stock-status stock-out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>';
} elseif ($product['units_available'] <= 10) {
    $stock_status = 'low_stock';
    $stock_badge = '<span class="stock-status stock-low-stock"><i class="fas fa-exclamation-triangle"></i> Low Stock: ' . $product['units_available'] . ' left</span>';
} else {
    $stock_badge = '<span class="stock-status stock-in-stock"><i class="fas fa-check-circle"></i> In Stock</span>';
}

// Process colors and sizes
$colors = array_filter(explode(',', $product['available_colors']));
$sizes = array_filter(explode(',', $product['available_sizes']));

// Process secondary images
$secondary_images = json_decode($product['secondary_image'] ?? '[]', true) ?: [];

// Fetch related products (same category)
$related_query = "SELECT * FROM products WHERE category = ? AND id != ? ORDER BY RAND() LIMIT 6";
$stmt = $connection->prepare($related_query);
$stmt->bind_param("si", $product['category'], $product_id);
$stmt->execute();
$related_result = $stmt->get_result();

// Include header
include('header.php');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en" data-theme="system">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - BrandX</title>
    <meta name="description" content="<?php echo htmlspecialchars($product['description']); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($product['name']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($product['description']); ?>">
    <meta property="og:image" content="https://brandx.co.ke/uploads/<?php echo htmlspecialchars($product['image']); ?>">
    <meta property="og:url" content="https://brandx.co.ke/product.php?slug=<?php echo htmlspecialchars($product['slug']); ?>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --product-accent: #00d2ff;
            --product-danger: #ff4757;
            --product-success: #2ed573;
            --product-warning: #ffa502;
            --product-light: #f8f9fa;
            --product-dark: #212529;
            --product-shadow: 0 15px 30px rgba(0,0,0,0.1);
            --transition-speed: 0.3s;
        }
html, body {
    width: 100%;
    overflow-x: hidden;
     font-family: 'Exo', sans-serif;
    position: relative;
}

/* Add this to ensure images scale properly */
img {
    max-width: 100%;
    height: auto;
}

/* For your main containers */
.main-product-container, 
.product-layout, 
.product-grid {
    width: 100%;
    box-sizing: border-box;
}

/* Specifically for your product gallery */
.product-gallery {
    width: 100%;
    max-width: 500px; /* Adjust as needed */
    margin: 0 auto;
}

/* For your meta sections */
.meta-section {
    width: 100%;
    max-width: 415px; /* Instead of fixed width */
    margin: 0 auto;
}

        /* Base Styles */
        body {
            background-color: var(--background);
            color: var(--text);
            font-family: 'Exo', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Header spacing fix for product page */
        body {
            padding-top: 70px; /* Default padding */
        }

        @media (max-width: 768px) {
            body {
                padding-top: 120px; /* Increased padding for mobile */
            }
            .meta-section {
    width: 100%;
    max-width: 380px; /* Instead of fixed width */
    margin: 0 auto;
}
        }

        /* Main Container */
        .main-product-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Product Layout */
        .product-layout {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .product-grid {
            display: flex;
            gap: 3rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        /* Product Gallery */
        .product-gallery {
            width: 500px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .main-image-container {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: var(--product-shadow);
            aspect-ratio: 1/1;
            background-color: var(--card-bg);
        }

        .main-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-speed) ease;
        }

        .thumbnail-container {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding: 0.5rem 0;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            border: 2px solid transparent;
            background-color: var(--card-bg);
        }

        .thumbnail:hover, .thumbnail.active {
            border-color: var(--product-accent);
            transform: scale(1.05);
        }

        /* Product Details */
        .product-details {
            flex-grow: 1;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--product-shadow);
            display: flex;
            flex-direction: column;
        }

        .product-header {
            position: relative;
        }

        .product-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        .details-toggle {
            background: none;
            border: none;
            color: var(--product-accent);
            cursor: pointer;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0;
        }

        .details-toggle i {
            transition: transform 0.3s ease;
        }

        .details-toggle.active i {
            transform: rotate(180deg);
        }

        .product-category {
            display: inline-block;
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--product-accent);
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--product-accent);
            margin-bottom: 1rem;
        }

        .product-description {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .product-actions.hidden {
            display: none;
        }

        /* Custom Selectors */
        .custom-select {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .select-header {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            
            border: 1px solid var(--border-color);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .select-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            display: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .select-option {
            padding: 0.8rem 1rem;
            cursor: pointer;
            transition: background-color var(--transition-speed);
        }

        .select-option:hover {
            background-color: rgba(0, 210, 255, 0.1);
        }

        .select-option.active {
            background-color: var(--product-accent);
            color: white;
        }

        .select-open .select-options {
            display: block;
        }

        /* Quantity Control */
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background-color: var(--product-accent);
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(0,210,255,0.5);
        }

        .quantity-value {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            flex: 1;
            min-width: 200px;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--product-accent), #3a7bd5);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,210,255,0.3);
        }

        .btn-secondary {
            background-color: transparent;
            border: 2px solid var(--product-accent);
            color: var(--product-accent);
        }

        .btn-secondary:hover {
            background-color: rgba(0, 210, 255, 0.1);
            transform: translateY(-3px);
        }

        .btn-whatsapp {
            background-color: #25D366;
            color: white;
            border: none;
        }

        .btn-whatsapp:hover {
            background-color: #128C7E;
            transform: translateY(-3px);
        }

        /* Product Meta Information */
        .product-meta-container {
            display: flex;
            flex-direction: row;
            gap: 2rem;
            font-size: 13px;
            width: 100%;
        }

        .meta-section {
            border: 2px solid var(--product-accent);border: 2px solid rgba(0, 210, 255, 0.3);
            border-radius: 12px;
            width: 415px;
            padding: 2rem;
            background-color: transparent;
            box-shadow: none;
        }

        .meta-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .meta-content {
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* Social Share */
        .social-share {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            align-items: center;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all var(--transition-speed) ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
        }

        .facebook { background-color: #3b5998; }
        .twitter { background-color: #1da1f2; }
        .instagram { background-color: #e1306c; }
        .whatsapp { background-color: #25D366; }

        /* Favorite Button */
        .favorite-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: var(--text-secondary);
            transition: all var(--transition-speed) ease;
            padding: 0.5rem;
            position: absolute;
            top: 0;
            right: 0;
        }

        .favorite-btn:hover {
            color: var(--product-danger);
        }

        .favorite-btn.active {
            color: var(--product-danger);
        }

        /* Custom Notification */
        .custom-notification {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: var(--card-bg);
            color: var(--text);
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 10000;
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            max-width: 90%;
            pointer-events: none;
        }

        .custom-notification.active {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
            pointer-events: auto;
        }

        .custom-notification.success {
            border-left: 4px solid var(--product-success);
        }

        .custom-notification.error {
            border-left: 4px solid var(--product-danger);
        }

        .custom-notification.warning {
            border-left: 4px solid var(--product-warning);
        }

        .custom-notification i {
            font-size: 1.5rem;
        }

        .custom-notification.success i {
            color: var(--product-success);
        }

        .custom-notification.error i {
            color: var(--product-danger);
        }

        .custom-notification.warning i {
            color: var(--product-warning);
        }

        .custom-notification .notification-content {
            display: flex;
            flex-direction: column;
        }

        .custom-notification .notification-message {
            font-weight: 500;
        }

        .custom-notification .notification-action {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 3px;
        }

        /* Stock Status Styles */
        .stock-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .stock-in-stock {
            background-color: rgba(46, 213, 115, 0.1);
            color: var(--product-success);
            border: 1px solid var(--product-success);
        }

        .stock-low-stock {
            background-color: rgba(255, 165, 2, 0.1);
            color: var(--product-warning);
            border: 1px solid var(--product-warning);
        }

        .stock-out-of-stock {
            background-color: rgba(255, 71, 87, 0.1);
            color: var(--product-danger);
            border: 1px solid var(--product-danger);
        }

        /* Disabled button styles */
        .btn-disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn-disabled:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .product-gallery {
                width: 400px;
            }
        }

        @media (max-width: 768px) {
            .main-product-container {
                padding: 0 1rem;
            }
            /* Product Meta Information */
        .product-meta-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            font-size: 13px;
            width: 100%;
        }
            .product-grid {
                flex-direction: column;
                gap: 2rem;
            }
            
            .product-gallery {
                width: 100%;
                max-width: 500px;
                margin: 0 auto;
            }
            
            .product-title {
                font-size: 1.5rem;
            }
            
            .product-price {
                font-size: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: row;
                flex-wrap: wrap;
            }
            
            .btn {
                min-width: 100%;
            }
            
            .thumbnail {
                width: 60px;
                height: 60px;
            }
        }

        @media (max-width: 600px) {
            .product-details {
                padding: 1.5rem;
            }
            
            .meta-section {
                padding: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .product-title {
                font-size: 1.3rem;
            }
            
            .product-price {
                font-size: 1.3rem;
            }
            
            .btn {
                padding: 0.8rem 1rem;
            }
        }

        .related-title {
            font-size: 1.5rem;
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent);
            display: flex;
            align-items: center;
        }

        .related-title i {
            margin-right: 0.8rem;
            color: var(--accent);
        }

        .related-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.5rem;
            margin: 0 0.25rem 1rem;
        }

        /* Responsive adjustments for related products */
        @media (max-width: 1200px) {
            .related-container {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 900px) {
            .related-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 600px) {
            .related-container {
                grid-template-columns: repeat(2, 1fr);
                margin: 0 0.1rem 0.75rem;
            }
        }

        /* Product card styles to match index page */
        .related-container .product-card {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .related-container .product-card:hover {
            transform: translateY(-5px);
        }

        .related-container .product-image {
            position: relative;
        }

        .related-container .product-image img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .related-container .product-details {
            padding: 1rem;
            position: relative;
        }

        .related-container .product-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .related-container .price-container {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .related-container .current-price {
            color: var(--accent);
            font-weight: bold;
            font-size: 1rem;
        }

        .related-container .product-hover-actions {
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

        .related-container .product-card:hover .product-hover-actions {
            opacity: 1;
        }

        .related-container .quickview-btn {
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

        .related-container .quickview-btn:hover {
            background-color: var(--accent);
            color: white;
            transform: scale(1.1);
        }

        .related-container .wishlist-btn {
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

        .related-container .wishlist-btn:hover {
            background-color: #ff4757;
            color: white;
            transform: scale(1.1);
        }

        .related-container .cart-icon {
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

        .related-container .cart-icon:hover {
            background-color: var(--accent);
            color: white;
            transform: scale(1.1);
        }

        .related-container .product-link {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Prevent quickview and cart buttons from triggering the product link */
        .related-container .product-hover-actions button {
            pointer-events: auto;
        }
    </style>
</head>
<body>
    <!-- Custom Notification Element -->
    <div class="custom-notification" id="customNotification">
        <i class="fas fa-check-circle"></i>
        <div class="notification-content">
            <span class="notification-message"></span>
            <span class="notification-action"></span>
        </div>
    </div>

    <div class="main-product-container">
        <div class="product-layout">
            <div class="product-grid">
                <!-- Product Gallery -->
                <div class="product-gallery">
                    <div class="main-image-container">
                        <img id="mainImage" src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-image">
                    </div>
                    <div class="thumbnail-container">
                        <!-- Main image as first thumbnail -->
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="thumbnail active" onclick="changeImage(this, 'uploads/<?php echo htmlspecialchars($product['image']); ?>')">
                        
                        <!-- Secondary images -->
                        <?php foreach ($secondary_images as $image): ?>
                            <?php if (!empty($image)): ?>
                                <img src="uploads/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="thumbnail" onclick="changeImage(this, 'uploads/<?php echo htmlspecialchars($image); ?>')">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="product-details">
                    <div class="product-header">
                        <div style="position: relative;">
                            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                            <button class="details-toggle" id="detailsToggle" onclick="toggleDetails()">
                                <span>See Details</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <button class="favorite-btn" id="favoriteBtn" onclick="toggleFavorite()">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                        <span class="product-category"><?php echo htmlspecialchars($product['category'] ?: 'Premium'); ?></span>
                        <div class="product-price">Ksh <?php echo number_format($product['price_ksh'], 2); ?></div>
                        <?php echo $stock_badge; ?>
                    </div>
                    
                    <div id="productDescription" style="display: none;">
                        <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    
                    <div class="product-actions" id="productActions">
                        <!-- Color Selection -->
                        <?php if (!empty($colors)): ?>
                        <div class="custom-select" id="colorSelect">
                            <div class="select-header" onclick="toggleSelect('colorSelect')">
                                <span id="colorSelectValue">Select Color</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="select-options">
                                <?php foreach ($colors as $color): ?>
                                    <div class="select-option" onclick="selectOption(this, 'colorSelect', '<?php echo htmlspecialchars(trim($color)); ?>')">
                                        <?php echo htmlspecialchars(trim($color)); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Size Selection -->
                        <?php if (!empty($sizes)): ?>
                        <div class="custom-select" id="sizeSelect">
                            <div class="select-header" onclick="toggleSelect('sizeSelect')">
                                <span id="sizeSelectValue">Select Size</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="select-options">
                                <?php foreach ($sizes as $size): ?>
                                    <div class="select-option" onclick="selectOption(this, 'sizeSelect', '<?php echo htmlspecialchars(trim($size)); ?>')">
                                        <?php echo htmlspecialchars(trim($size)); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Quantity Selection -->
                        <div class="quantity-control">
                            <span>Quantity:</span>
                            <button class="quantity-btn quantity-decrease">-</button>
                            <span class="quantity-value">1</span>
                            <button class="quantity-btn quantity-increase">+</button>
                        </div>
                    
                        <div class="action-buttons">
                            <button class="btn btn-primary <?php echo $stock_status === 'out_of_stock' ? 'btn-disabled' : ''; ?>" 
                                    onclick="addToCart()" 
                                    <?php echo $stock_status === 'out_of_stock' ? 'disabled' : ''; ?>>
                                <i class="fas fa-cart-plus"></i> 
                                <?php echo $stock_status === 'out_of_stock' ? 'Out of Stock' : 'Add to Cart'; ?>
                            </button>
                            <button class="btn btn-secondary <?php echo $stock_status === 'out_of_stock' ? 'btn-disabled' : ''; ?>" 
                                    onclick="orderViaWhatsApp()" 
                                    <?php echo $stock_status === 'out_of_stock' ? 'disabled' : ''; ?>>
                                <i class="fab fa-whatsapp"></i> 
                                <?php echo $stock_status === 'out_of_stock' ? 'Out of Stock' : 'Order via WhatsApp'; ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Social Share -->
                    <div class="action-row" style="margin-top: 2rem;">
                        <div class="social-share">
                            <span>Share:</span>
                            <a href="#" class="social-icon facebook" onclick="shareProduct('facebook')"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon twitter" onclick="shareProduct('twitter')"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon instagram" onclick="shareProduct('instagram')"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon whatsapp" onclick="shareProduct('whatsapp')"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            

                  <!-- Related Products Section -->
<?php if ($related_result->num_rows > 0): ?>
<style>
    /* Related Products Horizontal Scroller - Mobile Only */
    @media (max-width: 768px) {
        .related-scroller {
            position: relative;
            width: 100%;
            overflow-x: hidden;
            margin: 1rem 0 0; /* Reduced top margin */
            padding: 0;
        }
        
        .related-container {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            padding: 0 5px 1rem; /* Reduced padding */
            scrollbar-width: none;
            -ms-overflow-style: none;
            gap: 12px; /* Reduced gap between items */
        }
        
        .related-container::-webkit-scrollbar {
            display: none;
        }
        
        .related-product {
            flex: 0 0 auto;
            width: calc(50% - 5px); /* Adjusted width with smaller gap */
            scroll-snap-align: start;
        }
        
        /* First and last child adjustments */
        .related-product:first-child {
            margin-left: 5px; /* Reduced margin */
        }
        
        .related-product:last-child {
            margin-right: 5px; /* Reduced margin */
        }
        
        /* Product card adjustments */
        .related-product .product-card {
            width: 100%;
            margin: 0;
            border-radius: 8px; /* Slightly rounded corners */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow */
        }
        
        /* Auto-scroll animation */
        @keyframes scrollRelatedProducts {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-50%); }
            40% { transform: translateX(-100%); }
            60% { transform: translateX(-150%); }
            80% { transform: translateX(-200%); }
        }
    }
    
    /* Desktop view remains unchanged */
    @media (min-width: 769px) {
        .related-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.5rem;
            margin: 0 0.25rem 1rem;
        }
        
        @media (max-width: 1200px) {
            .related-container {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        @media (max-width: 900px) {
            .related-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    }
</style>

<div class="related-products">
    <h3 class="related-title"><i class="fas fa-thumbs-up"></i> You May Also Like</h3>
    <div class="related-scroller">
        <div class="related-container" id="relatedContainer">
            <?php 
            $related_result->data_seek(0);
            while ($related = $related_result->fetch_assoc()): 
            ?>
                <div class="related-product">
                    <div class="product-card">
                        <a href="product.php?slug=<?php echo $related['slug']; ?>" class="product-link"></a>
                        <div class="product-image">
                            <img src="uploads/<?php echo htmlspecialchars($related['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>" 
                                 loading="lazy">
                            <div class="product-hover-actions">
                                <button class="quickview-btn" onclick="event.stopPropagation(); quickView(<?php echo $related['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="wishlist-btn" onclick="event.stopPropagation(); toggleFavorite(<?php echo $related['id']; ?>)">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-details">
                            <h3 class="product-title"><?php echo htmlspecialchars($related['name']); ?></h3>
                            <div class="price-container">
                                <span class="current-price">Ksh <?php echo number_format($related['price_ksh'], 2); ?></span>
                            </div>
                        </div>
                        <button class="cart-icon" 
                                onclick="event.stopPropagation(); addToCart(<?php echo $related['id']; ?>, '<?php echo addslashes($related['name']); ?>', <?php echo $related['price_ksh']; ?>, '<?php echo $related['image']; ?>')">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
    // Initialize horizontal scroller for mobile related products
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth <= 768) {
            const container = document.getElementById('relatedContainer');
            const products = container.querySelectorAll('.related-product');
            const productWidth = products[0].offsetWidth + 5; // width + reduced gap
            
            let currentIndex = 0;
            let scrollInterval;
            
            function startAutoScroll() {
                scrollInterval = setInterval(() => {
                    currentIndex = (currentIndex + 1) % products.length;
                    container.scrollTo({
                        left: currentIndex * productWidth,
                        behavior: 'smooth'
                    });
                }, 5000);
            }
            
            // Start auto-scroll
            startAutoScroll();
            
            // Pause/resume on interaction
            container.addEventListener('mouseenter', () => clearInterval(scrollInterval));
            container.addEventListener('mouseleave', startAutoScroll);
            container.addEventListener('touchstart', () => clearInterval(scrollInterval));
            container.addEventListener('touchend', startAutoScroll);
            
            // Update index on manual scroll
            container.addEventListener('scroll', () => {
                currentIndex = Math.round(container.scrollLeft / productWidth);
            });
        }
    });
</script>
<?php endif; ?>
            
    <script>
        // Toggle product details view
        function toggleDetails() {
            const toggleBtn = document.getElementById('detailsToggle');
            const description = document.getElementById('productDescription');
            const actions = document.getElementById('productActions');
            
            if (description.style.display === 'none') {
                description.style.display = 'block';
                actions.classList.add('hidden');
                toggleBtn.innerHTML = '<span>Hide Details</span><i class="fas fa-chevron-up"></i>';
                toggleBtn.classList.add('active');
            } else {
                description.style.display = 'none';
                actions.classList.remove('hidden');
                toggleBtn.innerHTML = '<span>See Details</span><i class="fas fa-chevron-down"></i>';
                toggleBtn.classList.remove('active');
            }
        }

        // Change main image when thumbnail is clicked
        function changeImage(element, newSrc) {
            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            
            // Add active class to clicked thumbnail
            element.classList.add('active');
            
            // Change main image
            document.getElementById('mainImage').src = newSrc;
        }
        
        // Toggle select dropdown
        function toggleSelect(selectId) {
            document.getElementById(selectId).classList.toggle('select-open');
        }
        
        // Select option from dropdown
        function selectOption(element, selectId, value) {
            // Remove active class from all options in this select
            document.querySelectorAll(`#${selectId} .select-option`).forEach(option => {
                option.classList.remove('active');
            });
            
            // Add active class to clicked option
            element.classList.add('active');
            
            // Update select value
            document.getElementById(`${selectId}Value`).textContent = value;
            
            // Close dropdown
            document.getElementById(selectId).classList.remove('select-open');
        }
        
        // Close select dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-select')) {
                document.querySelectorAll('.custom-select').forEach(select => {
                    select.classList.remove('select-open');
                });
            }
        });
        
        // Quantity control
        document.addEventListener('DOMContentLoaded', function() {
            const decreaseBtn = document.querySelector('.quantity-decrease');
            const increaseBtn = document.querySelector('.quantity-increase');
            const quantityValue = document.querySelector('.quantity-value');
            
            decreaseBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityValue.textContent);
                if (currentValue > 1) {
                    quantityValue.textContent = currentValue - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityValue.textContent);
                quantityValue.textContent = currentValue + 1;
            });
        });
        
        // Show custom notification
        function showCustomNotification(message, actionText = '', type = 'success') {
            const customNotification = document.getElementById('customNotification');
            const notificationMessage = customNotification.querySelector('.notification-message');
            const notificationAction = customNotification.querySelector('.notification-action');
            
            customNotification.className = `custom-notification ${type}`;
            customNotification.querySelector('i').className = type === 'success' 
                ? 'fas fa-check-circle' 
                : type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle';
                
            notificationMessage.textContent = message;
            notificationAction.textContent = actionText;
            
            customNotification.classList.add('active');
            
            setTimeout(() => {
                customNotification.classList.remove('active');
            }, 3000);
        }
        
        // Add to cart function
        function addToCart() {
            // Check if product is out of stock
            <?php if ($stock_status === 'out_of_stock'): ?>
                showCustomNotification(
                    'This product is currently out of stock',
                    'Please check back later',
                    'error'
                );
                return;
            <?php endif; ?>
            
            const productId = <?php echo $product_id; ?>;
            const productName = "<?php echo addslashes($product['name']); ?>";
            const price = <?php echo $product['price_ksh']; ?>;
            const image = "<?php echo $product['image']; ?>";
            const quantity = parseInt(document.querySelector('.quantity-value').textContent);
            
            // Get selected color and size
            const selectedColor = document.querySelector('#colorSelect .select-option.active') ? 
                document.querySelector('#colorSelect .select-option.active').textContent : '';
            const selectedSize = document.querySelector('#sizeSelect .select-option.active') ? 
                document.querySelector('#sizeSelect .select-option.active').textContent : '';
            
            // Create FormData object
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('product_name', productName);
            formData.append('price', price);
            formData.append('image', image);
            formData.append('quantity', quantity);
            formData.append('color', selectedColor);
            formData.append('size', selectedSize);
            
            // Send AJAX request
            fetch('ajax/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomNotification(
                        'Product added to cart',
                        'Your cart has been updated',
                        'success'
                    );
                    // Update cart count in header
                    if (document.getElementById('cartCount')) {
                        document.getElementById('cartCount').textContent = data.cartCount;
                    }
                } else {
                    showCustomNotification(
                        data.message || 'Failed to add to cart',
                        'Please try again',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomNotification(
                    'Error adding to cart',
                    'Please try again',
                    'error'
                );
            });
        }
        
        // Toggle favorite UI
        function toggleFavoriteUI(isFavorite) {
            const favoriteBtn = document.getElementById('favoriteBtn');
            if (isFavorite) {
                favoriteBtn.innerHTML = '<i class="fas fa-heart"></i>';
                favoriteBtn.classList.add('active');
            } else {
                favoriteBtn.innerHTML = '<i class="far fa-heart"></i>';
                favoriteBtn.classList.remove('active');
            }
        }
        
        // Toggle favorite
        function toggleFavorite() {
            const favoriteBtn = document.getElementById('favoriteBtn');
            const isFavorite = favoriteBtn.classList.contains('active');
            
            <?php if ($isLoggedIn): ?>
                if (isFavorite) {
                    // Remove from favorites
                    removeFromWishlist();
                } else {
                    // Add to favorites
                    addToWishlist();
                }
            <?php else: ?>
                showCustomNotification(
                    'Please login to manage your wishlist',
                    'Redirecting to login page...',
                    'warning'
                );
                setTimeout(() => {
                    window.location.href = 'login.php?redirect=product.php?slug=<?php echo $product['slug']; ?>';
                }, 2000);
            <?php endif; ?>
        }
        
        // Add to wishlist function
        function addToWishlist() {
            const productId = <?php echo $product_id; ?>;
            
            fetch('ajax/add_to_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomNotification(
                        'Added to your wishlist',
                        'You can view it in your account',
                        'success'
                    );
                    toggleFavoriteUI(true);
                } else {
                    showCustomNotification(
                        data.message || 'Failed to update wishlist',
                        'Please try again',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomNotification(
                    'Error updating wishlist',
                    'Please try again',
                    'error'
                );
            });
        }
        
        // Remove from wishlist
        function removeFromWishlist() {
            const productId = <?php echo $product_id; ?>;
            
            fetch('ajax/remove_from_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomNotification(
                        'Removed from your wishlist',
                        '',
                        'success'
                    );
                    toggleFavoriteUI(false);
                } else {
                    showCustomNotification(
                        data.message || 'Failed to update wishlist',
                        'Please try again',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomNotification(
                    'Error updating wishlist',
                    'Please try again',
                    'error'
                );
            });
        }
        // Updated WhatsApp order function with proper phone number
        function orderViaWhatsApp() {
            // Check if product is out of stock
            <?php if ($stock_status === 'out_of_stock'): ?>
                showCustomNotification(
                    'This product is currently out of stock',
                    'Please check back later',
                    'error'
                );
                return;
            <?php endif; ?>
            
            const productName = "<?php echo addslashes($product['name']); ?>";
            const price = <?php echo $product['price_ksh']; ?>;
            const quantity = parseInt(document.querySelector('.quantity-value').textContent);
            const selectedColor = document.querySelector('#colorSelect .select-option.active') ? 
                document.querySelector('#colorSelect .select-option.active').textContent : 'Not specified';
            const selectedSize = document.querySelector('#sizeSelect .select-option.active') ? 
                document.querySelector('#sizeSelect .select-option.active').textContent : 'Not specified';
            
            // Use the SEO-friendly URL
            const productUrl = `https://brandx.co.ke/product.php?slug=<?php echo $product['slug']; ?>`;
            
            const message = `I want to order this product from BrandX:\n\n` +
                            `*${productName}*\n` +
                            `Price: Ksh ${price.toLocaleString()}\n` +
                            `Quantity: ${quantity}\n` +
                            `Color: ${selectedColor}\n` +
                            `Size: ${selectedSize}\n\n` +
                            `Product Link: ${productUrl}`;
            
            // Replace 2547XXXXXXXX with your actual WhatsApp business number (without +, 0, or spaces)
            const whatsappUrl = `https://wa.me/254795786918?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }
 // Share product
        function shareProduct(platform) {
            const productName = "<?php echo addslashes($product['name']); ?>";
            const productUrl = window.location.href;
            const imageUrl = "<?php echo 'uploads/' . $product['image']; ?>";
            let shareUrl = '';
            
            const message = `Check out this amazing product: ${productName} - ${productUrl}`;
            
            switch (platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(productUrl)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(message)}`;
                    break;
                case 'instagram':
                    // Instagram doesn't have a direct share URL, so we'll open in a new tab
                    alert('Please copy this link to share on Instagram: ' + productUrl);
                    return;
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
                    break;
                default:
                    return;
            }
            
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
        
        // Check if product is in wishlist on page load
        <?php if ($isLoggedIn): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const productId = <?php echo $product_id; ?>;
            
            fetch('ajax/check_wishlist.php?product_id=' + productId)
            .then(response => response.json())
            .then(data => {
                if (data.isInWishlist) {
                    toggleFavoriteUI(true);
                }
            })
            .catch(error => {
                console.error('Error checking wishlist:', error);
            });
        });
        <?php endif; ?>
        // [Remaining JavaScript functions stay the same]
    </script>
    
    <?php include('footer.php'); ?>
</body>
</html>
<?php
$connection->close();
?>