<?php
// Include database connection
include('db.php');

// Fetch all featured products from the database
$sql = "SELECT * FROM featured_products fp JOIN products p ON fp.product_id = p.id";
$featuredResult = $connection->query($sql);

// Handle removal of a featured product
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    // Delete the product from the featured_products table
    $deleteSql = "DELETE FROM featured_products WHERE product_id = ?";
    $stmt = $connection->prepare($deleteSql);
    $stmt->bind_param('i', $product_id);
    if ($stmt->execute()) {
        // After removal, redirect to refresh the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error removing product!";
    }
}

// Handle the admin form to add products to the featured list
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Check if the product exists
    $checkProductSql = "SELECT * FROM products WHERE id = ?";
    $stmt = $connection->prepare($checkProductSql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Insert into the 'featured_products' table
        $insertSql = "INSERT INTO featured_products (product_id) VALUES (?)";
        $stmtInsert = $connection->prepare($insertSql);
        $stmtInsert->bind_param('i', $product_id);
        $stmtInsert->execute();
        echo "Product added to featured list.";
    } else {
        echo "Product not found.";
    }

    $stmt->close();

    // After adding, redirect to refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Featured Products</title>
    <style>
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
            margin-top: 40px;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            color: rgb(255, 255, 255);
            font-weight: lighter;
            font-style: italic;
            font-family: cursive;
        }

        /* Product List Styling */
        .featured-products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            padding: 20px 10px;
        }

        .featured-product-card {
            background: #161b22;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            width: 200px;
            max-width: 200px;
            min-width: 200px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .featured-product-card:hover {
            transform: translateY(-10px);
        }

        .featured-product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }

        .featured-product-card h3 {
            font-size: 15px;
            color: #4ea8de;
            margin: 10px 0;
        }

        .featured-product-card .price {
            font-size: 0.9rem;
            color: white;
        }

        .featured-product-card a {
            background-color: red;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
            display: inline-block;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .featured-product-card a:hover {
            background-color: maroon;
        }

        /* Add product form styling */
        .add-product-form {
            margin-top: 30px;
            text-align: center;
            background-color: #161b22;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            margin: 30px auto;
        }

        .add-product-form input[type="number"] {
            padding: 10px;
            width: auto;
            border-radius: 6px;
            border: 1px solid #4ea8de;
            margin-bottom: 20px;
            background-color: #222;
            color: #c9d1d9;
        }

        .add-product-form input[type="submit"] {
            padding: 12px 20px;
            border-radius: 6px;
            background-color: rgb(31, 119, 173);
            color: white;
            font-size: 18px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        .add-product-form input[type="submit"]:hover {
            background-color: rgb(4, 201, 250);
        }
    </style>
</head>
<body>

    <h1>Manage Featured Products</h1>

    <!-- Add Product Form -->
    <div class="add-product-form">
        <form method="POST" action="">
            <label for="product_id">Enter Product ID:</label>
            <input type="number" name="product_id" required>
            <input type="submit" value="Add to Featured">
        </form>
    </div>

    <!-- Featured Products List -->
    <h2>Featured Products</h2>
    <div class="featured-products">
        <?php
        if ($featuredResult->num_rows > 0) {
            while ($product = $featuredResult->fetch_assoc()) {
                echo '<div class="featured-product-card">';
                echo '<img src="uploads/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">';
                echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
                echo '<p class="price">Ksh ' . number_format($product['price_ksh'], 2) . '</p>';
                echo '<a href="?remove=' . $product['product_id'] . '" onclick="return confirm(\'Are you sure you want to remove this product?\')">Remove from Featured</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No featured products found.</p>';
        }
        ?>
    </div>

</body>
</html>
