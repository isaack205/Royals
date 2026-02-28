    <footer>
    <style>
    /* Add this to your existing CSS */
footer {
     margin-top: 3rem;
        border-top: 1px solid rgba(0,20,10,0.5);

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding-bottom: 2rem;
}

.footer-section {
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.footer-section h3 {
    color: var(--accent);
    font-size: 1.2rem;
    margin-bottom: 1rem;
    font-family: 'Exo', sans-serif;
    position: relative;
    padding-bottom: 10px;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 2px;
    background-color: var(--accent);
}

.footer-section p {
    line-height: 1.6;
    margin-bottom: 1rem;
}

.footer-section a {
    display: block;
    color: var(--text-secondary);
    margin-bottom: 0.8rem;
    text-decoration: none;
    transition: color 0.3s, transform 0.3s;
}

.footer-section a:hover {
    color: var(--accent);
    transform: translateX(5px);
}

.footer-section i {
    margin-right: 8px;
    width: 20px;
    text-align: center;
}

.social-icons a {
    display: inline-block;
    font-size: 1.5rem;
    color: var(--text-secondary);
    transition: all 0.3s;
    padding: 0 20px;
}

.social-icons a:hover {
    color: var(--accent);
    transform: translateY(-3px);
}

.copyright {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.copyright a {
    color: var(--accent);
    text-decoration: none;
    transition: opacity 0.3s;
}

.copyright a:hover {
    opacity: 0.8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr 1fr;
    }
    
    .footer-section {
        margin-bottom: 1rem;
    }

    .social-icons a {
        padding: 0 3px;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .footer-content {
        grid-template-columns: 1fr;
    }
    
    footer {
        padding: 2rem 1.5rem 1rem;
    }
    
    .copyright {
        font-size: 0.8rem;
        line-height: 1.5;
    }

    .social-icons a {
        padding: 0 3px;
        font-size: 0.9rem;
    }
}
    </style>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <div class="footer-content">
            <!-- <div class="footer-section">
                <h3>About ROYALS</h3>
                <p>Your premier destination for quality sneakers in Kenya. We offer the latest styles from top brands at affordable prices.</p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="home.php">Home</a>
                <a href="products.php">Products</a>
                <a href="about_us.php">About Us</a>
                <a href="contact.php">Contact</a>
            </div>
            
            <div class="footer-section">
                <h3>Customer Service</h3>
                <a href="faq.php">FAQs</a>
                <a href="shipping.php">Shipping Policy</a>
                <a href="returns.php">Returns & Refunds</a>
                <a href="privacy.php">Privacy Policy</a>
            </div> -->
            
            <div class="footer-section">
                <h3>Contact Us</h3>
                <div class="social-icons" style="margin-top: 10px;">
                    <a href="tel:+254703301003" target="_blank" style="margin-right: 10px;"><i class="fas fa-phone-alt"></i></a>
                    <a href="mailto:info@royals.co.ke" target="_blank" style="margin-right: 10px;"><i class="fas fa-envelope"></i></a>
                    <a href="https://www.instagram.com/royals.ke/" target="_blank" style="margin-right: 10px;"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/254703301003" target="_blank" style="margin-right: 10px;"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/profile.php?id=61587283432306" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.tiktok.com/@royals.ke" target="_blank"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            &copy; 2024-2025 Royals Online Store | All Rights Reserved <br>
            Developed and maintained by <a href="https://wa.me/254773743248" target="_blank">Simon Ngugi</a>
        </div>
    </footer>
    
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
    
    <script>
    // Variables for slider
    const slides = document.querySelectorAll('.slide');
    const slider = document.querySelector('.slider');
    const totalSlides = slides.length;
    let currentIndex = 0;
    let slideInterval;
    let isAdPlaying = false;
    
    // Initialize slider
    function initSlider() {
        // Set initial transform
        slider.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        // Set up event listeners for navigation buttons
        document.querySelector('.prev')?.addEventListener('click', () => {
            clearInterval(slideInterval);
            goToSlide(currentIndex - 1);
            slideInterval = setInterval(autoSlide, 6000);
        });
        
        document.querySelector('.next')?.addEventListener('click', () => {
            clearInterval(slideInterval);
            goToSlide(currentIndex + 1);
            slideInterval = setInterval(autoSlide, 6000);
        });
        
        // Start auto sliding
        slideInterval = setInterval(autoSlide, 6000);
    }
    
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
        
        // Check if it's an ad and handle it
        const currentSlide = slides[currentIndex];
        const video = currentSlide?.querySelector('video');
        if (video) {
            isAdPlaying = true;
            video.play().catch(e => console.log("Autoplay prevented:", e));
            video.onended = function() {
                isAdPlaying = false;
                autoSlide();
            };
        } else {
            isAdPlaying = false;
        }
    }
    
    function autoSlide() {
        // If an ad is playing, don't auto slide
        if (!isAdPlaying) {
            goToSlide(currentIndex + 1);
        }
    }
    
    // Initialize the slider if it exists on the page
    if (slider) {
        initSlider();
    }
    
    // Remove blue highlight on tap/click
    document.addEventListener('touchstart', function() {}, {passive: true});
    
    // Brand filtering functionality
    function filterByBrand(brand) {
        window.location.href = `home.php?brand=${brand}`;
    }
    
    // Section filtering functionality
    function filterBySection(section) {
        window.location.href = `home.php?section=${section}`;
    }
    </script>
</body>
</html>