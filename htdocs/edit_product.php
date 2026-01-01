<?php
ob_start();
session_start();
include('db.php');

// Check if an ID is provided via GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid product ID.";
    exit;
}

// Fetch product data for the given ID
$product_id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $connection->query($sql);

if ($result->num_rows == 0) {
    echo "Product not found.";
    exit;
}

$product = $result->fetch_assoc();

// Handle form submission to update the product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $description = $_POST['description'];
    $price_ksh = $_POST['price_ksh'];
    $available_sizes = $_POST['available_sizes'];
    $available_colors = $_POST['available_colors'];
    $units_available = $_POST['units_available'];
    $category = $_POST['category'];
    $gender_category = $_POST['gender_category'];
    $stock_status = $_POST['stock_status'];
    
    // Handle image upload if a new image is provided
    $image = $product['image']; // Keep current image by default
    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Generate unique filename
            $new_filename = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $new_filename;
                
                // Delete old image if it exists
                if (!empty($product['image']) && file_exists($target_dir . $product['image'])) {
                    unlink($target_dir . $product['image']);
                }
            }
        }
    }
    
    // Update product in the database
    $update_sql = "UPDATE products SET 
        name = ?, 
        slug = ?,
        description = ?, 
        image = ?, 
        price_ksh = ?, 
        available_sizes = ?, 
        available_colors = ?, 
        units_available = ?,
        category = ?,
        gender_category = ?,
        stock_status = ?
        WHERE id = ?";
    
    $stmt = $connection->prepare($update_sql);
    $stmt->bind_param(
        "sssssssssssi", 
        $name, $slug, $description, $image, $price_ksh, 
        $available_sizes, $available_colors, $units_available,
        $category, $gender_category, $stock_status, $product_id
    );
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Product updated successfully!";
        header("Location: admin_products.php");
        exit;
    } else {
        $error_message = "Error updating product: " . $stmt->error;
    }
}

ob_end_flush();

// Include the header
include('adminheader.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0a0a0a;
            --secondary: #1a1a1a;
            --accent: #00d2ff;
            --text: #ffffff;
            --text-secondary: #888888;
            --danger: #ff4757;
            --success: #2ed573;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--primary);
            color: var(--text);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
            margin-top: 70px; /* Adjusted to account for fixed header */
        }
        
        h2 {
            margin: 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent);
            display: flex;
            align-items: center;
        }
        
        h2 i {
            margin-right: 0.8rem;
            color: var(--accent);
        }
        
        .error-message {
            background-color: var(--danger);
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .error-message i {
            margin-right: 0.5rem;
        }
        
        .form-container {
            background-color: var(--secondary);
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--accent);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 4px;
            background-color: rgba(255,255,255,0.05);
            color: var(--text);
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(0,210,255,0.2);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--accent);
            color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #00b8e6;
        }
        
        .btn-secondary {
            background-color: rgba(255,255,255,0.1);
            color: var(--text);
        }
        
        .btn-secondary:hover {
            background-color: rgba(255,255,255,0.2);
        }
        
        .image-preview {
            margin-top: 1rem;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 4px;
            object-fit: cover;
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .form-col {
            flex: 1;
            min-width: 250px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
            
            .form-col {
                width: 100%;
            }
            
            .container {
                margin-top: 60px; /* Adjusted for smaller screens */
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Edit Product</h2>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="edit_product.php?id=<?php echo $product['id']; ?>" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" class="form-control" 
                                value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" id="slug" name="slug" class="form-control" 
                                value="<?php echo htmlspecialchars($product['slug']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="price_ksh">Price (Ksh)</label>
                            <input type="number" id="price_ksh" name="price_ksh" class="form-control" 
                                value="<?php echo htmlspecialchars($product['price_ksh']); ?>" required min="0" step="0.01">
                        </div>
                        
                        <div class="form-group">
                            <label for="available_sizes">Available Sizes (comma separated)</label>
                            <input type="text" id="available_sizes" name="available_sizes" class="form-control" 
                                value="<?php echo htmlspecialchars($product['available_sizes']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-col">
                        <div class="form-group">
                            <label for="available_colors">Available Colors (comma separated)</label>
                            <input type="text" id="available_colors" name="available_colors" class="form-control" 
                                value="<?php echo htmlspecialchars($product['available_colors']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="units_available">Units Available</label>
                            <input type="number" id="units_available" name="units_available" class="form-control" 
                                value="<?php echo htmlspecialchars($product['units_available']); ?>" required min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="Nike" <?php echo $product['category'] == 'Nike' ? 'selected' : ''; ?>>Nike</option>
                                <option value="Adidas" <?php echo $product['category'] == 'Adidas' ? 'selected' : ''; ?>>Adidas</option>
                                <option value="Puma" <?php echo $product['category'] == 'Puma' ? 'selected' : ''; ?>>Puma</option>
                                <option value="Converse" <?php echo $product['category'] == 'Converse' ? 'selected' : ''; ?>>Converse</option>
                                <option value="Fila" <?php echo $product['category'] == 'Fila' ? 'selected' : ''; ?>>Fila</option>
                                <option value="Jordan" <?php echo $product['category'] == 'Jordan' ? 'selected' : ''; ?>>Jordan</option>
                                <option value="Vans" <?php echo $product['category'] == 'Vans' ? 'selected' : ''; ?>>Vans</option>
                                <option value="Gucci" <?php echo $product['category'] == 'Gucci' ? 'selected' : ''; ?>>Gucci</option>
                                <option value="Timberland" <?php echo $product['category'] == 'Timberland' ? 'selected' : ''; ?>>Timberland</option>
                                <option value="Lv" <?php echo $product['category'] == 'Lv' ? 'selected' : ''; ?>>LV</option>
                                <option value="Newbalance" <?php echo $product['category'] == 'Newbalance' ? 'selected' : ''; ?>>New Balance</option>
                                <option value="Other" <?php echo $product['category'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender_category">Gender Category</label>
                            <select id="gender_category" name="gender_category" class="form-control" required>
                                <option value="all" <?php echo $product['gender_category'] == 'all' ? 'selected' : ''; ?>>All</option>
                                <option value="men" <?php echo $product['gender_category'] == 'men' ? 'selected' : ''; ?>>Men</option>
                                <option value="women" <?php echo $product['gender_category'] == 'women' ? 'selected' : ''; ?>>Women</option>
                                <option value="kids" <?php echo $product['gender_category'] == 'kids' ? 'selected' : ''; ?>>Kids</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_status">Stock Status</label>
                            <select id="stock_status" name="stock_status" class="form-control" required>
                                <option value="in_stock" <?php echo $product['stock_status'] == 'in_stock' ? 'selected' : ''; ?>>In Stock</option>
                                <option value="low_stock" <?php echo $product['stock_status'] == 'low_stock' ? 'selected' : ''; ?>>Low Stock</option>
                                <option value="out_of_stock" <?php echo $product['stock_status'] == 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    
                    <?php if (!empty($product['image'])): ?>
                        <div class="image-preview">
                            <p>Current Image:</p>
                            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    <a href="admin_products.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$connection->close();
?>