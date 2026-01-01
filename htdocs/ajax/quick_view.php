<?php
include('../db.php');

$product_id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $product = $result->fetch_assoc()) {
    echo '<div class="quickview-content">';
    
    // Image Section
    echo '<div class="quickview-image-container">';
    echo '<div class="main-image-wrapper">';
    echo '<img src="../uploads/'.$product['image'].'" alt="'.htmlspecialchars($product['name']).'" class="quickview-main-image">';
    echo '</div>';
    echo '</div>';
    
    // Product Details Section
    echo '<div class="quickview-details">';
    echo '<h3 class="quickview-title">'.htmlspecialchars($product['name']).'</h3>';
    echo '<div class="quickview-price">Ksh '.number_format($product['price_ksh'], 2).'</div>';
    
    if ($product['rating']) {
        echo '<div class="product-rating">';
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
        echo '<span class="rating-text">('.$product['rating'].')</span>';
        echo '</div>';
    }
    
    // Product Specifications (replacing description)
    echo '<div class="product-specifications">';
    echo '<div class="spec-item">';
    echo '<span class="spec-label"><i class="fas fa-tag"></i> Brand:</span>';
    echo '<span class="spec-value">'.($product['category'] ?? 'BrandX').'</span>';
    echo '</div>';
    
    echo '<div class="spec-item">';
    echo '<span class="spec-label"><i class="fas fa-tshirt"></i> Category:</span>';
    echo '<span class="spec-value">'.($product['gender_category'] ?? 'Unisex').'</span>';
    echo '</div>';
    
    echo '<div class="spec-item">';
    echo '<span class="spec-label"><i class="fas fa-palette"></i> Color:</span>';
    echo '<span class="spec-value">'.($product['color'] ?? 'Various').'</span>';
    echo '</div>';
    
    echo '<div class="spec-item">';
    echo '<span class="spec-label"><i class="fas fa-ruler"></i> Size:</span>';
    echo '<span class="spec-value">'.($product['size'] ?? 'Multiple sizes available').'</span>';
    echo '</div>';
    
    echo '<div class="spec-item">';
    echo '<span class="spec-label"><i class="fas fa-box"></i> Availability:</span>';
    echo '<span class="spec-value available">In Stock</span>';
    echo '</div>';
    echo '</div>';
    
    // Quantity Selector
    echo '<div class="quantity-section">';
    echo '<div class="quantity-control">';
    echo '<span class="quantity-label">Quantity:</span>';
    echo '<div class="quantity-input-group">';
    echo '<button class="qty-decrease"><i class="fas fa-minus"></i></button>';
    echo '<input type="number" class="qty-input" value="1" min="1" max="10">';
    echo '<button class="qty-increase"><i class="fas fa-plus"></i></button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Action Buttons
    echo '<div class="quickview-actions">';
    echo '<button class="add-to-cart-btn" data-product-id="'.$product['id'].'" data-product-name="'.htmlspecialchars($product['name']).'" data-price="'.$product['price_ksh'].'" data-image="'.$product['image'].'">';
    echo '<i class="fas fa-cart-plus"></i> Add to Cart';
    echo '</button>';
    echo '<button class="wishlist-btn">';
    echo '<i class="far fa-heart"></i> Wishlist';
    echo '</button>';
    echo '</div>';
    
    // View Full Details Link
    echo '<div class="view-full-link">';
    echo '<a href="product.php?id='.$product['id'].'" class="full-details-link">';
    echo '<i class="fas fa-external-link-alt"></i> View Full Product Details';
    echo '</a>';
    echo '</div>';
    
    echo '</div>'; // End quickview-details
    echo '</div>'; // End quickview-content
}

$stmt->close();
?>
<style>
/* Quickview Content Styles */
.quickview-content {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    padding: 25px;
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
}

.quickview-image-container {
    flex: 1;
    min-width: 350px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.main-image-wrapper {
    width: 100%;
    height: 450px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0.05) 100%);
    border-radius: 12px;
    overflow: hidden;
    padding: 25px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.quickview-main-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.quickview-main-image:hover {
    transform: scale(1.02);
}

.quickview-details {
    flex: 1;
    min-width: 350px;
    padding: 10px 0;
}

.quickview-title {
    font-size: 2rem;
    margin-bottom: 15px;
    color: var(--text);
    font-weight: 700;
    line-height: 1.3;
    font-family: 'Montserrat', sans-serif;
}

.quickview-price {
    font-size: 2.2rem;
    color: var(--accent);
    font-weight: 800;
    margin-bottom: 25px;
    letter-spacing: 0.5px;
}

.product-rating {
    margin-bottom: 30px;
    color: #ffc107;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-rating i {
    margin-right: 1px;
}

.rating-text {
    font-size: 1.1rem;
    color: var(--text-secondary);
    margin-left: 8px;
    font-weight: 500;
}

/* Product Specifications */
.product-specifications {
    margin: 30px 0;
    padding: 25px;
    background: rgba(0, 210, 255, 0.03);
    border-radius: 12px;
    border: 1px solid var(--border-color);
    box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
}

.spec-item {
    display: flex;
    align-items: center;
    margin-bottom: 18px;
    padding-bottom: 18px;
    border-bottom: 1px dashed var(--border-color);
    transition: all 0.3s ease;
}

.spec-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.spec-item:hover {
    transform: translateX(5px);
}

