<?php
// Start the session
session_start();

// Include database connection
include('db.php');

// Include header
include('header.php');
?>

<!DOCTYPE html>
<html lang="en" data-theme="system">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - BrandX</title>
    <meta name="description" content="Discover the premium services offered by BrandX including free delivery, easy returns, customization options and more.">
    <style>
        :root {
            --service-primary: #00d2ff;
            --service-secondary: #2ed573;
            --service-dark: #212529;
            --service-light: #f8f9fa;
            --service-shadow: 0 15px 30px rgba(0,0,0,0.1);
            --transition-speed: 0.3s;
        }

        /* Base Styles */
        body {
            background-color: var(--background);
            color: var(--text);
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Header spacing fix */
        body {
            padding-top: 70px;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 120px;
            }
        }

        /* Main Container */
        .services-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Hero Section */
        .services-hero {
            background: linear-gradient(135deg, var(--service-primary), #3a7bd5);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 3rem;
            box-shadow: var(--service-shadow);
        }

        .services-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .services-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 2rem;
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .service-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--service-shadow);
            transition: transform var(--transition-speed) ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-icon {
            font-size: 3rem;
            color: var(--service-primary);
            margin-bottom: 1.5rem;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--service-primary);
        }

        .service-card p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .service-features {
            list-style-type: none;
            padding: 0;
        }

        .service-features li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: flex-start;
        }

        .service-features li::before {
            content: "✓";
            color: var(--service-secondary);
            font-weight: bold;
            margin-right: 0.8rem;
        }

        /* Premium Services Section */
        .premium-services {
            background-color: var(--card-bg);
            padding: 3rem 2rem;
            border-radius: 12px;
            margin-bottom: 4rem;
            box-shadow: var(--service-shadow);
        }

        .premium-services h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--service-primary);
        }

        .premium-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .premium-card {
            background-color: rgba(0, 210, 255, 0.1);
            border: 1px solid var(--service-primary);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }

        .premium-card h3 {
            color: var(--service-primary);
            margin-bottom: 1rem;
        }

        .premium-card p {
            color: var(--text-secondary);
        }

        /* How It Works Section */
        .how-it-works {
            margin-bottom: 4rem;
        }

        .how-it-works h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
            color: var(--service-primary);
        }

        .steps-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .step {
            flex: 1;
            min-width: 250px;
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background-color: var(--service-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 auto 1.5rem;
        }

        .step h3 {
            margin-bottom: 1rem;
            color: var(--text);
        }

        .step p {
            color: var(--text-secondary);
        }

        .step::after {
            content: "→";
            position: absolute;
            right: -30px;
            top: 30px;
            font-size: 2rem;
            color: var(--service-primary);
        }

        .step:last-child::after {
            display: none;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--service-primary), #3a7bd5);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 3rem;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .cta-section p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        .cta-btn {
            display: inline-block;
            background-color: white;
            color: var(--service-primary);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: bold;
            text-decoration: none;
            font-size: 1.1rem;
            transition: all var(--transition-speed) ease;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .services-hero h1 {
                font-size: 2.5rem;
            }
            
            .step::after {
                display: none;
            }
            
            .steps-container {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .services-hero {
                padding: 3rem 1rem;
            }
            
            .services-hero h1 {
                font-size: 2rem;
            }
            
            .services-hero p {
                font-size: 1rem;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .cta-section h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .services-hero h1 {
                font-size: 1.8rem;
            }
            
            .cta-section {
                padding: 3rem 1rem;
            }
            
            .cta-section h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="services-container">
        <!-- Hero Section -->
        <section class="services-hero">
            <h1>Premium Services by BrandX</h1>
            <p>We go beyond just selling products - our services are designed to provide you with an exceptional shopping experience from start to finish.</p>
        </section>

        <!-- Main Services Grid -->
        <section class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Free & Fast Delivery</h3>
                <p>Enjoy complimentary delivery on all orders within Kenya with our reliable shipping partners.</p>
                <ul class="service-features">
                    <li>2-3 day delivery in Nairobi</li>
                    <li>3-5 day delivery nationwide</li>
                    <li>Real-time order tracking</li>
                    <li>Secure packaging</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3>Easy Returns</h3>
                <p>Not satisfied? Our hassle-free return policy has you covered.</p>
                <ul class="service-features">
                    <li>14-day return window</li>
                    <li>Free returns for defective items</li>
                    <li>Instant refund processing</li>
                    <li>Dedicated returns support</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3>Customization</h3>
                <p>Make it uniquely yours with our customization options.</p>
                <ul class="service-features">
                    <li>Personalized engraving</li>
                    <li>Custom color combinations</li>
                    <li>Monogramming services</li>
                    <li>Bulk order customization</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Our customer care team is always ready to assist you.</p>
                <ul class="service-features">
                    <li>Phone, email and chat support</li>
                    <li>Product experts available</li>
                    <li>Quick response times</li>
                    <li>Multilingual support</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <h3>Gift Services</h3>
                <p>Perfect presents made easy with our gift services.</p>
                <ul class="service-features">
                    <li>Gift wrapping available</li>
                    <li>Personalized gift messages</li>
                    <li>Gift receipt options</li>
                    <li>Corporate gifting solutions</li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3>In-Store Experience</h3>
                <p>Visit our physical stores for an elevated shopping experience.</p>
                <ul class="service-features">
                    <li>Expert styling advice</li>
                    <li>VIP fitting rooms</li>
                    <li>Complimentary beverages</li>
                    <li>Exclusive in-store offers</li>
                </ul>
            </div>
        </section>

        <!-- Premium Services Section -->
        <section class="premium-services">
            <h2>BrandX Premium Services</h2>
            <div class="premium-grid">
                <div class="premium-card">
                    <h3>VIP Membership</h3>
                    <p>Exclusive access to limited editions, private sales, and personal shopping assistance.</p>
                </div>
                <div class="premium-card">
                    <h3>Same-Day Delivery</h3>
                    <p>Get your orders delivered within hours in select Nairobi areas.</p>
                </div>
                <div class="premium-card">
                    <h3>Product Care</h3>
                    <p>Professional cleaning and maintenance services for your purchases.</p>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works">
            <h2>How Our Services Work</h2>
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Place Your Order</h3>
                    <p>Select your items and choose your preferred services at checkout.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>We Process It</h3>
                    <p>Our team prepares your order with care and applies your selected services.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Delivery & Enjoy</h3>
                    <p>Receive your package and enjoy the BrandX experience.</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <h2>Ready to Experience BrandX Services?</h2>
            <p>Shop now and enjoy our premium services with every purchase.</p>
            <a href="products.php" class="cta-btn">Browse Products</a>
        </section>
    </div>

    <?php include('footer.php'); ?>

    <script>
        // Simple animation for service cards on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const serviceCards = document.querySelectorAll('.service-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            serviceCards.forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>
<?php
$connection->close();
?>