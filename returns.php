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
        /* Returns Page Specific Styles */
        .returns-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
        }

        /* Hero Section */
        .returns-hero {
            text-align: center;
            padding: 3rem 2rem;
            background: linear-gradient(135deg, rgba(0, 210, 255, 0.1), rgba(14, 33, 65, 0.3));
            border-radius: 15px;
            margin-bottom: 3rem;
            border: 1px solid rgba(0, 210, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .returns-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 107, 107, 0.1) 0%, transparent 70%);
            z-index: 0;
        }

        .returns-hero-content {
            position: relative;
            z-index: 1;
        }

        .returns-hero h1 {
            font-size: 3rem;
            color: #ff4757;
            margin-bottom: 1rem;
            font-weight: 800;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Policy Sections */
        .policy-section {
            margin-bottom: 3rem;
            padding: 2rem;
            background-color: rgba(14, 33, 65, 0.3);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ff4757;
        }

        .section-header h2 {
            font-size: 1.8rem;
            color: #ff4757;
            margin: 0;
        }

        .section-header i {
            font-size: 1.8rem;
            color: #ff4757;
        }

        /* Policy Points */
        .policy-point {
            margin: 1.5rem 0;
            padding: 1.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border-left: 4px solid #ff4757;
        }

        .point-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.8rem;
        }

        .point-header h3 {
            font-size: 1.3rem;
            color: #ff4757;
            margin: 0;
        }

        .point-header i {
            color: #ff4757;
            font-size: 1.2rem;
        }

        .point-content {
            color: var(--text);
            line-height: 1.6;
        }

        .point-content ul {
            padding-left: 1.5rem;
            margin: 0.8rem 0;
        }

        .point-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        /* Timeline Steps */
        .timeline-steps {
            margin: 2rem 0;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 71, 87, 0.2);
        }

        .step-number {
            width: 50px;
            height: 50px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        .step-content h4 {
            font-size: 1.2rem;
            color: #ff4757;
            margin-bottom: 0.5rem;
        }

        .step-content p {
            color: var(--text);
            line-height: 1.6;
            margin: 0;
        }

        /* Conditions Grid */
        .conditions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .condition-card {
            padding: 1.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .condition-card.approved {
            border-color: #2ed573;
        }

        .condition-card.rejected {
            border-color: #ff4757;
        }

        .condition-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .condition-card.approved .condition-icon {
            background: rgba(46, 213, 115, 0.2);
            color: #2ed573;
        }

        .condition-card.rejected .condition-icon {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
        }

        .condition-card h4 {
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
        }

        .condition-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }

        /* Timeframes */
        .timeframes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .timeframe {
            padding: 1.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }

        .timeframe h4 {
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        .timeframe .duration {
            font-size: 1.5rem;
            color: #ff4757;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .timeframe p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Warning Box */
        .warning-box {
            background-color: rgba(255, 71, 87, 0.1);
            border: 1px solid #ff4757;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .warning-box h4 {
            color: #ff4757;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .warning-box p {
            color: var(--text);
            line-height: 1.6;
            margin: 0;
        }

        /* CTA Section */
        .returns-cta {
            text-align: center;
            padding: 3rem 2rem;
            background: linear-gradient(135deg, rgba(0, 210, 255, 0.1), rgba(14, 33, 65, 0.3));
            border-radius: 15px;
            margin-top: 3rem;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }

        .returns-cta h2 {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .returns-cta p {
            font-size: 1.1rem;
            color: var(--text);
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-returns {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--accent), #3a7bd5);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 210, 255, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #ff4757;
            border: 2px solid #ff4757;
        }

        .btn-secondary:hover {
            background: #ff4757;
            color: white;
        }

        /* FAQ Section */
        .faq-section {
            margin: 3rem 0;
        }

        .faq-item {
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .faq-question {
            padding: 1.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--text);
        }

        .faq-question:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .faq-item.active .faq-answer {
            padding: 1.5rem;
            max-height: 500px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .returns-container {
                padding: 1rem;
            }

            .returns-hero {
                padding: 2rem 1rem;
            }

            .returns-hero h1 {
                font-size: 2.2rem;
            }

            .policy-section {
                padding: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .conditions-grid {
                grid-template-columns: 1fr;
            }

            .timeframes {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-returns {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .step {
                flex-direction: column;
                text-align: center;
            }

            .step-number {
                margin: 0 auto;
            }
        }

        @media (max-width: 480px) {
            .returns-hero h1 {
                font-size: 1.8rem;
            }

            .policy-point {
                padding: 1rem;
            }

            .condition-card {
                padding: 1rem;
            }

            .timeframe {
                padding: 1rem;
            }
        }
    </style>

    <div class="returns-container">
        <!-- Hero Section -->
        <section class="returns-hero">
            <div class="returns-hero-content">
                <h1><i class="fas fa-exchange-alt"></i> Returns & Refunds Policy</h1>
                <p class="hero-subtitle">
                    Your satisfaction is our priority. Learn about our hassle-free returns and refunds process.
                </p>
            </div>
        </section>

        <!-- Overview Section -->
        <section class="policy-section">
            <div class="section-header">
                <i class="fas fa-info-circle"></i>
                <h2>Policy Overview</h2>
            </div>
            
            <div class="policy-point">
                <div class="point-header">
                    <i class="fas fa-handshake"></i>
                    <h3>Our Commitment</h3>
                </div>
                <div class="point-content">
                    <p>At Royals, we stand behind the quality of our products. If you're not completely satisfied with your purchase, we're here to help. We offer a straightforward returns and refunds process designed with your convenience in mind.</p>
                </div>
            </div>

            <div class="policy-point">
                <div class="point-header">
                    <i class="fas fa-clock"></i>
                    <h3>Return Window</h3>
                </div>
                <div class="point-content">
                    <p>You have <strong>14 days</strong> from the date of delivery to initiate a return request for most items. This gives you ample time to inspect your purchase and ensure it meets your expectations.</p>
                </div>
            </div>
        </section>

        <!-- Return Conditions -->
        <section class="policy-section">
            <div class="section-header">
                <i class="fas fa-clipboard-check"></i>
                <h2>Return Conditions</h2>
            </div>
            
            <div class="conditions-grid">
                <div class="condition-card approved">
                    <div class="condition-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h4>Accepted Returns</h4>
                    <p>Items in original condition, unused, with all tags attached and original packaging</p>
                </div>
                
                <div class="condition-card approved">
                    <div class="condition-icon">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                    <h4>Trial Wear</h4>
                    <p>Light indoor try-on is acceptable for sizing verification only</p>
                </div>
                
                <div class="condition-card rejected">
                    <div class="condition-icon">
                        <i class="fas fa-times"></i>
                    </div>
                    <h4>Non-Returnable</h4>
                    <p>Items worn outdoors, damaged, or with removed tags cannot be returned</p>
                </div>
                
                <div class="condition-card rejected">
                    <div class="condition-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <h4>Personalized Items</h4>
                    <p>Custom or personalized products cannot be returned unless defective</p>
                </div>
            </div>
        </section>

        <!-- Return Process -->
        <section class="policy-section">
            <div class="section-header">
                <i class="fas fa-sync-alt"></i>
                <h2>Return Process</h2>
            </div>
            
            <div class="timeline-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>Initiate Return Request</h4>
                        <p>Log into your account, go to "My Orders" and select the item you wish to return. Fill out the return request form within 14 days of delivery.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>Receive Approval & Instructions</h4>
                        <p>We'll review your request within 24-48 hours and send you return approval and instructions. You'll receive a Return Authorization Number (RAN).</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>Package & Ship</h4>
                        <p>Pack the item securely in its original packaging with all accessories and tags. Include the RAN on the outside of the package.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>Inspection & Refund</h4>
                        <p>Once received, we'll inspect the item within 3-5 business days and process your refund via the original payment method.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Refund Information -->
        <section class="policy-section">
            <div class="section-header">
                <i class="fas fa-money-bill-wave"></i>
                <h2>Refund Information</h2>
            </div>
            
            <div class="timeframes">
                <div class="timeframe">
                    <h4>Processing Time</h4>
                    <div class="duration">3-5 Days</div>
                    <p>After we receive and inspect the returned item</p>
                </div>
                
                <div class="timeframe">
                    <h4>Refund Method</h4>
                    <div class="duration">Original Payment</div>
                    <p>Refunded to your original payment method</p>
                </div>
                
                <div class="timeframe">
                    <h4>Bank Processing</h4>
                    <div class="duration">5-10 Days</div>
                    <p>Additional time for bank processing</p>
                </div>
            </div>
            
            <div class="warning-box">
                <h4><i class="fas fa-exclamation-triangle"></i> Important Note</h4>
                <p>Shipping costs are non-refundable unless the return is due to our error (wrong item shipped, defective product, etc.). Return shipping costs are the customer's responsibility unless otherwise specified.</p>
            </div>
        </section>

        <!-- Exchange Policy -->
        <section class="policy-section">
            <div class="section-header">
                <i class="fas fa-exchange-alt"></i>
                <h2>Exchange Policy</h2>
            </div>
            
            <div class="policy-point">
                <div class="point-header">
                    <i class="fas fa-arrows-alt-h"></i>
                    <h3>Size Exchanges</h3>
                </div>
                <div class="point-content">
                    <p>We offer free size exchanges within 14 days of delivery. If you need a different size:</p>
                    <ul>
                        <li>Initiate an exchange request through your account</li>
                        <li>We'll ship the new size once we receive the return</li>
                        <li>No additional shipping charges for size exchanges</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-point">
                <div class="point-header">
                    <i class="fas fa-palette"></i>
                    <h3>Color/Style Exchanges</h3>
                </div>
                <div class="point-content">
                    <p>Color or style exchanges are treated as a return and repurchase:</p>
                    <ul>
                        <li>Return the original item following standard procedures</li>
                        <li>Place a new order for the desired color/style</li>
                        <li>Your refund will be processed separately</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Defective/Damaged Items -->
        <section class="policy-section">
            <div class="section-header">
                <i class="fas fa-tools"></i>
                <h2>Defective or Damaged Items</h2>
            </div>
            
            <div class="policy-point">
                <div class="point-header">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Immediate Reporting Required</h3>
                </div>
                <div class="point-content">
                    <p>If you receive a defective or damaged item, you must report it within 48 hours of delivery. Contact our customer service immediately with:</p>
                    <ul>
                        <li>Order number</li>
                        <li>Clear photos of the defect/damage</li>
                        <li>Description of the issue</li>
                    </ul>
                    <p>We'll arrange for a prepaid return label and expedite your replacement or refund.</p>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="policy-section">
            <div class="section-header">
                <i class="fas fa-question-circle"></i>
                <h2>Frequently Asked Questions</h2>
            </div>
            
            <div class="faq-section">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How long does the refund process take?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Once we receive and inspect your return, refunds are processed within 3-5 business days. It may take an additional 5-10 business days for the refund to appear on your original payment method.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What if I received the wrong item?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Contact us immediately within 48 hours of delivery. We'll provide a prepaid return label and expedite shipping of the correct item to you.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Can I return sale items?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, sale items can be returned within 14 days of delivery, provided they meet our return conditions. All sale items are subject to the same quality standards.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Do I need the original box for returns?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>While not absolutely necessary, we strongly recommend using the original packaging to protect the shoes during return shipping and to ensure they reach us in the same condition.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What about international returns?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>International returns are accepted within 30 days of delivery. Customers are responsible for return shipping costs and any customs duties. Refunds are processed in KSH.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="returns-cta">
            <h2>Need Help With a Return?</h2>
            <p>Our customer service team is ready to assist you with any questions about returns, exchanges, or refunds.</p>
            
            <div class="cta-buttons">
                <a href="contact.php" class="btn-returns btn-primary">
                    <i class="fas fa-headset"></i> Contact Support
                </a>
                <a href="orders.php" class="btn-returns btn-secondary">
                    <i class="fas fa-box"></i> My Orders
                </a>
                <a href="shipping.php" class="btn-returns btn-secondary">
                    <i class="fas fa-shipping-fast"></i> Shipping Policy
                </a>
            </div>
            
            <div style="margin-top: 2rem; font-size: 0.9rem; color: var(--text-secondary);">
                <p><i class="fas fa-history"></i> Last Updated: <?php echo date('F j, Y'); ?></p>
                <p>This policy is subject to change. Please check back periodically for updates.</p>
            </div>
        </section>
    </div>
</main>

<script>
    // FAQ Toggle Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const faqItem = this.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                // Close all FAQ items
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Open clicked item if it wasn't already active
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
        
        // Open first FAQ by default
        if (faqQuestions.length > 0) {
            faqQuestions[0].parentElement.classList.add('active');
        }
    });
</script>

<?php
// Include the footer
include('footer.php');

// Close the database connection
if (isset($connection)) {
    $connection->close();
}
?>