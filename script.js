function toggleMenu() {
    const menu = document.getElementById('menu');
    const hamMenu = document.querySelector('.ham-menu');
    menu.classList.toggle('active');
    hamMenu.classList.toggle('active');
}
// script.js

// Variables
let currentIndex = 0;
const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;
const slider = document.querySelector('.slider');

// Show the next slide
function nextSlide() {
  currentIndex = (currentIndex + 1) % totalSlides;
  updateSlidePosition();
}

// Show the previous slide
function prevSlide() {
  currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
  updateSlidePosition();
}

// Update the slider position based on current index
function updateSlidePosition() {
  slider.style.transform = `translateX(-${currentIndex * 100}%)`;
}

// Automatic slide change every 5 seconds
setInterval(nextSlide, 5000);

// Event listeners for navigation buttons
document.querySelector('.prev').addEventListener('click', prevSlide);
document.querySelector('.next').addEventListener('click', nextSlide);


// Fetch data from the PHP endpoint

// Function to fetch and update the cart count
async function updateCartCount() {
    try {
        const response = await fetch('cart.php?action=count'); // Request cart count from PHP
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json(); // Parse the JSON response
        document.getElementById('cart-count').innerText = data.count; // Update the cart icon count
    } catch (error) {
        console.error('Error fetching cart count:', error); // Log any errors
    }
}

// Call the function to initialize cart count on page load
window.onload = updateCartCount;
