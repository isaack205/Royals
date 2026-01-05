<?php
// Include the database connection
include('db.php');

// Start the session to use cart functionality
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']); 

// Get the search query from the URL
$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';

// Fetch products that match the query (start with the same prefix)
$sql = "SELECT * FROM products WHERE name LIKE '$searchQuery%' ORDER BY name";
$result = $connection->query($sql);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandX - Search Results</title>
    <link rel="stylesheet" href="st.css">
    <style>
        /* General page styling */
       /* General page styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #0d1117;
    color: #c9d1d9;
}

h1 {
    text-align: center;
    font-size: 36px;
    color: #4ea8de;
    font-family: 'Times New Roman', Times, serif;
    margin-top: 20px;
}

h3 {
    color: #c9d1d9;
    font-size: 20px;
    font-style: italic;
    text-align: center;
}

/* Product Container (Grid Layout with Two Products per Row) */
.product-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* Default: Two columns */
    gap: 20px; /* Space between items */
    justify-items: center; /* Centers items in each grid cell */
    margin-top: 20px;
}

/* Product Card Styling */
.product {
    width: 100%; /* Full width of the grid item */
    text-align: center;
    background-color:  #161b22;
    border-radius: 12px;
    padding: 5px;
    color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.product img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}

.product h3 {
    font-size: 18px;
    margin: 15px 0;
}

.product p {
    font-size: 16px;
}

.add-to-cart {
    display: inline-block;
    padding: 10px 20px;
    background: linear-gradient(135deg, #4ea8de, #287b99); /* Gradient background */
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 16px;
    transition: background-color 0.3s ease;
    cursor: pointer;
}

.add-to-cart:hover {
    background: linear-gradient(135deg, #287b99, #4ea8de); /* Reversed gradient */
}

.search-bar {
    text-align: center;
    margin: 20px 0;
}

/* Footer Styling */
footer {
    background-color: #161b22;
    color: #c9d1d9;
    padding: 20px 0;
    text-align: center;
    margin-top: 50px;
}

footer p {
    margin: 0;
    font-size: 14px;
}

/* Responsive Design for Medium Screens (Tablets, etc.) */
@media (max-width: 768px) {
    .product-container {
        grid-template-columns: repeat(2, 1fr); /* Two products per row on medium screens */
    }

    .product {
        width: 100%;
    }

    h1 {
        font-size: 28px;
    }

    h3 {
        font-size: 18px;
    }
}

/* Responsive Design for Small Screens (Mobile Devices) */
@media (max-width: 480px) {
    .product-container {
        grid-template-columns: repeat(2, 1fr); /* Two products per row even on small screens */
    }

    .product {
        width: 100%;
        margin: 10px 0;
    }

    h1 {
        font-size: 24px;
    }

    h3 {
        font-size: 16px;
    }
}

/* Large Screen (Desktop) - 6 products per row */
@media (min-width: 1200px) {
    .product-container {
        grid-template-columns: repeat(6, 1fr); /* 6 products per row */
    }
}

 /* Back Button */
 .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #4ea8de;
        border-radius: 50%;
        color: white;
        font-size: 20px;
        cursor: pointer;
        text-decoration: none;
      }

      .back-button:hover {
        background-color: #c9d1d9;
      }
            /* Styling for the search bar */
 .search-bar {
            display: flex;
            align-items: center;
            margin: auto;
            width: 250px;
            height: 40px;
            padding: 0 15px;
            border-radius: 20px;
            border: 2px solid #4ea8de;
            background-color: #161b22;
            color: white;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            justify-content: center;
            text-decoration: none;
        }

        .search-bar:focus {
            border-color: #287b99;
            background-color: #222;
        }

        .search-bar::placeholder {
            color: #8e8e8e;
            text-decoration: none;
        }
            .logo img,

.off-screen-menu .menu-logo img {

    background: transparent; /* Ensure transparency */

    display: block;

    margin: 0 auto; /* Center horizontally */

}

/* Center the Header Logo */

.logo {

    text-align: center; /* Center the container */

    padding: 10px 0;

}

.logo img {

    height: 70px; /* Adjust size as needed */

    width: auto;

}

/* Center the Menu Logo */

.off-screen-menu .menu-logo {

    text-align: center; /* Center the container */

    margin: 20px 0;

}

.off-screen-menu .menu-logo img {




    </style>
        </head>
<body>
     <!-- Back Button -->
     <a href="search.php" class="back-button">
        <i class="fas fa-arrow-left"> 🔙</i> <!-- Font Awesome left arrow icon -->
    </a>
    <header>
            <div class="logo">

            <a href="home.php">

                <img src="uploads/brandxlogo.png" alt="BrandX Logo">

            </a>

        </div>
        <h1>Search Results</h1>
    </header>

    <!-- Search Bar Section -->
    <div class="search-barr">
        <h3>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h3>
    </div>
              <a href="search.php">
            <input type="text" class="search-bar" placeholder="Search for products..." />
        </a>

    <!-- Product List Section -->
   <div class="product-container">
    <?php
    if (!empty($products)) {
        foreach ($products as $product) {
            $priceKsh = number_format($product['price_ksh'], 2);
            echo '<div class="product">';
            echo '<a href="product.php?id=' . $product['id'] . '"><img src="uploads/' . $product['image'] . '" alt="' . $product['name'] . '" style="width: 100%;"></a>';
            echo '<h3>' . $product['name'] . '</h3>';
            echo '<p><strong>Price:</strong> Ksh ' . $priceKsh . '</p>';
            // Removed the "Add to Cart" button
            // echo '<a href="cart.php?action=add&id=' . $product['id'] . '" class="add-to-cart">Add to Cart</a>';
            echo '</div>';
        }
    } else {
        echo '<p>No products found for this search.</p>';
    }
    ?>
</div>
             <footer>

        <p>&copy; 2024-2025 BrandX Online Store | All Rights Reserved <br>Developed and maintained by 

<a href="https://wa.me/254773743248" target="_blank" style="color: #4ea8de; text-decoration: none;">

  Simon Ngugi

</a>.

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

</body>
</html>

<?php
// Close the database connection
$connection->close();
?>
