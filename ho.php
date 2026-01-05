<?php

// Include the database connection
include('db.php');

// Start the session to use cart functionality
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']); // Check if 'user_id' session variable exists

// Include the authentication handler
include('auth.php');

// Check if the user is logged in via cookie
$user_data = checkAuth();

// Initialize cart count to 0 by default
$cartCount = 0;

// If the user is logged in, fetch the cart count from the database
if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];

    // Query to count the total quantity of products in the user's cart
    $cartQuery = "SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = $userId";
    $cartResult = $connection->query($cartQuery);
    
    if ($cartResult && $cartResult->num_rows > 0) {
        $row = $cartResult->fetch_assoc();
        $cartCount = $row['cart_count']; // Total quantity of items in the cart
    }
}

// Query to fetch all products from the database in random order
$sql = "SELECT * FROM products ORDER BY RAND()";  // Added ORDER BY RAND() to fetch products randomly
$result = $connection->query($sql);  // Execute the query

// Fetch the latest products based on the highest id values
$newProductsSql = "SELECT * FROM products ORDER BY id DESC LIMIT 20";
$newProductsResult = $connection->query($newProductsSql);

// Function to get random ad banners
function getRandomAd() {
    // Directory containing ads
    $adDirectory = 'assets/ads/';
    $adFiles = glob($adDirectory . '*.{jpg,jpeg,png,gif,webp,mp4,webm}', GLOB_BRACE);

    // Check if there are any ad files
    if (empty($adFiles)) {
        return ''; // No ads to display
    }

    // Select a random ad
    $randomAd = $adFiles[array_rand($adFiles)];
    $fileExtension = strtolower(pathinfo($randomAd, PATHINFO_EXTENSION));

    // Display ad based on type
    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        // Display image ad
        return '<div class="ad-banner">
                    <img src="' . $randomAd . '" alt="Ad Banner" style="width:100%; max-height:300px; object-fit:cover;">
                </div>';
    } elseif (in_array($fileExtension, ['mp4', 'webm'])) {
        // Display video ad
        return '<div class="ad-banner">
                    <video autoplay muted loop style="width:100%; max-height:300px; object-fit:cover;">
                        <source src="' . $randomAd . '" type="video/' . $fileExtension . '">
                        Your browser does not support the video tag.
                    </video>
                </div>';
    }

    return '';
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
        
        <meta name="monetag" content="1e245ad9a82232c848e85dcf06fa59a7">
    <meta charset="UTF-8">
   
   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandX - quality sneakers</title>
    <link rel="stylesheet" href="st.css">  <!-- Link to your CSS file -->
    <script src="script.js"></script>
          <script>(function(d,z,s){s.src='https://'+d+'/401/'+z;try{(document.body||document.documentElement).appendChild(s)}catch(e){}})('gizokraijaw.net',8719318,document.createElement('script'))</script>
        
    <style>
    
    
            /* Chat icon container styles */
.chat-icon-container {
    position: fixed; /* Keeps the icon in a fixed position while scrolling */
    left: 10px; /* Distance from the left of the screen */
    bottom: 10px; /* Distance from the bottom of the screen */
    z-index: 1000; /* Ensures it's always on top */
    
    
}

/* Chat icon image styles */
.chat-icon img {
    width: 75px; /* Set the size of the icon */
    height: 75px;
    cursor: pointer; /* Show a pointer cursor when hovered over */
    border-radius: 50%; /* Optionally round the icon */
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); /* Optional shadow for visibility */
    transition: opacity 0.3s; /* Smooth transition for hover effect */
}


/* Optional hover effect for the chat icon */
.chat-icon img:hover {
    opacity: 0.8; /* Slight transparency when hovered */
}
        /* General page styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0d1117;
            color: #c9d1d9;
        }
       
  @keyframes shine {
  0% {
    background-position: -500% 0;
  }
  50% {
    background-position: 500% 0;
  }
  100% {
    background-position: -500% 0;
  }
}

header {
  /* Updated background with darker shades and deep maroon */
  background: linear-gradient(120deg, 
      rgba(0, 0, 0, 1) 0%,           /* Darker black */
      rgba(0, 31, 61, 1) 35%,        /* Darker blue */
     rgba(128, 0, 32, 1) 50%,  
      rgba(0, 31, 61, 1) 65%,        /* Darker blue */
      rgba(0, 0, 0, 1) 100%);        /* Darker black */

  background-size: 400% 100%; /* Larger background size for smooth animation */
  animation: shine 150s ease-in-out infinite; /* Slow transition for smooth gradient effect */
  
  height: 60px; /* Header height */
  width: 100%; /* Full width */
  position: fixed; /* Fix the header to the top of the page */
  top: 0; /* Align it to the top of the page */
  left: 0; /* Align it to the left of the page */
  z-index: 1000; /* Ensure it's on top of other content */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional shadow for depth */
}

