<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header('Location: login.php');
    exit();
}
// Include the authentication handler
include('auth.php');

// Check if the user is logged in via cookie
$user_data = checkAuth();

// Initialize the cart if not already done
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id']) && isset($_POST['color']) && isset($_POST['size']) && isset($_POST['quantity'])) {
    $productId = $_GET['id'];
    $color = $_POST['color'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];
    $userId = $_SESSION['user_id']; // Use the logged-in user's ID

    // Check if the product already exists in the session cart for this user
    $productFound = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['product_id'] == $productId && $cartItem['color'] == $color && $cartItem['size'] == $size) {
            // Update quantity if product exists in session
            $cartItem['quantity'] += $quantity;
            $productFound = true;
            break;
        }
    }

    // If the product doesn't exist in session, add a new entry to session
    if (!$productFound) {
        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'color' => $color,
            'size' => $size,
            'quantity' => $quantity
        ];
    }
    

    // Now, update the database as well
    // Check if the product already exists in the database cart for this user
    $query = "SELECT * FROM cart WHERE user_id = $userId AND product_id = $productId AND product_color = '$color' AND product_size = '$size'";
    $result = $connection->query($query);
    
    if ($result && $result->num_rows > 0) {
        // Product exists, update quantity
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity; // Update quantity
        $updateQuery = "UPDATE cart SET quantity = $newQuantity WHERE id = {$row['id']}";
        $connection->query($updateQuery);
    } else {
        // Product doesn't exist in database, add new entry
        $insertQuery = "INSERT INTO cart (user_id, product_id, product_color, product_size, quantity, created_at) VALUES ($userId, $productId, '$color', '$size', $quantity, NOW())";
        $connection->query($insertQuery);
    }

    // Redirect back to cart page after adding item
    header('Location: cart.php');
    exit;
}
// Query to fetch all products from the database in random order
$sql = "SELECT * FROM products ORDER BY RAND()";  // Added ORDER BY RAND() to fetch products randomly
$result = $connection->query($sql);  // Execute the query
// Fetch the latest products based on the highest id values
$newProductsSql = "SELECT * FROM products ORDER BY RAND() DESC LIMIT 10";
$newProductsResult = $connection->query($newProductsSql);


// Remove item from cart
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $productId = $_GET['id'];
    $userId = $_SESSION['user_id']; // Use the logged-in user's ID

    // Remove item from the database
    $deleteQuery = "DELETE FROM cart WHERE user_id = $userId AND product_id = $productId";
    $connection->query($deleteQuery);

    // Also remove from session if the item exists there
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $productId) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
            break;
        }
    }

    // Redirect back to the cart page after removal
    header('Location: cart.php');
    exit;
}

// Display Cart
$totalPrice = 0;
$userId = $_SESSION['user_id']; // Use the logged-in user's ID

// Query the cart from the database for the specific user
$sql = "SELECT * FROM cart WHERE user_id = $userId";
$result = $connection->query($sql);

