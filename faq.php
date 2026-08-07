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
        /* FAQ Page Specific Styles */
        .faq-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
        }

        /* Hero Section */
        .faq-hero {
            text-align: center;
            padding: 3rem 2rem;
            background: linear-gradient(135deg, rgba(0, 210, 255, 0.1), rgba(14, 33, 65, 0.3));
            border-radius: 15px;
            margin-bottom: 3rem;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }

        .faq-hero h1 {
            font-size: 3rem;
            color: var(--accent);
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

        .search-box {
            max-width: 500px;
            margin: 2rem auto 0;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 1rem 1.5rem 1rem 3rem;
            border: 2px solid var(--accent);
            border-radius: 50px;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.2);
        }

        .search-box i {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            font-size: 1.2rem;
        }

        /* Category Navigation */
        .category-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            justify-content: center;
            margin-bottom: 3rem;
            padding: 1rem;
            background-color: rgba(14, 33, 65, 0.3);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .category-btn {
            padding: 0.8rem 1.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            color: var(--text-secondary);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .category-btn:hover {
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--accent);
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .category-btn.active {
            background-color: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        /* FAQ Sections */
        .faq-section {
            margin-bottom: 3rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--accent);
        }

        .section-header h2 {
            font-size: 1.8rem;
            color: var(--accent);
            margin: 0;
        }

        .section-header i {
            font-size: 1.8rem;
            color: var(--accent);
        }

        /* FAQ Items */
        .faq-items {
            display: grid;
            gap: 1rem;
        }

        .faq-item {
            background-color: rgba(14, 33, 65, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 210, 255, 0.1);
        }

        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--text);
            font-size: 1.1rem;
        }

        .faq-question:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .faq-question span {
            flex: 1;
            margin-right: 1rem;
        }

        .faq-icon {
            color: var(--accent);
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease, padding 0.3s ease;
        }

        .faq-item.active .faq-answer {
            padding: 0 1.5rem 1.5rem;
            max-height: 1000px;
        }

        .answer-content {
            color: var(--text-secondary);
            line-height: 1.8;
            font-size: 1rem;
        }

        .answer-content ul {
            padding-left: 1.5rem;
            margin: 0.8rem 0;
        }

        .answer-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        .answer-content strong {
            color: var(--accent);
        }

        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 3rem 0;
        }

        .quick-link-card {
            padding: 1.5rem;
            background-color: rgba(14, 33, 65, 0.3);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .quick-link-card:hover {
            border-color: var(--accent);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 210, 255, 0.1);
        }

        .quick-link-icon {
            width: 60px;
            height: 60px;
            background: rgba(0, 210, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: var(--accent);
        }

        .quick-link-card h3 {
            color: var(--accent);
            margin-bottom: 0.8rem;
            font-size: 1.2rem;
        }

        .quick-link-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .quick-link-btn {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--accent);
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .quick-link-btn:hover {
            background-color: var(--accent);
            color: white;
        }

        /* CTA Section */
        .faq-cta {
            text-align: center;
            padding: 3rem 2rem;
            background: linear-gradient(135deg, rgba(0, 210, 255, 0.1), rgba(14, 33, 65, 0.3));
            border-radius: 15px;
            margin-top: 3rem;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }

        .faq-cta h2 {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .faq-cta p {
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

        .btn-faq {
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
            color: var(--accent);
            border: 2px solid var(--accent);
        }

        .btn-secondary:hover {
            background: var(--accent);
            color: white;
        }

        /* No Results Message */
        .no-results {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
            display: none;
        }

        .no-results i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--border-color);
        }

        /* Back to Top Button */
        .faq-back-to-top {
            text-align: center;
            margin-top: 2rem;
        }

        .back-to-top-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--accent);
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-to-top-btn:hover {
            background-color: var(--accent);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .faq-container {
                padding: 1rem;
            }

            .faq-hero {
                padding: 2rem 1rem;
            }

            .faq-hero h1 {
                font-size: 2.2rem;
            }

            .category-nav {
                flex-direction: column;
                align-items: stretch;
            }

            .category-btn {
                justify-content: center;
            }

            .faq-question {
                padding: 1.2rem;
                font-size: 1rem;
            }

            .quick-links {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-faq {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .faq-hero h1 {
                font-size: 1.8rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .faq-question {
                padding: 1rem;
            }

            .faq-answer {
                padding: 0 1rem;
            }

            .faq-item.active .faq-answer {
                padding: 0 1rem 1rem;
            }
        }
    </style>

    <div class="faq-container">
        <!-- Hero Section -->
        <section class="faq-hero">
            <h1><i class="fas fa-question-circle"></i> Frequently Asked Questions</h1>
            <p class="hero-subtitle">
                Find quick answers to common questions about shopping, shipping, returns, and more.
            </p>
            
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="faqSearch" placeholder="Search for answers...">
            </div>
        </section>

        <!-- Category Navigation -->
        <div class="category-nav">
            <button class="category-btn active" data-category="all">
                <i class="fas fa-th-large"></i> All Questions
            </button>
            <button class="category-btn" data-category="shipping">
                <i class="fas fa-shipping-fast"></i> Shipping & Delivery
            </button>
            <button class="category-btn" data-category="orders">
                <i class="fas fa-shopping-cart"></i> Orders & Payments
            </button>
            <button class="category-btn" data-category="returns">
                <i class="fas fa-exchange-alt"></i> Returns & Refunds
            </button>
            <button class="category-btn" data-category="products">
                <i class="fas fa-shoe-prints"></i> Products & Sizing
            </button>
            <button class="category-btn" data-category="account">
                <i class="fas fa-user"></i> Account & Security
            </button>
        </div>

        <!-- FAQ Sections -->
        
        <!-- Shipping & Delivery -->
        <section class="faq-section" data-category="shipping">
            <div class="section-header">
                <i class="fas fa-shipping-fast"></i>
                <h2>Shipping & Delivery</h2>
            </div>
            
            <div class="faq-items">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How long does shipping take?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>We ship orders on <strong>Tuesday, Thursday, and Saturday</strong>. Orders placed before 11:00 AM on these days are shipped the same day. Orders placed after 11:00 AM are shipped on the next shipping day.</p>
                            <p>Delivery typically takes <strong>2-7 business days</strong> depending on your location within Kenya. You'll receive a tracking number once your order is shipped.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Do you offer international shipping?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>Yes! We now offer international shipping to select countries. Shipping rates and delivery times vary by destination. During checkout, enter your address to see available shipping options and costs for your location.</p>
                            <p>International orders typically take <strong>7-21 business days</strong> to arrive, depending on customs processing.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How can I track my order?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>Once your order is shipped, you'll receive an email with your tracking number and a link to track your package. You can also track your order by:</p>
                            <ul>
                                <li>Logging into your account and visiting "My Orders"</li>
                                <li>Clicking the tracking link in your shipping confirmation email</li>
                                <li>Contacting our customer service team with your order number</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Orders & Payments -->
        <section class="faq-section" data-category="orders">
            <div class="section-header">
                <i class="fas fa-shopping-cart"></i>
                <h2>Orders & Payments</h2>
            </div>
            
            <div class="faq-items">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>We accept various secure payment methods:</p>
                            <ul>
                                <li><strong>MPesa</strong> (Lipa Na MPesa)</li>
                                <li><strong>Credit/Debit Cards</strong> (Visa, MasterCard, American Express)</li>
                                <li><strong>Mobile Money</strong> across multiple networks</li>
                                <li><strong>Bank Transfer</strong> for corporate orders</li>
                            </ul>
                            <p>All payments are processed securely through encrypted channels.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Can I modify or cancel my order?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>You can modify or cancel your order within <strong>1 hour</strong> of placing it, provided it hasn't been processed for shipping. To request changes:</p>
                            <ul>
                                <li>Contact our customer service immediately</li>
                                <li>Provide your order number and requested changes</li>
                                <li>We'll do our best to accommodate your request</li>
                            </ul>
                            <p>Once an order has been processed for shipping, it cannot be modified or canceled.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Is it safe to shop on your website?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>Absolutely! We take security seriously:</p>
                            <ul>
                                <li><strong>SSL Encryption</strong> - All data is encrypted during transmission</li>
                                <li><strong>Secure Payment Gateway</strong> - We don't store your payment details</li>
                                <li><strong>Privacy Protection</strong> - Your information is never shared or sold</li>
                                <li><strong>PCI Compliance</strong> - We follow industry security standards</li>
                            </ul>
                            <p>You can shop with confidence knowing your information is protected.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Returns & Refunds -->
        <section class="faq-section" data-category="returns">
            <div class="section-header">
                <i class="fas fa-exchange-alt"></i>
                <h2>Returns & Refunds</h2>
            </div>
            
            <div class="faq-items">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What is your return policy?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>We offer a <strong>14-day return policy</strong> from the date of delivery. Items must be:</p>
                            <ul>
                                <li>In original condition (unworn, undamaged)</li>
                                <li>With all original tags attached</li>
                                <li>In the original packaging</li>
                                <li>Accompanied by proof of purchase</li>
                            </ul>
                            <p>Refunds are processed within 3-5 business days after we receive and inspect the returned item.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I initiate a return?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>To initiate a return:</p>
                            <ul>
                                <li>Log into your account and go to "My Orders"</li>
                                <li>Select the item you wish to return</li>
                                <li>Complete the return request form</li>
                                <li>Wait for return approval and instructions</li>
                                <li>Ship the item back with the provided return label</li>
                            </ul>
                            <p>You'll receive email notifications at each step of the process.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What if I receive a damaged or wrong item?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>If you receive a damaged item or the wrong product:</p>
                            <ul>
                                <li><strong>Contact us immediately</strong> within 48 hours of delivery</li>
                                <li>Provide clear photos of the issue</li>
                                <li>Share your order number and details</li>
                                <li>We'll arrange for a prepaid return label</li>
                                <li>We'll expedite your replacement or refund</li>
                            </ul>
                            <p>In such cases, return shipping is free, and we prioritize your replacement.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Products & Sizing -->
        <section class="faq-section" data-category="products">
            <div class="section-header">
                <i class="fas fa-shoe-prints"></i>
                <h2>Products & Sizing</h2>
            </div>
            
            <div class="faq-items">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I find the right shoe size?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>Finding the perfect fit is easy:</p>
                            <ul>
                                <li>Check our detailed <strong>size guide</strong> on each product page</li>
                                <li>Measure your foot length in centimeters</li>
                                <li>Compare with the size chart provided</li>
                                <li>Consider ordering half a size up for comfort</li>
                                <li>Read customer reviews for sizing tips</li>
                            </ul>
                            <p>Remember, you have 14 days to exchange for a different size if needed!</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Are your products authentic?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>Yes! We guarantee <strong>100% authenticity</strong> for all our products. We source directly from authorized distributors and trusted suppliers. Every product undergoes quality verification before being listed on our store.</p>
                            <p>You'll receive genuine products with all original packaging, tags, and authenticity indicators. We stand behind the quality of every item we sell.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I care for my sneakers?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>To keep your sneakers looking fresh:</p>
                            <ul>
                                <li><strong>Cleaning</strong> - Use a soft brush and mild soap for most materials</li>
                                <li><strong>Drying</strong> - Air dry naturally, away from direct heat</li>
                                <li><strong>Storage</strong> - Keep in a cool, dry place with shoe trees</li>
                                <li><strong>Protection</strong> - Apply waterproof spray for suede/leather</li>
                                <li><strong>Rotation</strong> - Alternate between pairs to extend life</li>
                            </ul>
                            <p>Check product care instructions for specific material recommendations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Account & Security -->
        <section class="faq-section" data-category="account">
            <div class="section-header">
                <i class="fas fa-user"></i>
                <h2>Account & Security</h2>
            </div>
            
            <div class="faq-items">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I create an account?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>Creating an account is simple and free:</p>
                            <ul>
                                <li>Click the "Login/Sign Up" button in the header</li>
                                <li>Select "Create Account"</li>
                                <li>Enter your email address and create a password</li>
                                <li>Verify your email through the confirmation link</li>
                                <li>Complete your profile information</li>
                            </ul>
                            <p>With an account, you can track orders, save favorites, and checkout faster.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>I forgot my password. What should I do?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>No worries! You can reset your password easily:</p>
                            <ul>
                                <li>Go to the login page</li>
                                <li>Click "Forgot Password"</li>
                                <li>Enter the email address associated with your account</li>
                                <li>Check your email for a password reset link</li>
                                <li>Click the link and create a new password</li>
                            </ul>
                            <p>The reset link expires after 24 hours for security. If you don't receive the email, check your spam folder.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I update my account information?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="answer-content">
                            <p>To update your account information:</p>
                            <ul>
                                <li>Log into your account</li>
                                <li>Click on "My Account" or your profile icon</li>
                                <li>Select "Account Settings" or "Profile"</li>
                                <li>Update your personal information, shipping addresses, or preferences</li>
                                <li>Click "Save Changes" to update</li>
                            </ul>
                            <p>Keeping your information current ensures smooth deliveries and better service.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- No Results Message -->
        <div class="no-results" id="noResults">
            <i class="fas fa-search"></i>
            <h3>No Results Found</h3>
            <p>We couldn't find any questions matching your search. Try different keywords or browse the categories above.</p>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <div class="quick-link-card">
                <div class="quick-link-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Shipping Policy</h3>
                <p>Learn about our shipping schedule, delivery times, and international shipping options.</p>
                <a href="shipping.php" class="quick-link-btn">View Details</a>
            </div>
            
            <div class="quick-link-card">
                <div class="quick-link-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3>Returns Policy</h3>
                <p>Understand our return process, conditions, and refund timelines for your purchases.</p>
                <a href="returns.php" class="quick-link-btn">View Details</a>
            </div>
            
            <div class="quick-link-card">
                <div class="quick-link-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Contact Support</h3>
                <p>Can't find your answer? Our support team is ready to help you with any questions.</p>
                <a href="contact.php" class="quick-link-btn">Get Help</a>
            </div>
        </div>

        <!-- Back to Top -->
        <div class="faq-back-to-top">
            <a href="#" class="back-to-top-btn" id="faqBackToTop">
                <i class="fas fa-arrow-up"></i> Back to Top
            </a>
        </div>

        <!-- Call to Action -->
        <section class="faq-cta">
            <h2>Still Have Questions?</h2>
            <p>Our customer support team is here to help you with any questions not covered in our FAQ.</p>
            
            <div class="cta-buttons">
                <a href="contact.php" class="btn-faq btn-primary">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
                <a href="index.php" class="btn-faq btn-secondary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
            
            <div style="margin-top: 2rem; font-size: 0.9rem; color: var(--text-secondary);">
                <p><i class="fas fa-sync-alt"></i> Last Updated: <?php echo date('F j, Y'); ?></p>
                <p>This FAQ is regularly updated with new information.</p>
            </div>
        </section>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ Toggle Functionality
        const faqQuestions = document.querySelectorAll('.faq-question');
        const searchInput = document.getElementById('faqSearch');
        const categoryBtns = document.querySelectorAll('.category-btn');
        const faqSections = document.querySelectorAll('.faq-section');
        const noResults = document.getElementById('noResults');
        const backToTopBtn = document.getElementById('faqBackToTop');
        
        // Initialize: Open first FAQ in each section
        faqSections.forEach(section => {
            const firstFaq = section.querySelector('.faq-item');
            if (firstFaq) {
                firstFaq.classList.add('active');
            }
        });
        
        // FAQ Toggle
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const faqItem = this.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                // Close all FAQ items in the same section
                const parentSection = faqItem.closest('.faq-section');
                parentSection.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Open clicked item if it wasn't already active
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
        
        // Category Filtering
        categoryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.dataset.category;
                
                // Update active button
                categoryBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Show/hide sections based on category
                if (category === 'all') {
                    faqSections.forEach(section => {
                        section.style.display = 'block';
                    });
                } else {
                    faqSections.forEach(section => {
                        if (section.dataset.category === category) {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                }
                
                // Reset search
                searchInput.value = '';
                noResults.style.display = 'none';
                
                // Scroll to first section
                const firstVisibleSection = document.querySelector('.faq-section[style="display: block"]');
                if (firstVisibleSection) {
                    firstVisibleSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        
        // Search Functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let hasResults = false;
            
            // Reset category filter to 'all'
            categoryBtns.forEach(b => b.classList.remove('active'));
            document.querySelector('.category-btn[data-category="all"]').classList.add('active');
            
            if (searchTerm === '') {
                // Show all sections and questions
                faqSections.forEach(section => {
                    section.style.display = 'block';
                    section.querySelectorAll('.faq-item').forEach(item => {
                        item.style.display = 'block';
                    });
                });
                noResults.style.display = 'none';
                return;
            }
            
            // Search through all questions
            faqSections.forEach(section => {
                section.style.display = 'block';
                let sectionHasResults = false;
                
                section.querySelectorAll('.faq-item').forEach(item => {
                    const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                    const answer = item.querySelector('.answer-content').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                        sectionHasResults = true;
                        hasResults = true;
                        
                        // Highlight search term
                        highlightText(item, searchTerm);
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Show/hide sections based on results
                section.style.display = sectionHasResults ? 'block' : 'none';
            });
            
            // Show/hide no results message
            noResults.style.display = hasResults ? 'none' : 'block';
        });
        
        // Highlight search term in text
        function highlightText(element, searchTerm) {
            const textElements = element.querySelectorAll('.faq-question span, .answer-content');
            
            textElements.forEach(textElement => {
                const originalHTML = textElement.innerHTML;
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                const highlightedHTML = originalHTML.replace(regex, '<span style="background-color: rgba(0, 210, 255, 0.3); padding: 2px 4px; border-radius: 3px;">$1</span>');
                textElement.innerHTML = highlightedHTML;
            });
        }
        
        // Back to Top functionality
        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Show/hide back to top button based on scroll
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.opacity = '1';
                backToTopBtn.style.visibility = 'visible';
            } else {
                backToTopBtn.style.opacity = '0';
                backToTopBtn.style.visibility = 'hidden';
            }
        });
        
        // Initialize back to top button
        backToTopBtn.style.opacity = '0';
        backToTopBtn.style.visibility = 'hidden';
        backToTopBtn.style.transition = 'opacity 0.3s, visibility 0.3s';
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