body {
  padding-top: 60px; /* Add space to body content so it doesn't get hidden behind the fixed header */
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

        /* Product Container */
        .product-container {
            display: flex;
            flex-wrap: nowrap; /* No wrapping */
            overflow-x: auto; /* Enable horizontal scrolling */
            gap: 15px;
            justify-content: flex-start;
            padding: 10px 20px;
            width: 100%;
            scroll-behavior: smooth;
            
        }

        /* Product Card */
        .product {
            flex: 0 0 calc(16.6667% - 15px); /* 6 products per row on large screens */
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
/* Best from BrandX Section */
.best-from-brandx {
    display: flex;
    justify-content: flex-start;
    overflow-x: auto; /* Allow horizontal scrolling if too many items */
    gap: 20px; /* Space between products */
    margin-top: 30px;
    padding: 20px 10px;
    scroll-snap-type: x mandatory; /* Ensure smooth scroll behavior */
}
       /* Off-Screen Menu Styles */
.off-screen-menu {

    background: #0d1117;/* Darker background without transparency */

    color: rgb(248, 245, 245);

    height: 100vh;
        backdrop-filter: blur(10px);

    width: 250px; /* Menu width */

    position: fixed;

    top: 0;

    z-index: 10;

    right: -250px; /* Initially off-screen */

    display: flex;

    flex-direction: column;

    justify-content: center;

    align-items: center;

    text-align: center;

    font-size: 1.5rem;

    transition: right 0.3s ease; /* Smooth slide-in effect */

    box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2); /* Slight shadow for depth */

}

/* When the menu is active (slid in) */
.off-screen-menu.active {
    right: 0; /* Slide the menu into view */
}

.off-screen-menu ul {
    list-style: none;
    padding: 0;
}

.off-screen-menu li {
    margin: 20px 0;
    display: block;
    padding: 10px 15px;
    border-bottom: 1px solid #ccc;
}

.off-screen-menu a {
    color: rgb(255, 255, 255);
    text-decoration: none;
    font-size: 1.5rem;
}

/* Hamburger Menu Styles */
.ham-menu {
    height: 50px;
    width: 50px;
    margin-left: auto;
    position: relative;
    cursor: pointer;
    z-index: 1000; /* Ensures it stays on top */
}

.ham-menu span {
    height: 5px;
    width: 100%;
    background-color: #fff;
    border-radius: 25px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.ham-menu span:nth-child(1) {
    top: 25%;
}

.ham-menu span:nth-child(2) {
    top: 50%;
}

.ham-menu span:nth-child(3) {
    top: 75%;
}

/* When the hamburger menu is active */
.ham-menu.active span:nth-child(1) {
    top: 50%;
    transform: translate(-50%, -50%) rotate(45deg); /* Rotate the top bar */
}

.ham-menu.active span:nth-child(2) {
    opacity: 0; /* Hide the middle bar */
}

.ham-menu.active span:nth-child(3) {
    top: 50%;
    transform: translate(-50%, 50%) rotate(-45deg); /* Rotate the bottom bar */
}

/* Make sure the header/nav is positioned correctly */
nav {
    padding: 1rem;
    background-color: #444;
    position: relative;
}



        /* "Add to Cart" Button Styles */
        .add-to-cart {
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

        .add-to-cart:hover {
            background:none; /* Darker green on hover */
            border: 1px solid rgb(4, 201, 250);
            color:  #4ea8de;
        }

        
          
         
        /* Container for the slider */
        .slider-container {
      width: 100%;
      overflow: hidden;
      position: relative;
      height: 200px; /* Set the height of the slider */
    }

    /* The slides */
    .slider {
      display: flex;
      transition: transform 0.5s ease-in-out;
       height: 200px; /* Set the height of the slider */
    }
/* Media query for screens smaller than 1024px (tablets) */
@media (max-width: 1024px) {
  .slider {
    height: 100px; /* Set a smaller height for tablets */
  }
}

    /* Each individual slide */
    .slide {
      min-width: calc(100% - 20px); /* Adjusting width to account for the gap */
  margin: 0 10px; /* Creates the gap between slides */
  transition: opacity 0.5s ease-in-out;
  border-radius: 10px; /* Rounded corners for the slides */
  overflow: hidden; /* Ensures the content fits within the rounded corners */

    }

  

    /* Navigation buttons (optional) */
    .1prev, .next {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      padding: 10px;
      font-size: 18px;
      border: none;
      cursor: pointer;
    }

    .1prev {
      left: 10px;
    }

    .1next {
      right: 10px;
    }

/* Styles for images and videos to control size */
.slider img,
.slider video {
   
      width: 100%; /* Set the width of the media */
      height: 200px; /* Set the height of the media */
    }
            /* Media queries for smaller screens */
@media (max-width: 1024px) {
  .slider-container {
    max-width: 100px; /* Reduce the size of the slider for smaller screens */
 
  }

  .slider img,
  .slider video {
    max-height: 200px; /* Reduce height further for smaller screens */
          
  }
}

@media (max-width: 768px) {
  .slider-container {
    max-width: 100px; /* Further reduce size on tablets */
  }

  .slider img,
  .slider video {
    max-height: 200px; /* Further limits height */
          
  
}


  .slider-container {
    max-width:95%; /* Make the slider even smaller on very small screens */
    padding: 0 10px; /* Adds small padding */
          height: 100px;
        
  }

  .slider img,
  .slider video {
    max-height: 100px; /* Further limits height on mobile */
          
  }

  .slider-nav button {
    font-size: 12px; /* Smaller button text */
    padding: 6px; /* Smaller buttons */
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
            width: 350px;
            height: 30px;
            padding: 0 15px;
            border-radius: 20px;
            border: 2px solid #FF8C00;
            background-color: #161b22;
            color: white;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            justify-content: center;
            text-decoration: none;
             text-align: center; /* Center the placeholder text inside the input */
        }

        .search-bar:focus {
            border-color: #287b99;
            background-color: #222;
        }

        .search-bar::placeholder {
            color: #4ea8de;
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

/* Center the Menu Logo */

.off-screen-menu .menu-logo {

    text-align: center; /* Center the container */

    margin: 20px 0;

}

.off-screen-menu .menu-logo img {

    height: 60px; /* Adjust size as needed */

    width: auto;

}
/* Styling for the cart icon container */
.cart-icon {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 1000;
}

.cart-icon a {
    position: relative;
    display: inline-block;
}

.cart-icon img {
    width: 30px; /* Cart icon size */
    height: 30px; /* Cart icon size */
}

/* Styling for the cart count */
.cart-count {
    position: absolute;
    top: -6px;  /* Position the counter above the icon */
    right: -20px;  /* Position the counter slightly to the right */
    background-color: red; /* Red background */
    color: white; /* White text */
    border-radius: 50%; /* Rounded background */
    padding: 2px 5px; /* Small padding to make it smaller */
    font-weight: bold; /* Make the number bold */
    font-size: 10px; /* Smaller font size */
    min-width: 10px; /* Set minimum width */
    min-height: 10px; /* Set minimum height */
    display: flex; /* Use flexbox to center the text */
    justify-content: center;
    align-items: center;
}

.banner-ad {
    width: 100%;
    text-align: center;
    margin: 30px 0;

}

.banner-ad img.ad-image {

    max-width: 100%;

    height: auto;

    border-radius: 8px;

    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);

}

.banner-ad a {

    display: inline-block;

}

    </style>
</head>
<body>
    
    <header>
        <div class="ham-menu" onclick="toggleMenu()">
            <span></span><span></span><span></span>
                
        </div>
          <!-- Add this code in your homepage where you want to display the cart icon -->
<div class="cart-icon">
    <a href="cart.php">
        <img src="uploads/cart.png" alt="Cart">
        <!-- Display cart count if available -->
        <?php if (isset($cartCount) && $cartCount > 0): ?>
            <span class="cart-count"><?php echo $cartCount; ?></span>
        <?php endif; ?>
    </a>
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
    
         <a href="search.php">
            <input type="text" class="search-bar" placeholder="Search for products..." readonly>
        </a>
  
    
    <div class="slider-container">
    <div class="slider">
        <?php
        $adsDirectory = 'ads/'; // Directory where ads are stored
        $ads = scandir($adsDirectory); // Get all files in the 'ads' folder

        // Loop through all files and display valid ads (images or videos)
        foreach ($ads as $ad) {
            $filePath = $adsDirectory . $ad;

            // Skip "." and ".." (default directory entries)
            if ($ad !== '.' && $ad !== '..') {
                // Get file extension
                $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                // Display the media based on its type
                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo "<div class='slide'><img src='$filePath' alt='Ad'></div>";
                } elseif (in_array($fileExt, ['mp4', 'webm', 'ogg', 'avi', 'mov'])) {
                    // We disable the controls and set autoplay to true
                    echo "<div class='slide'>
                            <video class='ad-video' autoplay muted loop>
                                <source src='$filePath' type='video/$fileExt'>
                            </video>
                          </div>";
                }
            }
        }

        ?>
    </div>
        <div class="slibuttonv">
        <button class="prev"></button>
        <button class="next"></button>
        </div>
</div>
      
  <script>// Variables
  document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll('.slide');
    const slider = document.querySelector('.slider');
    const totalSlides = slides.length;
    let currentIndex = 0;
    let slideInterval;
    let isAdPlaying = false;

    function goToSlide(index) {
        // Adjusting for continuous looping
        if (index >= totalSlides) {
            currentIndex = 0;
        } else if (index < 0) {
            currentIndex = totalSlides - 1;
        } else {
            currentIndex = index;
        }

        // Slide to the new index
        slider.style.transform = `translateX(-${currentIndex * 100}%)`;

        // Check if it's an ad and repeat it
        const currentSlide = slides[currentIndex];
        const video = currentSlide.querySelector('video');
        if (video) {
            isAdPlaying = true;
            video.play();
            video.onended = function() {
                isAdPlaying = false;
                autoSlide();
            };
        } else {
            isAdPlaying = false;
        }
    }

    function autoSlide() {
        // If an ad is playing, don’t auto slide
        if (!isAdPlaying) {
            goToSlide(currentIndex + 1);
        }
    }

    // Add auto sliding every 6 seconds
    slideInterval = setInterval(autoSlide, 6000);

    // Initialize first slide
    goToSlide(currentIndex);
});

function toggleMenu() {
    const menu = document.getElementById('menu');
    const hamMenu = document.querySelector('.ham-menu');
    menu.classList.toggle('active');
    hamMenu.classList.toggle('active');
}
  
        </script>

  <h2>BEST FROM BRANDX</h2>
<div class="best-from-brandx">
    <?php
    // Query to get featured products (from the featured_products table)
    $featuredSql = "SELECT p.id, p.name, p.price_ksh, p.image FROM products p 
                    INNER JOIN featured_products fp ON p.id = fp.product_id
                    LIMIT 20";  // Limit to 6 products

    $featuredResult = $connection->query($featuredSql);

    if ($featuredResult->num_rows > 0) {
        while ($featuredProduct = $featuredResult->fetch_assoc()) {
            echo '<div class="new-product-card">';
            // Link to the product page
            echo '<a href="product.php?id=' . $featuredProduct['id'] . '">';
            echo '<img src="uploads/' . htmlspecialchars($featuredProduct['image']) . '" alt="' . htmlspecialchars($featuredProduct['name']) . '" class="product-image">';
            echo '</a>';
            echo '<h3>' . htmlspecialchars($featuredProduct['name']) . '</h3>';
            echo '<p class="price">Ksh ' . number_format($featuredProduct['price_ksh'], 2) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>No featured products available at the moment.</p>';
    }
    ?>
</div>


 <!-- Newly Added Products Row -->
<h2>NEW ARRIVALS</h2>
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
<h2>TOP BRANDS</h2>

<div class="top-brands">
    <div class="brand-container">
        <a href="category.php?category=nike">
            <img src="uploads/nike.png" alt="nike">
        </a>
    </div>
        <div class="brand-container">
        <a href="category.php?category=vans">
            <img src="uploads/vans.png" alut="vans">
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

<style>
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
    height: 100px;
}

.brand-container img {
    width: 100px;
    border-radius: 8px;
    transition: transform 0.3s ease;
    height: 100px;
}

.brand-container img:hover {
    transform: translateY(-10px);
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

</style>

 <h2>RECOMMENDED FOR YOU</h2>

    <!-- Product Section -->

<div class="product-container">
    <?php
    // Check if there are any products in the database
    if ($result->num_rows > 0) {
        $counter = 0;  // Counter to track the number of products in a row
        // Loop through each product and display it
        while ($row = $result->fetch_assoc()) {
            // Every 6 products, start a new row
            if ($counter % 6 == 0 && $counter != 0) {
                echo '</div><div class="product-container">';  // Close current row and start new row
            }

            echo '<div class="product">';
            
            // Handle image path dynamically and safely
            $imagePath = 'uploads/' . htmlspecialchars($row['image']);
            
            // Display the product image (image filename stored in DB)
            echo '<a href="product.php?id=' . $row['id'] . '"><img src="' . $imagePath . '" alt="' . htmlspecialchars($row['name']) . '" class="product-image"></a>';
            
            // Display product name and price (only in KSH)
            $priceKsh = number_format($row['price_ksh'], 2);  // Use price_ksh directly from the database
            echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
            echo '<p><strong>Price:</strong> Ksh ' . $priceKsh . '</p>';
            
            // No Add to Cart button now
            
            echo '</div>';
            $counter++;
        }
    } else {
        // Message when there are no products
        echo '<p>No products available.</p>';
    }
    ?>
</div>
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