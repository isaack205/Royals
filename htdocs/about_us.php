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
    <title>About BrandX - Premium Footwear & Accessories</title>
    <meta name="description" content="Discover the story behind BrandX - Kenya's premier destination for luxury footwear and accessories. Learn about our mission, values, and commitment to quality.">
    <style>
        :root {
            --about-primary: #00d2ff;
            --about-secondary: #2ed573;
            --about-dark: #212529;
            --about-light: #f8f9fa;
            --about-shadow: 0 15px 30px rgba(0,0,0,0.1);
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
        .about-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Hero Section */
        .about-hero {
            position: relative;
            height: 60vh;
            min-height: 400px;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/about-hero.jpg') center/cover no-repeat;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 3rem;
            overflow: hidden;
        }

        .about-hero-content {
            max-width: 800px;
            padding: 2rem;
            z-index: 2;
        }

        .about-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s ease;
        }

        .about-hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Story Section */
        .story-section {
            display: flex;
            align-items: center;
            gap: 3rem;
            margin-bottom: 4rem;
        }

        .story-content {
            flex: 1;
        }

        .story-image {
            flex: 1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--about-shadow);
        }

        .story-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.5s ease;
        }

        .story-image:hover img {
            transform: scale(1.05);
        }

        .section-title {
            font-size: 2.5rem;
            color: var(--about-primary);
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50%;
            height: 3px;
            background-color: var(--about-primary);
        }

        /* Mission Section */
        .mission-section {
            background-color: rgba(0, 210, 255, 0.1);
            padding: 4rem 2rem;
            border-radius: 12px;
            margin-bottom: 4rem;
            text-align: center;
        }

        .mission-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .mission-card {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: var(--about-shadow);
            transition: transform var(--transition-speed) ease;
        }

        .mission-card:hover {
            transform: translateY(-10px);
        }

        .mission-card h3 {
            color: var(--about-primary);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        /* Team Section */
        .team-section {
            margin-bottom: 4rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .team-member {
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: var(--about-shadow);
        }

        .team-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }

        .team-member:hover .team-image {
            transform: scale(1.1);
        }

        .team-info {
            padding: 1.5rem;
            background-color: var(--card-bg);
        }

        .team-info h3 {
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        .team-info p {
            color: var(--about-primary);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-links a {
            color: var(--text-secondary);
            transition: color var(--transition-speed) ease;
        }

        .social-links a:hover {
            color: var(--about-primary);
        }

        /* Values Section */
        .values-section {
            margin-bottom: 4rem;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .value-card {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: var(--about-shadow);
            text-align: center;
            transition: transform var(--transition-speed) ease;
        }

        .value-card:hover {
            transform: translateY(-10px);
        }

        .value-icon {
            font-size: 2.5rem;
            color: var(--about-primary);
            margin-bottom: 1.5rem;
        }

        .value-card h3 {
            margin-bottom: 1rem;
            color: var(--text);
        }

        /* CTA Section */
        .about-cta {
            background: linear-gradient(135deg, var(--about-primary), #3a7bd5);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 3rem;
        }

        .about-cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .about-cta p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        .cta-btn {
            display: inline-block;
            background-color: white;
            color: var(--about-primary);
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
            .story-section {
                flex-direction: column;
            }
            
            .about-hero h1 {
                font-size: 2.8rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .about-hero {
                height: 50vh;
            }
            
            .about-hero h1 {
                font-size: 2.2rem;
            }
            
            .about-hero p {
                font-size: 1.1rem;
            }
            
            .about-cta h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .about-hero {
                height: 60vh;
                min-height: 300px;
            }
            
            .about-hero h1 {
                font-size: 1.8rem;
            }
            
            .about-cta {
                padding: 3rem 1rem;
            }
            
            .about-cta h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="about-container">
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="about-hero-content">
                <h1>Our Story</h1>
                <p>From humble beginnings to becoming Kenya's premier destination for luxury footwear and accessories</p>
            </div>
        </section>

        <!-- Story Section -->
        <section class="story-section">
            <div class="story-content">
                <h2 class="section-title">How It All Began</h2>
                <p>Founded in 2015, BrandX started as a small boutique in Nairobi with a simple mission: to bring high-quality, stylish footwear to the Kenyan market. Our founder, Jane Muthoni, noticed a gap in the market for premium footwear that combined international trends with local sensibilities.</p>
                <p>What began as a single-store operation has now grown into a multi-channel retail experience with physical stores across major Kenyan cities and a thriving e-commerce platform serving customers throughout East Africa.</p>
                <p>Today, we're proud to partner with over 50 international brands while also nurturing local design talent through our BrandX Emerging Designers program.</p>
            </div>
            <div class="story-image">
                <img src="images/about-story.jpg" alt="BrandX first store opening">
            </div>
        </section>

        <!-- Mission Section -->
        <section class="mission-section">
            <h2 class="section-title">Our Mission & Vision</h2>
            <p>We exist to elevate your style while delivering exceptional value and service at every touchpoint.</p>
            
            <div class="mission-cards">
                <div class="mission-card">
                    <h3>Mission</h3>
                    <p>To curate the finest selection of footwear and accessories while providing an unparalleled shopping experience that celebrates individuality and style.</p>
                </div>
                <div class="mission-card">
                    <h3>Vision</h3>
                    <p>To become East Africa's most trusted authority in premium footwear and accessories, recognized for our quality, innovation, and customer service.</p>
                </div>
                <div class="mission-card">
                    <h3>Promise</h3>
                    <p>Every BrandX product meets our rigorous standards for quality, comfort, and style, backed by our commitment to your complete satisfaction.</p>
                </div>
            </div>
        </section>

  

        <!-- Values Section -->
        <section class="values-section">
            <h2 class="section-title">Our Core Values</h2>
            <p>The principles that guide everything we do</p>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Quality</h3>
                    <p>We never compromise on quality, from materials to craftsmanship to customer service.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Passion</h3>
                    <p>Our love for footwear and accessories drives us to deliver exceptional experiences.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Innovation</h3>
                    <p>We constantly seek new ways to improve and stay ahead of trends.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Community</h3>
                    <p>We believe in building relationships, not just making sales.</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="about-cta">
            <h2>Become Part of Our Story</h2>
            <p>Join thousands of satisfied customers who trust BrandX for their footwear and accessory needs.</p>
            <a href="products.php" class="cta-btn">Shop Now</a>
        </section>
    </div>

    <?php include('footer.php'); ?>

    <script>
        // Simple animation for elements on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.story-content, .mission-card, .team-member, .value-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            animateElements.forEach(element => {
                element.style.opacity = 0;
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'all 0.5s ease';
                observer.observe(element);
            });
        });
    </script>
</body>
</html>
<?php
$connection->close();
?>