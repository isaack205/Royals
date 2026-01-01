<?php
session_start();
include('db.php');

// Ensure the user is an admin (you may need to adjust this according to your authentication system)

// Fetch all products
$sql = "SELECT * FROM products";
$result = $connection->query($sql);

// Display success message if set
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying
}
include('adminheader.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Products</title>
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
        
        header {
            background-color: var(--secondary);
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        header h1 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--accent);
        }
        
        .admin-nav {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .admin-nav a {
            color: var(--text);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            background-color: rgba(255,255,255,0.1);
            transition: all 0.3s;
        }
        
        .admin-nav a:hover {
            background-color: var(--accent);
            color: var(--primary);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }
        
        h2 {
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent);
            display: flex;
            align-items: center;
        }
        
        h2 i {
            margin-right: 0.8rem;
            color: var(--accent);
        }
        
        .success-message {
            background-color: var(--success);
            color: var(--primary);
            padding: 0.8rem 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .success-message i {
            margin-right: 0.5rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--secondary);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
        }
        
        th, td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        th {
            background-color: rgba(0,210,255,0.1);
            color: var(--accent);
            font-weight: 600;
        }
        
        tr:hover {
            background-color: rgba(255,255,255,0.05);
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .edit-btn, .delete-btn {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .edit-btn {
            background-color: rgba(0,210,255,0.2);
            color: var(--accent);
            border: 1px solid var(--accent);
        }
        
        .edit-btn:hover {
            background-color: var(--accent);
            color: var(--primary);
        }
        
        .delete-btn {
            background-color: rgba(255,71,87,0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }
        
        .delete-btn:hover {
            background-color: var(--danger);
            color: white;
        }
        
        .no-products {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }
        
        .no-products i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            color: rgba(255,255,255,0.1);
        }
        
        @media (max-width: 900px) {
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                padding: 0.6rem;
            }
            
            .product-image {
                width: 50px;
                height: 50px;
            }
        }
        
        @media (max-width: 600px) {
            header h1 {
                font-size: 1.5rem;
            }
            
            .admin-nav {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .admin-nav a {
                width: 100%;
                text-align: center;
            }
            
            th, td {
                padding: 0.4rem;
                font-size: 0.9rem;
            }
            
            .edit-btn, .delete-btn {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
   

    <div class="container">
        <h2><i class="fas fa-box"></i> All Products</h2>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price (Ksh)</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $imagePath = 'uploads/' . htmlspecialchars($row['image']);
                        $description = htmlspecialchars($row['description']);
                        // Truncate description if too long
                        if (strlen($description) > 100) {
                            $description = substr($description, 0, 100) . '...';
                        }
                        
                        echo "<tr>";
                        echo "<td><img src='" . $imagePath . "' alt='" . htmlspecialchars($row['name']) . "' class='product-image'></td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . number_format($row['price_ksh'], 2) . "</td>";
                        echo "<td>" . $description . "</td>";
                        echo "<td>
                                <a href='edit_product.php?id=" . $row['id'] . "' class='edit-btn'><i class='fas fa-edit'></i> Edit</a>
                                <a href='delete_product.php?id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this product?\");'><i class='fas fa-trash'></i> Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='no-products'>
                            <i class='fas fa-box-open'></i>
                            <p>No products found</p>
                          </td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$connection->close();
?>