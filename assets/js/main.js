document.addEventListener('DOMContentLoaded', () => {

    // ---------- Elements ----------
    const productsGrid = document.getElementById('productsGrid');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartToggle = document.getElementById('cartToggle');
    const closeCart = document.getElementById('closeCart');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartCountSpan = document.getElementById('cartCount');
    const cartSubtotalSpan = document.getElementById('cartSubtotal');
    const cartDeliverySpan = document.getElementById('cartDelivery');
    const cartTotalSpan = document.getElementById('cartTotal');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const toastContainer = document.getElementById('toastContainer');

    const DELIVERY_FEE = 2000;

    // ---------- Load Cart Safely ----------
    let cart = [];
    function loadCart() {
        try {
            const saved = JSON.parse(localStorage.getItem('shadesArrayCart')) || [];
            cart = saved.filter(item => item.price && !isNaN(item.price) && item.price > 0 && item.quantity > 0);
            saveCart();
        } catch {
            cart = [];
            saveCart();
        }
    }

    function saveCart() {
        localStorage.setItem('shadesArrayCart', JSON.stringify(cart));
        updateCartCount();
    }

    function updateCartCount() {
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        if (cartCountSpan) cartCountSpan.textContent = count;
    }

    // ---------- Fetch Products ----------
    async function fetchProducts() {
        try {
            const res = await fetch('api/get_products.php');
            const data = await res.json();
            if (data.success) renderProducts(data.products);
            else showToast('Failed to load products.', 'error');
        } catch (err) {
            console.error(err);
            showToast('Network error. Refresh page.', 'error');
        }
    }

    function renderProducts(products) {
        if (!productsGrid) return;
        productsGrid.innerHTML = '';

        products.forEach(product => {
            const price = Number(product.price) || 0;
            const card = document.createElement('div');
            card.className = `product-card ${product.is_new ? 'new' : ''}`;
            card.innerHTML = `
                <img src="${product.image_url}" alt="${product.name}" class="product-image" loading="lazy">
                <div class="product-info">
                    <h3 class="product-name">${product.name}</h3>
                    <p class="product-description">${product.description}</p>
                    <p class="product-price">${product.price_formatted}</p>
                    <button class="add-to-cart" data-id="${product.id}" data-name="${product.name}" data-price="${price}" data-image="${product.image_url}">
                        Add to Cart
                    </button>
                </div>
            `;
            productsGrid.appendChild(card);
        });
    }

    // ---------- Event Delegation ----------
    document.addEventListener('click', e => {

        // Add to Cart
        if (e.target.classList.contains('add-to-cart')) {
            const btn = e.target;
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const price = Number(btn.dataset.price) || 0;
            const image = btn.dataset.image;

            if (!price) {
                showToast('Invalid product price.', 'error');
                return;
            }

            const existing = cart.find(i => i.id === id);
            if (existing) existing.quantity += 1;
            else cart.push({ id, name, price, image, quantity: 1 });

            saveCart();
            renderCart();
            showToast(`${name} added to cart.`, 'success');
            animateCartIcon();
        }

        // Increase Quantity
        if (e.target.classList.contains('qty-increase')) {
            const id = e.target.dataset.id;
            const item = cart.find(i => i.id === id);
            if (item) { item.quantity++; saveCart(); renderCart(); }
        }

        // Decrease Quantity
        if (e.target.classList.contains('qty-decrease')) {
            const id = e.target.dataset.id;
            const item = cart.find(i => i.id === id);
            if (item) {
                if (item.quantity > 1) item.quantity--;
                else cart = cart.filter(i => i.id !== id);
                saveCart(); renderCart();
            }
        }

        // Remove Item
        if (e.target.classList.contains('remove-item')) {
            const id = e.target.dataset.id;
            cart = cart.filter(i => i.id !== id);
            saveCart(); renderCart();
            showToast('Item removed.', 'info');
        }
    });

    // ---------- Render Cart ----------
    function renderCart() {
        if (!cartItemsContainer) return;
        if (!cart.length) {
            cartItemsContainer.innerHTML = '<p class="empty-cart">Your cart is empty.</p>';
            cartSubtotalSpan.textContent = '₦0.00';
            cartDeliverySpan.textContent = `₦${DELIVERY_FEE.toLocaleString()}`;
            cartTotalSpan.textContent = `₦${DELIVERY_FEE.toLocaleString()}`;
            updateCartCount();
            return;
        }

        let subtotal = 0;
        let html = '';
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            html += `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">₦${item.price.toLocaleString()}</div>
                        <div class="cart-item-quantity">
                            <button class="qty-decrease" data-id="${item.id}">−</button>
                            <span>${item.quantity}</span>
                            <button class="qty-increase" data-id="${item.id}">+</button>
                        </div>
                        <button class="remove-item" data-id="${item.id}">Remove</button>
                    </div>
                </div>
            `;
        });

        cartItemsContainer.innerHTML = html;

        const total = subtotal + DELIVERY_FEE;
        cartSubtotalSpan.textContent = `₦${subtotal.toLocaleString()}`;
        cartDeliverySpan.textContent = `₦${DELIVERY_FEE.toLocaleString()}`;
        cartTotalSpan.textContent = `₦${total.toLocaleString()}`;
        updateCartCount();
    }

    // ---------- Cart UI ----------
    function toggleCart(open) {
        if (!cartSidebar || !cartOverlay) return;
        cartSidebar.classList.toggle('open', open);
        cartOverlay.classList.toggle('active', open);
    }
    if (cartToggle) cartToggle.addEventListener('click', () => toggleCart(true));
    if (closeCart) closeCart.addEventListener('click', () => toggleCart(false));
    if (cartOverlay) cartOverlay.addEventListener('click', () => toggleCart(false));

    // ---------- Checkout via WhatsApp ----------
    function generateOrderId() {
        const d = new Date();
        return `ORD-${d.getFullYear()}${String(d.getMonth()+1).padStart(2,'0')}${String(d.getDate()).padStart(2,'0')}-${Math.floor(Math.random()*9000+1000)}`;
    }

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {

            if (!cart.length) { showToast('Your cart is empty.', 'error'); return; }

            const orderId = generateOrderId();
            let subtotal = 0;
            let itemsText = '';

            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                itemsText += `${item.name} x${item.quantity} - ₦${itemTotal.toLocaleString()}\n`;
            });

            const total = subtotal + DELIVERY_FEE;

            // ✅ Proper encoding for WhatsApp
            const message = encodeURIComponent(
`Order ID: ${orderId}
Items:
${itemsText}
Subtotal: ₦${subtotal.toLocaleString()}
Delivery: ₦${DELIVERY_FEE.toLocaleString()}
Total: ₦${total.toLocaleString()}

Thank you for shopping with Shades Array!`
            );

            const phoneNumber = '2348032498333';
            window.open(`https://wa.me/${phoneNumber}?text=${message}`, '_blank');
        });
    }

    // ---------- Toast ----------
    function showToast(msg, type='info') {
        if (!toastContainer) return;
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = msg;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function animateCartIcon() {
        if (!cartToggle) return;
        cartToggle.classList.add('pop');
        setTimeout(() => cartToggle.classList.remove('pop'), 300);
    }

    // ---------- Init ----------
    loadCart();
    fetchProducts();
    renderCart();
});