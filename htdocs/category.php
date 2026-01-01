<?php
// Check if a category is set in the URL
if (isset($_GET['category'])) {
    $category = $_GET['category'];
} else {
    // If no category is selected, default to 'home' or some other fallback
    $category = 'home';
}

// Fetch products based on category
include('db.php');

// Ensure the category is valid by sanitizing it
$category = mysqli_real_escape_string($connection, $category);

// Set limit to 6 products per page
$limit = 6;

// Fetch the products with the limit and offset for pagination

$sql = "SELECT * FROM products WHERE category = '$category' ORDER BY RAND() LIMIT 10"; // Change LIMIT as needed
$result = $connection->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandX - <?php echo ucfirst($category); ?> Products</title>
    <link rel="stylesheet" href="sty.css">
    <script src="script.js"></script>
</head>
<body>
     <!-- Back Button -->
     <a href="home.php" class="back-button">
        <i class="fas fa-arrow-left"> 🔙 </i>
    </a>
    <header>
    <div class="ham-menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
       
        <div class="logo">

            <a href="home.php">

                <img src="uploads/brandxlogo.png" alt="BrandX Logo">

            </a>

        </div>
        <div class="off-screen-menu" id="menu">
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
    


    <h1><?php echo ucfirst($category); ?> Sneakers</h1>
    
    <div class="product-container">
    <?php
if ($result->num_rows > 0) {
    $count = 0; // Initialize the count to track the number of products displayed
    while ($row = $result->fetch_assoc()) {
        $imagePath = 'uploads/' . htmlspecialchars($row['image']);

        // Start a new row after every 6 products
        if ($count % 6 == 0 && $count != 0) {
            echo '</div><div class="product-container">'; // Close the previous row and start a new one
        }

        // Fetch the latest products based on the highest id values
$newProductsSql ="SELECT * FROM products ORDER BY RAND()";
$newProductsResult = $connection->query($newProductsSql);


        // Display each product
        echo '<div class="product">';
        echo '<a href="product.php?id=' . $row['id'] . '"><img src="' . $imagePath . '" alt="' . htmlspecialchars($row['name']) . '" class="product-image"></a>';
        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
        echo '<p><strong>Price: Ksh ' . number_format($row['price_ksh'], 2) . '</strong></p>';
        echo '</div>';

        $count++; // Increment the counter
    }
} else {
    echo '<p>No sneakers from this brand currently.</p>';
}
?>

    </div>
    <h2>Other Brands</h2>

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
        
        <h2>Sneakers from Brands you may like</h2>
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

<style>
header{
     background: linear-gradient(120deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 31, 61, 0.9) 50%, rgba(0, 0, 0, 0.8) 100%);
  background-size: 400% 100%; /* Larger background size for more smoothness */
  animation: shine 150s ease-in-out infinite; /* Slow transition */
  height: 60px; /* Header height */
  width: 100%; /* Full width */
 
  top: 0; /* Align it to the top of the page */
  left: 0; /* Align it to the left of the page */
  z-index: 1000; /* Ensure it's on top of other content */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional shadow for depth */
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
/* Center the Header Logo */
.navlogo {
    text-align: center; /* Center the container */
    padding: 10px 0;
}

.navlogo img {
    height: 70px; /* Adjust size as needed */
    width: auto;
}
.top-brands {
    display: flex;
    justify-content: flex-start;
    overflow-x: auto; /* Allow horizontal scrolling */
    gap: 10px; /* Space between brand containers */
    
    padding: 5px 10px; /* Reduced top and bottom padding */
    scroll-snap-type: x mandatory; /* Ensure smooth scroll behavior */
}


.brand-container {
    flex: 0 0 auto; /* Allow containers to be as small as needed */
    max-width: 100px; /* Ensure small fixed width */
    min-width: 100px; /* Ensure small fixed width */
    text-align: center;
}

.brand-container img {
    width: 100%;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.brand-container img:hover {
    transform: translateY(-10px);
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
        
        /* General page styling */
  body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0d1117;
            color: #c9d1d9;
        }
        
      

        /* Main Title and Description */
        h1 {
            text-align: center;
            font-size: 36px;
            color: #4ea8de;
            font-family: 'Times New Roman', Times, serif;
        }

        h2 {
            text-align: center;
            font-size: 20px;
            color: #FF8C00;
            font-weight: lighter;
            font-style: italic;
            font-family: Times New Roman;
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
            
/* Product Title */
.product h3 {
    font-size: 15px;
    color: #4ea8de; /* Blue for title */
    margin: 10px 0;
        font-family:Times New Roman;
}

/* Product Price */
.product p {
    font-size: 14px;
    color: #8b949e; /* Lighter color for product description */
    line-height: 1.5;
}

.product p strong {
    color: #ff6600; /* Orange for emphasis */
}

        /* Categories Section */
        .categories {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .categories a {
            background-color: rgb(1, 72, 116);
            color: white;
            padding: 10px 20px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .product-container {
    display: flex;
    flex-wrap: nowrap; /* Prevent wrapping to new rows */
    overflow-x: auto; /* Enable horizontal scrolling */
    gap: 15px;
    justify-content: flex-start;
    padding: 10px 20px;
    width: 100%;
    scroll-behavior: smooth;
}

.product {
    flex: 0 0 calc(16.6667% - 15px); /* 6 products per row */
    background: #161b22;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    transition: transform 0.3s ease;
    max-width: 200px; /* Ensure fixed width */
    min-width: 200px; /* Ensure fixed width */
}

.product:hover {
    transform: translateY(-10px);
}
                    /* Newly Added Products Row */


        /* Product Image */
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            max-height: 150px;
        }

        /* Scrollable Container - Mobile Specific */
        /* Ensuring no squeezing of products on small screens */
        @media (max-width: 768px) {
            .product-container {
                padding: 10px 0;
            }
            .product {
                flex: 0 0 calc(16.6667% - 15px); /* Maintain fixed size of products */
            }
        }

        @media (max-width: 480px) {
            .product-container {
                padding: 10px 0;
            }
            .product {
                flex: 0 0 calc(16.6667% - 15px); /* Maintain fixed size of products */
            }
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
.button-link {
    width: 100%;
            padding: 12px;
            border-radius: 6px;
            background-color:rgb(31, 119, 173); /* Green background for button */
            color: white;
            font-size: 18px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
            text-decoration: none;
}

.button-link:hover {
    background:none; /* Darker green on hover */
            border: 1px solid rgb(4, 201, 250);
            color:  #4ea8de;
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
            /* General Reset for Transparency */

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

    height: 60px; /* Adjust size as needed */

    width: auto;

}


 
</style>
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


</html>

<?php
// Close the database connection
$connection->close();
?>
