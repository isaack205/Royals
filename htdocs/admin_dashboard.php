<?php
// Start the session to track user login status
session_start();

// Include the database connection
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php'); // Redirect to login if not logged in
    exit();
}

// Fetch admin details (optional, for a personalized dashboard)
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admins WHERE id = ?";
if ($stmt = $connection->prepare($sql)) {
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
}

// Include the header
include('adminheader.php');
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BrandX</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0a0a0a;
            --secondary: #1a1a1a;
            --accent: #00d2ff;
            --accent-light: #33dbff;
            --text: #ffffff;
            --text-secondary: #888888;
            --danger: #ff4757;
            --success: #2ed573;
            --warning: #ff9f43;
            --card-bg: #1e1e1e;
            --border-color: rgba(255, 255, 255, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--primary);
            color: var(--text);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Welcome Message */
        .welcome-message {
            background: linear-gradient(135deg, var(--secondary) 0%, #252525 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .welcome-message h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--accent);
            font-weight: 600;
        }
        
        .welcome-message h2 i {
            margin-right: 0.8rem;
        }
        
        .welcome-message p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }
        
        /* Tabs Navigation */
        .tabs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .tabs a {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: var(--card-bg);
            color: var(--text);
            text-decoration: none;
            padding: 1.5rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .tabs a:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border-color: var(--accent);
            color: var(--accent);
        }
        
        .tabs a i {
            font-size: 1.8rem;
            margin-bottom: 0.8rem;
            color: var(--accent);
        }
        
        /* Stats Container */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }
        
        .stat-card h3 {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        
        .stat-card h3 i {
            margin-right: 0.5rem;
            color: var(--accent);
        }
        
        .stat-card p {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--accent);
            margin: 0;
        }
        
        /* Recent Activity */
        .recent-activity {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border-color);
        }
        
        .recent-activity h3 {
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            color: var(--accent);
            display: flex;
            align-items: center;
        }
        
        .recent-activity h3 i {
            margin-right: 0.8rem;
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            background-color: rgba(0, 210, 255, 0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .activity-icon i {
            color: var(--accent);
            font-size: 1rem;
        }
        
        .activity-content p {
            margin-bottom: 0.3rem;
            font-weight: 500;
        }
        
        .activity-time {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        
        /* Logout Button */
        form {
            text-align: center;
            margin-top: 2rem;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, var(--danger) 0%, #ff6b81 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 71, 87, 0.3);
        }
        
        .logout-btn i {
            margin-right: 0.5rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .tabs {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            .tabs a {
                padding: 1.2rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .tabs a i {
                font-size: 1.5rem;
            }
            
            .stats-container {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .stat-card p {
                font-size: 1.8rem;
            }
            
            .welcome-message h2 {
                font-size: 1.6rem;
            }
        }
        
        @media (max-width: 480px) {
            .tabs {
                grid-template-columns: 1fr 1fr;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .welcome-message {
                padding: 1.5rem;
            }
            
            .recent-activity {
                padding: 1.5rem;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .welcome-message,
        .tabs,
        .stats-container,
        .recent-activity {
            animation: fadeIn 0.6s ease-out;
        }
        
        .tabs a:nth-child(1) { animation-delay: 0.1s; }
        .tabs a:nth-child(2) { animation-delay: 0.2s; }
        .tabs a:nth-child(3) { animation-delay: 0.3s; }
        .tabs a:nth-child(4) { animation-delay: 0.4s; }
        .tabs a:nth-child(5) { animation-delay: 0.5s; }
        .tabs a:nth-child(6) { animation-delay: 0.6s; }
        .tabs a:nth-child(7) { animation-delay: 0.7s; }
        .tabs a:nth-child(8) { animation-delay: 0.8s; }
        
        .stat-card:nth-child(1) { animation-delay: 0.2s; }
        .stat-card:nth-child(2) { animation-delay: 0.3s; }
        .stat-card:nth-child(3) { animation-delay: 0.4s; }
        .stat-card:nth-child(4) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Welcome Message -->
        <div class="welcome-message">
            <h2><i class="fas fa-user-shield"></i> Welcome, <?php echo htmlspecialchars($admin['username']); ?>!</h2>
            <p>Admin Dashboard</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="tabs">
            <a href="admin_user_profiles.php"><i class="fas fa-inbox"></i> Inbox</a>
            <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Products</a>
            <a href="featured_products.php"><i class="fas fa-star"></i> Featured Products</a>
            <a href="orders_made.php"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="home.php"><i class="fas fa-home"></i> Home</a>
            <a href="admin_products.php"><i class="fas fa-boxes"></i> Product Management</a>
            <a href="upload_ads.php"><i class="fas fa-ad"></i> Ads</a>
            <a href="registeredusers.php"><i class="fas fa-users"></i> Customers</a>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <h3><i class="fas fa-box"></i> Total Products</h3>
                <p><?php 
                    $productCount = $connection->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
                    echo number_format($productCount);
                ?></p>
            </div>
            
            <div class="stat-card">
                <h3><i class="fas fa-shopping-cart"></i> Total Orders</h3>
                <p><?php 
                    $orderCount = $connection->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
                    echo number_format($orderCount);
                ?></p>
            </div>
            
            <div class="stat-card">
                <h3><i class="fas fa-users"></i> Total Customers</h3>
                <p><?php 
                    $userCount = $connection->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
                    echo number_format($userCount);
                ?></p>
            </div>
            
            <div class="stat-card">
                <h3><i class="fas fa-star"></i> Featured Products</h3>
                <p><?php 
                    $featuredCount = $connection->query("SELECT COUNT(*) FROM featured_products")->fetch_row()[0];
                    echo number_format($featuredCount);
                ?></p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h3><i class="fas fa-clock"></i> Recent Activity</h3>
            <ul class="activity-list">
                <?php
                // Fetch recent admin activities
                $activities = $connection->query("
                    SELECT * FROM admin_activities 
                    WHERE admin_id = $admin_id 
                    ORDER BY activity_date DESC 
                    LIMIT 5
                ");
                
                if ($activities && $activities->num_rows > 0) {
                    while ($activity = $activities->fetch_assoc()) {
                        echo '
                        <li class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-' . htmlspecialchars($activity['activity_icon']) . '"></i>
                            </div>
                            <div class="activity-content">
                                <p>' . htmlspecialchars($activity['activity_description']) . '</p>
                                <span class="activity-time">' . date('M j, Y g:i A', strtotime($activity['activity_date'])) . '</span>
                            </div>
                        </li>
                        ';
                    }
                } else {
                    echo '<p>No recent activity found</p>';
                }
                ?>
            </ul>
        </div>

        <!-- Logout Button -->
        <form action="adminlogout.php" method="POST">
            <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>