$cartItems = [];
if ($result && $result->num_rows > 0) {
    while ($cartItem = $result->fetch_assoc()) {
        $productId = $cartItem['product_id'];
        $size = $cartItem['product_size'];
        $color = $cartItem['product_color'];
        $quantity = $cartItem['quantity'];

        // Query to get the product details
        $sqlProduct = "SELECT * FROM products WHERE id = $productId";
        $productResult = $connection->query($sqlProduct);
        
        if ($productResult && $productResult->num_rows > 0) {
            $product = $productResult->fetch_assoc();
            $productPrice = $product['price_ksh'];
            $totalPrice += $productPrice * $quantity; // Multiply price by quantity

            $cartItems[] = [
                'product_id' => $productId,
                'image' => $product['image'],
                'name' => $product['name'],
                'description' => $product['description'],
                'size' => $size,
                'color' => $color,
                'quantity' => $quantity,
                'price' => $productPrice,
                'total' => $productPrice * $quantity
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand - Cart</title>
    <link rel="stylesheet" href="sty.css">
    <script src="script.js"></script>
    <style>
        /* General page styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0d1117; /* Dark background */
            color: #c9d1d9; /* Updated text color */
        }

        /* Header and Navigation */
header {
    background-color: #161b22; /* Updated background color */
    color: #c9d1d9; /* Updated text color */
    padding: 1rem; /* Padding adjusted */
}

header nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    text-align: center;
}

header nav ul li {
    display: inline;
    margin-right: 20px;
}

header nav ul li a {
    color: #c9d1d9; /* Updated link color */
    text-decoration: none;
    font-size: 18px;
}

/* Main Title and Description */
h1 {
    text-align: center;
    font-size: 36px;
    color: #4ea8de; /* Updated main heading color */
}

h2 {
    text-align: center;
    font-size: 24px;
    color: #ff6600; /* Orange color for subheading */
}

/* Container of cart items */
.cart-items {
    display: flex;
    flex-wrap: wrap; /* Allow items to wrap to the next row */
    gap: 20px; /* Adds space between each cart item */
    justify-content: flex-start; /* Align items to the left */
    margin: 0 auto;
}

/* Individual cart item */
.cart-item {
    display: flex; /* Enables flexbox for layout */
    align-items: center; /* Vertically align items */
    background-color: #161b22; /* Dark background for product */
    border: 1px solid #30363d; /* Subtle border */
    border-radius: 6px;
    overflow: hidden;
    padding: 1px;
    width: 100%; /* Ensure each item uses available width */
    max-width: 500px; /* Maximum width for cart item */
    text-align: left; /* Align text to the left */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease; /* Hover effect */
}


.cart-item img {
    width: 100px; /* Set a fixed width */
    height: 100px; /* Set a fixed height */
    border-radius: 5px;
    margin-right: 20px; /* Add space between image and text */
}

.cart-item .details {
    flex: 1; /* Allow details to take up the remaining space */
    display: flex;
    flex-direction: column; /* Stack text vertically */
    justify-content: center; /* Vertically center the text */
}

.cart-item p {
    margin: 5px 0;
    text-align: left; /* Align text to the left */
}

.cart-item a {
    color: #ff0000;
    text-decoration: none;
    margin-top: 10px;
}

.cart-item a:hover {
    text-decoration: underline;
}

        .cart-summary {
            margin-top: 20px;
            padding: 20px;
            background-color: #161b22; /* Dark background for summary */
            border: 1px solid #30363d; /* Subtle border */
            border-radius: 6px;
            text-align: center;
        }

        .total-price {
            text-align: center;
            font-size: 1.5em;
            margin-top: 30px;
        }

        .checkout-btn {
    display: block;
    width: 50%; /* Set width to 'auto' so the button takes only as much space as its content */
    padding: 15px 30px; /* Adjust the padding to make the button more compact */
    text-align: center;
    font-size: 1.2em;
    cursor: pointer;
    margin: 20px auto; /* Centers the button horizontally with margin auto */
    background-color: rgb(4, 201, 250);
    color: black;
    text-decoration: none;
    border-radius: 5px;
    transition: transform 0.3s ease; /* Smooth hover effect */
}

.checkout-btn:hover {
    border: 1px solid rgb(4, 201, 250);
    background-color: none;
    background: none;
    transform: scale(1.05); /* Slight zoom effect */
    color: rgb(4, 201, 250);
    text-decoration: none;
}


        .remove-button {
            display: inline-block;
            padding: 5px 10px;
            background:none;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            border:1px solid #c82333;
            margin-top: 10px;
        }

        .remove-button:hover {
            background-color:rgb(243, 5, 29);
            color:black;
            text-decoration:none;
        }
        
footer {

background-color: #161b22;

color: #c9d1d9;

padding: 20px 0;

text-align: center;

margin-top: 50px;

}

        

        .footer-icons {
  display: flex;
  justify-content: center; /* Center the icons closer to each other */
  align-items: center;
  gap: 50px; /* Adjust this gap to control the distance between the icons */
  width: 100%;
  max-width: 400px; /* Optional: adjust this width if necessary */
  margin: 0 auto;
  padding: 10px 0;
}

.footer-icons img {
  width: 50px; /* Adjust the size of the icons if needed */
  height: auto;
}

.footer {
  text-align: center;
  padding: 20px 0;
}

.footer p {
  margin: 0;
}

.footer a {
  color: #4ea8de;
  text-decoration: none;
}
.footer-icons-text p {
  text-align: center;
  font-size: 16px; /* Adjust font size */
  color: #4ea8de; /* Choose a suitable color */
  margin-bottom: 10px; /* Space between the text and icons */
}
                 /* Newly Added Products Row */
                 .new-products {
    display: flex;
    justify-content: flex-start;
    overflow-x: auto; /* Allow horizontal scrolling if too many items */
    gap: 20px; /* Space between products */
    margin-top: 30px;
    padding: 20px 10px;
    scroll-snap-type: x mandatory; /* Ensure smooth scroll behavior */
}

/* Product Card for Newly Added Products */
.new-product-card {
    flex: 0 0 calc(16.6667% - 15px); /* 6 products per row on large screens */
    background: #161b22;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    transition: transform 0.3s ease;
    max-width: 200px; /* Ensure fixed width */
    min-width: 200px; /* Ensure fixed width */
}

.new-product-card:hover {
    transform: translateY(-10px); /* Slight hover effect */
}

/* Product Image Styling */
.new-product-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    max-height: 150px;
}

/* Product Name Styling */
.new-product-card h3 {
    font-size: 15px;
    color: #4ea8de; /* Blue for title */
    margin: 10px 0;
    font-family: Times New Roman;
}

/* Price Styling */
.new-product-card .price {
    font-size: 0.9rem;
    color: white; /* Orange color for price emphasis */
}

/* Adjustments for Mobile */
@media (max-width: 768px) {
    .new-product-card {
        flex: 0 0 calc(30% - 15px); /* 3 products per row on tablets */
        max-width: calc(30% - 15px);
    }
}

@media (max-width: 480px) {
    .new-product-card {
        flex: 0 0 calc(50% - 15px); /* 2 products per row on mobile */
        max-width: calc(50% - 15px);
    }

    .new-product-card img {
        max-height: 200px; /* Adjust image height for smaller screens */
    }
}
.logo img,
.off-screen-menu .menu-logo img {
    background: transparent; /* Ensure transparency */
    display: block;
    margin: 0 auto; /* Center horizontally */
}

/* Center the Header Logo */
.navlogo {
    text-align: center; /* Center the container */
    padding: 10px 0;
}

.navlogo img {
    height: 70px; /* Adjust size as needed */
    width: auto;
}

/* Logo container */
.logo {
  text-align: center; /* Center the container */
  padding: 0; /* Remove padding */
  position: absolute; /* Position it at the top of the header */
  top: 0; /* Align it to the top */
  left: 50%; /* Center horizontally */
  transform: translateX(-50%); /* Adjust the position to truly center it */
}

/* Logo image styling */
.logo img {
  height: 70px; /* Adjust size as needed */
  width: auto; /* Maintain aspect ratio */
}


    </style>
</head>
<body>

<header>
<div class="ham-menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
             <!-- Logo in Header -->

             <div class="logo">

<a href="home.php">

    <img src="uploads/brandxlogo.png" alt="BrandX Logo">

</a>

</div>
        <div class="off-screen-menu" id="menu">
                 <!-- Logo in Header -->

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

<h1>Your Shopping Cart</h1>

<div class="cart-items">
    <?php
    if (!empty($cartItems)) {
        foreach ($cartItems as $item) {
            echo '<div class="cart-item">';
            echo '<img src="uploads/' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['name']) . '">';
            echo '<div>';
            echo '<h3>' . htmlspecialchars($item['name']) . '</h3>';
            echo '<p><strong>Quantity:</strong> ' . $item['quantity'] . '</p>';
            echo '<p><strong>Price per item:</strong> Ksh ' . number_format($item['price'], 2) . '</p>';
            echo '<p><strong>Total for this item:</strong> Ksh ' . number_format($item['total'], 2) . '</p>';
            echo '<a href="cart.php?action=remove&id=' . $item['product_id'] . '" class="remove-button">Remove</a>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>Your cart is empty.</p>';
    }
    ?>
</div>


<?php if (!empty($cartItems)) { ?>
    <div class="cart-summary">
        <p class="total-price"><strong>Total Price:</strong> Ksh <?php echo number_format($totalPrice, 2); ?></p>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
    </div>
<?php } ?>
<!-- Newly Added Products Row -->
<h2>Products you may like</h2>
<div class="new-products">
    <?php
    if ($newProductsResult->num_rows > 0) {
        while ($newProduct = $newProductsResult->fetch_assoc()) {
            echo '<div class="new-product-card">';
            // Wrap the image with a link that points to the product page
            echo '<a href="product.php?id=' . $newProduct['id'] . '">';
            echo '<img src="uploads/' . htmlspecialchars($newProduct['image']) . '" alt="' . htmlspecialchars($newProduct['name']) . '" class="product-image">';
            echo '</a>';
            echo '<h3>' . htmlspecialchars($newProduct['name']) . '</h3>';
            echo '<p class="price">Ksh ' . number_format($newProduct['price_ksh'], 2) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>No newly added products found.</p>';
    }
    ?>
</div>
<footer>

  <div class="footer-icons-text">

    <p>You may reach us on:</p>

  </div>

  <div class="footer-icons">

    <!-- Call Icon -->

    <a href="tel:+254773184426" target="_blank">

      <img src="uploads/call.png" alt="Call Us">

    </a>

    

    <!-- WhatsApp Icon -->

    <a href="https://wa.me/254773184426?text=Hello%20there,%20I%20would%20like%20to%20learn%20more%20about%20the%20sneakers%20from%20BrandX." target="_blank">

      <img src="uploads/whatsapp.png" alt="WhatsApp">

    </a>

    

    <!-- Email Icon -->

    <a href="mailto:michaelngugi448@gmail.com?subject=Inquiry%20about%20BrandX%20Sneakers&body=Hello%20there,%20I%20would%20like%20to%20inquire%20about%20the%20sneakers%20available%20on%20BrandX%20Online%20Store." target="_blank">

      <img src="uploads/gmail.png" alt="Email">

    </a>

    

    <!-- Instagram Icon -->

    <a href="https://www.instagram.com/top_brand_x?igsh=MWFsajhibHVrOXFtcg==" target="_blank">

      <img src="uploads/ig.png" alt="Instagram">

    </a>

  </div>

  <p>&copy; 2024-2025 BrandX Online Store | All Rights Reserved <br>Developed and maintained by 

    <a href="https://wa.me/254773743248" target="_blank" style="color: #4ea8de; text-decoration: none;">Simon Ngugi</a>.

  </p>

</footer>


<script>
function toggleMenu() {
    const menu = document.getElementById('menu');
    const hamMenu = document.querySelector('.ham-menu');
    menu.classList.toggle('active');
    hamMenu.classList.toggle('active');
}
</script>
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
</body>
</html>