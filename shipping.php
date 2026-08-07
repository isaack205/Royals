<?php
// Include the database connection
include('db.php');

// Start the session
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Calculate cart count from session
$cartCount = count($_SESSION['cart']);

// Include the header
include('header.php');
?>

<main>
    <style>
        /* Shipping Policy Specific Styles */
        .shipping-policy-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .shipping-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--accent);
        }

        .shipping-header h1 {
            color: var(--accent);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .shipping-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .policy-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: rgba(14, 33, 65, 0.3);
            border-radius: 8px;
            border-left: 4px solid var(--accent);
        }

        .policy-section h2 {
            color: var(--accent);
            margin-bottom: 1rem;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .policy-section h2 i {
            font-size: 1.5rem;
        }

        .policy-point {
            margin: 1rem 0;
            padding: 0.8rem;
            background-color: rgba(255, 255, 255, 0.03);
            border-radius: 6px;
            border-left: 3px solid rgba(0, 210, 255, 0.5);
        }

        .policy-point h3 {
            color: var(--text);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .policy-point p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin: 0;
        }

        .shipping-schedule {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .schedule-day {
            padding: 1.2rem;
            background-color: rgba(0, 210, 255, 0.1);
            border-radius: 8px;
            text-align: center;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }

        .schedule-day.active {
            background-color: rgba(0, 210, 255, 0.2);
            border-color: var(--accent);
        }

        .day-name {
            font-weight: bold;
            color: var(--accent);
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .day-status {
            color: var(--text);
            font-size: 0.9rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .status-shipping {
            background-color: rgba(0, 210, 255, 0.2);
            color: var(--accent);
        }

        .status-processing {
            background-color: rgba(255, 165, 0, 0.2);
            color: orange;
        }

        .info-box {
            background-color: rgba(0, 210, 255, 0.1);
            border: 1px solid rgba(0, 210, 255, 0.2);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .info-box h3 {
            color: var(--accent);
            margin-bottom: 0.8rem;
            font-size: 1.3rem;
        }

        .info-box p {
            color: var(--text);
            line-height: 1.6;
        }

        .time-deadline {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
        }

        .time-deadline i {
            color: var(--accent);
            font-size: 1.5rem;
        }

        .deadline-text {
            flex: 1;
        }

        .deadline-text strong {
            color: var(--accent);
        }

        .benefits-list {
            margin: 1rem 0;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin: 0.8rem 0;
            padding: 0.5rem;
        }

        .benefit-item i {
            color: var(--accent);
            font-size: 1.2rem;
            margin-top: 0.2rem;
        }

        .shipping-cta {
            text-align: center;
            margin-top: 2rem;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(0, 210, 255, 0.1), rgba(14, 33, 65, 0.3));
            border-radius: 10px;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }

        .shipping-cta h3 {
            color: var(--accent);
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .btn-shipping {
            padding: 0.8rem 1.5rem;
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-shipping:hover {
            background-color: #00b8e6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 210, 255, 0.3);
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--accent);
            color: var(--accent);
        }

        .btn-outline:hover {
            background-color: var(--accent);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .shipping-policy-container {
                padding: 1.5rem;
                margin: 1rem;
            }

            .shipping-header h1 {
                font-size: 2rem;
            }

            .policy-section {
                padding: 1rem;
            }

            .policy-section h2 {
                font-size: 1.5rem;
            }

            .shipping-schedule {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-shipping {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .shipping-policy-container {
                padding: 1rem;
                margin: 0.5rem;
            }

            .shipping-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>

    <div class="shipping-policy-container">
        <!-- Header Section -->
        <div class="shipping-header">
            <h1><i class="fas fa-shipping-fast"></i> Shipping Policy</h1>
            <p>Transparent, Reliable, and Efficient Delivery Services</p>
        </div>

        <!-- Shipping Schedule Section -->
        <div class="policy-section">
            <h2><i class="fas fa-calendar-alt"></i> Shipping Days & Schedule</h2>
            
            <div class="info-box">
                <h3>📦 Our Shipping Days</h3>
                <p>All confirmed orders are shipped exclusively on:</p>
            </div>

            <div class="shipping-schedule">
                <?php
                $days = [
                    ['day' => 'Monday', 'shipping' => false, 'status' => 'Processing Day'],
                    ['day' => 'Tuesday', 'shipping' => true, 'status' => 'Shipping Day'],
                    ['day' => 'Wednesday', 'shipping' => false, 'status' => 'Processing Day'],
                    ['day' => 'Thursday', 'shipping' => true, 'status' => 'Shipping Day'],
                    ['day' => 'Friday', 'shipping' => false, 'status' => 'Processing Day'],
                    ['day' => 'Saturday', 'shipping' => true, 'status' => 'Shipping Day'],
                    ['day' => 'Sunday', 'shipping' => false, 'status' => 'Closed']
                ];

                foreach ($days as $day):
                ?>
                <div class="schedule-day <?php echo $day['shipping'] ? 'active' : ''; ?>">
                    <div class="day-name"><?php echo $day['day']; ?></div>
                    <div class="day-status <?php echo $day['shipping'] ? 'status-shipping' : 'status-processing'; ?>">
                        <?php echo $day['status']; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Order Deadlines Section -->
        <div class="policy-section">
            <h2><i class="fas fa-clock"></i> Order Processing Deadlines</h2>
            
            <div class="policy-point">
                <h3>⏰ Same-Day Shipping</h3>
                <p>Orders placed before <strong>11:00 AM</strong> on shipping days (Tuesday, Thursday, Saturday) will be processed and shipped the same day.</p>
                
                <div class="time-deadline">
                    <i class="fas fa-check-circle"></i>
                    <div class="deadline-text">
                        <strong>Before 11:00 AM:</strong> Your order ships the same day
                    </div>
                </div>
            </div>

            <div class="policy-point">
                <h3>📅 Next-Day Shipping</h3>
                <p>Orders placed after <strong>11:00 AM</strong> on shipping days will be processed and shipped on the next available shipping day.</p>
                
                <div class="time-deadline">
                    <i class="fas fa-calendar-plus"></i>
                    <div class="deadline-text">
                        <strong>After 11:00 AM:</strong> Your order ships on the next shipping day
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h3>📝 Example Timeline</h3>
                <p>• Order placed Monday at 2:00 PM → Shipped Tuesday<br>
                   • Order placed Tuesday at 10:30 AM → Shipped Tuesday<br>
                   • Order placed Tuesday at 11:30 AM → Shipped Thursday<br>
                   • Order placed Friday at any time → Shipped Saturday</p>
            </div>
        </div>

        <!-- Why This Schedule Section -->
        <div class="policy-section">
            <h2><i class="fas fa-question-circle"></i> Why This Shipping Schedule?</h2>
            
            <p>Our Tuesday, Thursday, and Saturday shipping schedule is designed to provide you with the best possible service quality:</p>
            
            <div class="benefits-list">
                <div class="benefit-item">
                    <i class="fas fa-clipboard-check"></i>
                    <div>
                        <strong>Thorough Order Processing:</strong> Allows adequate time for verifying all order details, payment confirmation, and inventory checks.
                    </div>
                </div>
                
                <div class="benefit-item">
                    <i class="fas fa-search"></i>
                    <div>
                        <strong>Quality Assurance:</strong> Each product undergoes careful inspection to ensure it meets our high-quality standards before shipping.
                    </div>
                </div>
                
                <div class="benefit-item">
                    <i class="fas fa-box-open"></i>
                    <div>
                        <strong>Secure Packaging:</strong> We take extra time to properly package your items, ensuring they arrive in perfect condition.
                    </div>
                </div>
                
                <div class="benefit-item">
                    <i class="fas fa-shipping-fast"></i>
                    <div>
                        <strong>Reliable Logistics:</strong> Coordinated shipping days allow us to work efficiently with our delivery partners for timely deliveries.
                    </div>
                </div>
                
                <div class="benefit-item">
                    <i class="fas fa-headset"></i>
                    <div>
                        <strong>Better Customer Support:</strong> This schedule enables our team to provide more personalized support and handle inquiries promptly.
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h3>🎯 Our Commitment</h3>
                <p>While we have specific shipping days, we're committed to processing all orders as quickly as possible. Rest assured, your order is in good hands from the moment you click "Purchase" until it arrives at your doorstep.</p>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="policy-section">
            <h2><i class="fas fa-info-circle"></i> Additional Information</h2>
            
            <div class="policy-point">
                <h3>📞 Order Tracking</h3>
                <p>Once your order is shipped, you'll receive a tracking number via email/SMS. You can track your package in real-time through our website or delivery partner's portal.</p>
            </div>

            <div class="policy-point">
                <h3>📍 Delivery Areas</h3>
                <p>We currently ship within [Country/Region]. Delivery times vary based on your location but typically range from 1-5 business days after shipping.</p>
            </div>

            <div class="policy-point">
                <h3>🔄 Shipping Updates</h3>
                <p>You'll receive notifications at every stage: order confirmation, processing, shipping, and delivery. Our customer service team is always available to assist with any shipping inquiries.</p>
            </div>
        </div>

        <!-- Call to Action Section -->
        <div class="shipping-cta">
            <h3>Ready to Shop?</h3>
            <p>Now that you know our shipping policy, browse our collection with confidence!</p>
            
            <div class="cta-buttons">
                <a href="index.php" class="btn-shipping">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
                <a href="contact.php" class="btn-shipping btn-outline">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
                <a href="faq.php" class="btn-shipping btn-outline">
                    <i class="fas fa-question"></i> View FAQ
                </a>
            </div>
        </div>

        <!-- Last Updated -->
        <div style="text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); color: var(--text-secondary); font-size: 0.9rem;">
            <p><i class="fas fa-history"></i> Last Updated: <?php echo date('F j, Y'); ?></p>
            <p>This policy is subject to change. Please check back periodically for updates.</p>
        </div>
    </div>
</main>

<?php
// Include the footer
include('footer.php');

// Close the database connection
if (isset($connection)) {
    $connection->close();
}
?>