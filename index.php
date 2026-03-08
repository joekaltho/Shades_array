<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shades Array | Luxury Sunglasses</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Floating decorative sunglasses (CSS absolute element) -->
    <div class="floating-sunglasses" aria-hidden="true">👓</div>

    <!-- Cart Sidebar Toggle -->
    <button class="cart-toggle" id="cartToggle" aria-label="Open cart">🛒 <span id="cartCount">0</span></button>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>Your Cart</h3>
            <button id="closeCart" aria-label="Close cart">&times;</button>
        </div>
        <div class="cart-items" id="cartItems"></div>
        <div class="cart-footer">
            <p>Subtotal: <span id="cartSubtotal">₦0.00</span></p>
            <p>Delivery: <span id="cartDelivery">₦2,000.00</span></p>
            <p class="cart-total">Total: <span id="cartTotal">₦0.00</span></p>
            <button class="btn-checkout" id="checkoutBtn">Checkout via WhatsApp</button>
        </div>
    </div>
    <div class="cart-overlay" id="cartOverlay"></div>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1 class="hero-title">See the World in <span class="gold">Style</span></h1>
                <p class="hero-subtitle">premiume sunglasses for the discerning</p>
                <a href="#products" class="btn btn-primary">Explore Collection</a>
            </div>
            <div class="hero-overlay"></div>
        </section>

        <!-- Products Grid Section -->
        <section id="products" class="products-section">
            <div class="container">
                <h2 class="section-title">Our Collection</h2>
                <div class="products-grid" id="productsGrid"></div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-section">
            <div class="container about-container">
                <div class="about-text">
                    <h2 class="section-title">Artistry in Every Pair</h2>
                   <p>Shades Array offers stylish, high-quality sunglasses designed for modern lifestyles.
                     Sleek designs, vibrant accents, and reliable UV protection make every pair a statement piece.</p>
                </div>
                <div class="about-image">
                    <img src="assets/images/about-sunglasses.png" alt="Luxury sunglasses craftsmanship" loading="lazy">
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="contact-section">
            <div class="container">
                <h2 class="section-title">Concierge</h2>
                <p class="contact-intro">For personal assistance, reach out to us.</p>
                <div class="contact-details">
                    <p>Email: <a href="mailto:shadesarray@gmail.com">shadesarray@gmail.com</a></p>
                    <p>Phone: <a href="tel:+2348032498333">+234 803 249 8333</a></p>
                    <p>Lagos · Plateau · Gombe </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Elegant Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Shades Array. see the world in style <br>designed and developed by kaltrix.</p>
        </div>
    </footer>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="assets/js/main.js"></script>
</body>
</html>