.spec-label {
    font-weight: 600;
    color: var(--text);
    min-width: 140px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.spec-label i {
    color: var(--accent);
    width: 20px;
    text-align: center;
}

.spec-value {
    color: var(--text-secondary);
    flex: 1;
    font-size: 1rem;
    font-weight: 500;
}

.spec-value.available {
    color: #2ecc71;
    font-weight: 700;
    background: rgba(46, 204, 113, 0.1);
    padding: 6px 15px;
    border-radius: 6px;
    display: inline-block;
}

/* Quantity Section */
.quantity-section {
    margin: 30px 0;
    padding: 25px;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.quantity-control {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}

.quantity-label {
    font-weight: 600;
    color: var(--text);
    font-size: 1.1rem;
    min-width: 100px;
}

.quantity-input-group {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--card-bg);
    border-radius: 10px;
    padding: 5px;
    border: 1px solid var(--border-color);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.qty-decrease,
.qty-increase {
    width: 45px;
    height: 45px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--background);
    color: var(--text);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.3s ease;
}

.qty-decrease:hover,
.qty-increase:hover {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
    transform: scale(1.05);
}

.qty-input {
    width: 70px;
    height: 45px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: var(--text);
    text-align: center;
    font-size: 18px;
    font-weight: 700;
    outline: none;
}

.qty-input:focus {
    outline: none;
    background: rgba(0, 210, 255, 0.05);
}

/* Action Buttons */
.quickview-actions {
    display: flex;
    gap: 20px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.add-to-cart-btn {
    padding: 20px 35px;
    background: linear-gradient(135deg, var(--accent), #3a7bd5);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.2rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    box-shadow: 0 6px 20px rgba(0, 210, 255, 0.3);
    letter-spacing: 0.5px;
}

.add-to-cart-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0, 210, 255, 0.4);
    background: linear-gradient(135deg, #00c5ef, #2f6ac1);
}

.wishlist-btn {
    padding: 20px 35px;
    background: var(--border-color);
    color: var(--text);
    border: 2px solid transparent;
    border-radius: 12px;
    font-size: 1.2rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.wishlist-btn:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: var(--accent);
    color: var(--accent);
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* View Full Details Link */
.view-full-link {
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid var(--border-color);
    text-align: center;
}

.full-details-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
    padding: 12px 25px;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: rgba(0, 210, 255, 0.08);
}

.full-details-link:hover {
    background: rgba(0, 210, 255, 0.15);
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 210, 255, 0.2);
}

.full-details-link i {
    transition: transform 0.3s ease;
}

.full-details-link:hover i {
    transform: translateX(3px);
}

/* Responsive Styles */
@media (max-width: 992px) {
    .quickview-content {
        gap: 30px;
        padding: 20px;
    }
    
    .quickview-image-container,
    .quickview-details {
        min-width: 300px;
    }
    
    .main-image-wrapper {
        height: 400px;
    }
}

@media (max-width: 768px) {
    .quickview-content {
        flex-direction: column;
        gap: 25px;
        padding: 15px;
    }
    
    .quickview-image-container,
    .quickview-details {
        min-width: 100%;
    }
    
    .main-image-wrapper {
        height: 350px;
        padding: 20px;
    }
    
    .quickview-title {
        font-size: 1.7rem;
    }
    
    .quickview-price {
        font-size: 1.9rem;
    }
    
    .quickview-actions {
        flex-direction: column;
    }
    
    .add-to-cart-btn,
    .wishlist-btn {
        width: 100%;
        min-width: auto;
        padding: 18px 25px;
    }
    
    .product-specifications,
    .quantity-section {
        padding: 20px;
    }
    
    .spec-label {
        min-width: 120px;
    }
}

@media (max-width: 480px) {
    .main-image-wrapper {
        height: 280px;
    }
    
    .quickview-title {
        font-size: 1.5rem;
    }
    
    .quickview-price {
        font-size: 1.7rem;
    }
    
    .quantity-control {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .quantity-input-group {
        width: 100%;
        justify-content: center;
    }
}
</style>
<script>
// Initialize quantity controls
document.addEventListener('DOMContentLoaded', function() {
    const quickviewContent = document.getElementById('quickviewContent');
    
    if (!quickviewContent) return;
    
    // Quantity controls
    const decreaseBtn = quickviewContent.querySelector('.qty-decrease');
    const increaseBtn = quickviewContent.querySelector('.qty-increase');
    const qtyInput = quickviewContent.querySelector('.qty-input');
    
    if (decreaseBtn && qtyInput) {
        decreaseBtn.addEventListener('click', () => {
            let value = parseInt(qtyInput.value) || 1;
            if (value > 1) {
                qtyInput.value = value - 1;
            }
        });
    }
    
    if (increaseBtn && qtyInput) {
        increaseBtn.addEventListener('click', () => {
            let value = parseInt(qtyInput.value) || 1;
            if (value < 10) {
                qtyInput.value = value + 1;
            }
        });
    }
    
    // Validate input
    if (qtyInput) {
        qtyInput.addEventListener('change', function() {
            let value = parseInt(this.value) || 1;
            if (value < 1) this.value = 1;
            if (value > 10) this.value = 10;
        });
    }
    
    // Add to cart button with quantity
    const addToCartBtn = quickviewContent.querySelector('.add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const price = parseFloat(this.dataset.price) || 0;
            const image = this.dataset.image;
            const quantity = parseInt(qtyInput?.value) || 1;
            
            // Call addToCart multiple times for the quantity
            for (let i = 0; i < quantity; i++) {
                addToCart(productId, productName, price, image);
            }
            
            // Show notification with quantity
            showNotification(`${quantity} × ${productName} added to cart!`, `uploads/${image}`);
            
            // Close quickview
            const quickviewModal = document.getElementById('quickviewModal');
            if (quickviewModal) {
                quickviewModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
    
    // Wishlist button
    const wishlistBtn = quickviewContent.querySelector('.wishlist-btn');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            const productName = quickviewContent.querySelector('.quickview-title')?.textContent || '';
            showNotification(`${productName} added to wishlist!`, '', 'success');
        });
    }
});
</script>