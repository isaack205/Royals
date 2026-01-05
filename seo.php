<?php
// Set the content type to HTML
header("Content-Type: text/html; charset=UTF-8");

// Prevent this page from being indexed directly
header("X-Robots-Tag: noindex", true);

// Add meta tags and structured data
echo <<<HTML
<!DOCTYPE html>
<html lang="en-KE" dir="ltr">
<head>
    <!-- Basic Meta Tags -->
        <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">

                    <!-- Page Title -->
                        <title>BrandX.co.ke | Affordable Sneakers in Kenya</title>

                            <!-- Meta Description -->
                                <meta name="description" content="Shop the latest sneakers in Kenya at BrandX.co.ke. Find affordable Nike, Adidas, and Puma sneakers with free delivery nationwide.">

                                    <!-- Canonical URL -->
                                        <link rel="canonical" href="https://www.brandx.co.ke/" />

                                            <!-- Open Graph Meta Tags (for Social Media) -->
                                                <meta property="og:title" content="BrandX.co.ke | Affordable Sneakers in Kenya">
                                                    <meta property="og:description" content="Shop the latest sneakers in Kenya at BrandX.co.ke. Find affordable Nike, Adidas, and Puma sneakers with free delivery nationwide.">
                                                        <meta property="og:image" content="https://www.brandx.co.ke/images/brandx-sneakers.jpg">
                                                            <meta property="og:url" content="https://www.brandx.co.ke/">
                                                                <meta property="og:type" content="website">

                                                                    <!-- Twitter Card Meta Tags -->
                                                                        <meta name="twitter:card" content="summary_large_image">
                                                                            <meta name="twitter:title" content="BrandX.co.ke | Affordable Sneakers in Kenya">
                                                                                <meta name="twitter:description" content="Shop the latest sneakers in Kenya at BrandX.co.ke. Find affordable Nike, Adidas, and Puma sneakers with free delivery nationwide.">
                                                                                    <meta name="twitter:image" content="https://www.brandx.co.ke/images/brandx-sneakers.jpg">

                                                                                        <!-- Structured Data (Schema.org) -->
                                                                                            <script type="application/ld+json">
                                                                                                {
                                                                                                      "@context": "https://schema.org",
                                                                                                            "@type": "WebSite",
                                                                                                                  "name": "BrandX.co.ke",
                                                                                                                        "url": "https://www.brandx.co.ke/",
                                                                                                                              "description": "Shop the latest sneakers in Kenya at BrandX.co.ke. Find affordable Nike, Adidas, and Puma sneakers with free delivery nationwide.",
                                                                                                                                    "potentialAction": {
                                                                                                                                            "@type": "SearchAction",
                                                                                                                                                    "target": "https://www.brandx.co.ke/search?q={search_term_string}",
                                                                                                                                                            "query-input": "required name=search_term_string"
                                                                                                                                                                  }
                                                                                                                                                                      }
                                                                                                                                                                          </script>
                                                                                                                                                                          </head>
                                                                                                                                                                          <body>
                                                                                                                                                                              <!-- Hidden Content for SEO -->
                                                                                                                                                                                  <div style="display:none;">
                                                                                                                                                                                          <h1>BrandX.co.ke - Affordable Sneakers in Kenya</h1>
                                                                                                                                                                                                  <p>Shop the latest sneakers in Kenya at BrandX.co.ke. Find affordable Nike, Adidas, and Puma sneakers with free delivery nationwide.</p>
                                                                                                                                                                                                          <ul>
                                                                                                                                                                                                                      <li><a href="https://www.brandx.co.ke/men-sneakers">Men's Sneakers</a></li>
                                                                                                                                                                                                                                  <li><a href="https://www.brandx.co.ke/women-sneakers">Women's Sneakers</a></li>
                                                                                                                                                                                                                                              <li><a href="https://www.brandx.co.ke/latest-sneakers">Latest Sneakers</a></li>
                                                                                                                                                                                                                                                      </ul>
                                                                                                                                                                                                                                                          </div>
                                                                                                                                                                                                                                                          </body>
                                                                                                                                                                                                                                                          </html>
                                                                                                                                                                                                                                                          HTML;
                                                                                                                                                                                                                                                          ?>