<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | BrandX</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
    /* Include all your existing CSS variables and base styles from the main page */
    :root {
        --primary: white;
        --primary-light: #15253f;
        --secondary: #3a7bd5;
        --accent: #00d2ff;
        --text: #333;
        --text-secondary: #666;
        --footer-dark: #080f1a;
        --background: white;
        --card-bg: white;
        --border-color: #e0e0e0;
    }

    [data-theme="dark"] {
        --primary: #080f1a;
        --primary-light: #15253f;
        --secondary: #3a7bd5;
        --accent: #00d2ff;
        --text: #f5f5f5;
        --text-secondary: #cccccc;
        --footer-dark: #080f1a;
        --background: #080f1a;
        --card-bg: #0e1726;
        --border-color: #1e293b;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--background);
        color: var(--text);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: background-color 0.3s, color 0.3s;
    }

    /* 404 Page Specific Styles */
    .error-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        padding: 3rem 2rem;
        text-align: center;
    }

    .error-content {
        max-width: 600px;
        margin: 0 auto;
    }

    .error-code {
        font-size: 8rem;
        font-weight: 700;
        color: var(--accent);
        margin-bottom: 1rem;
        line-height: 1;
        font-family: 'Montserrat', sans-serif;
        text-shadow: 0 5px 15px rgba(0, 210, 255, 0.3);
    }

    .error-title {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--text);
    }

    .error-message {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .error-image {
        width: 100%;
        max-width: 400px;
        margin: 2rem auto;
        filter: drop-shadow(0 10px 15px rgba(0, 210, 255, 0.2));
    }

    .btn-home {
        display: inline-block;
        padding: 12px 30px;
        background-color: var(--accent);
        color: white;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 2px solid var(--accent);
        margin-top: 1rem;
        box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
    }

    .btn-home:hover {
        background-color: transparent;
        color: var(--accent);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 210, 255, 0.4);
    }

    .sneaker-animation {
        position: relative;
        width: 200px;
        height: 100px;
        margin: 2rem auto;
    }

    .sneaker {
        position: absolute;
        width: 80px;
        height: 40px;
        background-color: var(--accent);
        border-radius: 10px;
        animation: bounce 2s infinite ease-in-out;
    }

    .sneaker:nth-child(2) {
        left: 60px;
        animation-delay: 0.2s;
        opacity: 0.8;
    }

    .sneaker:nth-child(3) {
        left: 120px;
        animation-delay: 0.4s;
        opacity: 0.6;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-30px);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .error-code {
            font-size: 6rem;
        }
        
        .error-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 480px) {
        .error-code {
            font-size: 5rem;
        }
        
        .error-title {
            font-size: 1.5rem;
        }
        
        .error-message {
            font-size: 1rem;
        }
        
        .btn-home {
            padding: 10px 25px;
        }
    }

    /* Include your existing header/footer styles here */
    </style>
</head>
<body>

    <!-- 404 Content -->
    <main class="error-container">
        <div class="error-content">
            <div class="error-code">404</div>
            <h1 class="error-title">Oops! Page Not Found</h1>
            <p class="error-message">
                The page you're looking for doesn't exist or has been moved. 
                Don't worry though, we've got plenty of fresh sneakers waiting for you!
            </p>
            
            <div class="sneaker-animation">
                <div class="sneaker"></div>
                <div class="sneaker"></div>
                <div class="sneaker"></div>
            </div>
            
            <a href="index.php" class="btn-home">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>
    </main>
<script>
    // Your existing theme toggle and other JS functionality
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    // Check for saved theme preference or use system preference
    const savedTheme = localStorage.getItem('theme') || 'system';
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (savedTheme === 'system' && systemDark)) {
        html.setAttribute('data-theme', 'dark');
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    } else {
        html.setAttribute('data-theme', 'light');
        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    }

    themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-theme');
        
        if (currentTheme === 'dark') {
            html.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
            themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        } else {
            html.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }
    });

    // Mobile menu toggle function
    function toggleMenu() {
        const menu = document.getElementById('menu');
        const hamMenu = document.querySelector('.ham-menu');
        menu.classList.toggle('active');
        hamMenu.classList.toggle('active');
        
        document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
    }
    </script>
</body>
</html>