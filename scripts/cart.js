// Cart functionality
class ShoppingCart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cartItems')) || [];
        this.total = 0;
        this.count = 0;
        this.calculateTotal();
        this.setupEventListeners();
        this.updateCartUI();
    }

    setupEventListeners() {

        // Toggle cart visibility
        document.querySelector('.cart-toggle').addEventListener('click', () => {
            const cartContainer = document.querySelector('.cart-container');
            const cartOverlay = document.querySelector('.cart-overlay');

            cartContainer.classList.toggle('active');
            cartContainer.classList.remove('hidden');

            cartOverlay.classList.toggle('active');
            cartOverlay.classList.remove('hidden');
        });

        // Also update the close cart functions to re-add the hidden class
        document.querySelector('.cart-overlay').addEventListener('click', () => {
            document.querySelector('.cart-container').classList.remove('active');
            document.querySelector('.cart-overlay').classList.remove('active');
        });

        document.querySelector('.cart-close').addEventListener('click', () => {
            document.querySelector('.cart-container').classList.remove('active');
            document.querySelector('.cart-overlay').classList.remove('active');
        });

        // Checkout button
        document.querySelector('.checkout-btn').addEventListener('click', () => {
            if (this.items.length === 0) {
                alert('Your cart is empty. Add some products first.');
                return;
            }

            // Save order to localStorage
            const order = {
                id: Date.now(),
                items: this.items,
                total: this.total,
                date: new Date().toISOString(),
                status: 'pending'
            };

            const orders = JSON.parse(localStorage.getItem('orders') || '[]');
            orders.push(order);
            localStorage.setItem('orders', JSON.stringify(orders));

            // Clear cart
            this.items = [];
            localStorage.setItem('cartItems', JSON.stringify(this.items));
            this.calculateTotal();
            this.updateCartUI();

            // Show confirmation
            alert('Thank you for your order! We will process it shortly.');

            // Close cart
            document.querySelector('.cart-container').classList.remove('active');
            document.querySelector('.cart-overlay').classList.remove('active');
        });

        // WhatsApp order button
        document.querySelector('.whatsapp-order-btn').addEventListener('click', () => {
            const message = this.generateWhatsAppOrderMessage();
            const adminPhoneNumber = "94757997430"; // Admin's phone number
            const url = `https://wa.me/${adminPhoneNumber}?text=${message}`;
            window.open(url, '_blank');
        });
    }

    addItem(product, quantity = 1) {
        // Check if product already exists in cart
        const existingItem = this.items.find(item => item.id === product.id);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({
                ...product,
                quantity: quantity
            });
        }

        this.saveToLocalStorage();
        this.calculateTotal();
        this.updateCartUI();

        // Show feedback
        this.showAddToCartFeedback();
    }

    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveToLocalStorage();
        this.calculateTotal();
        this.updateCartUI();
    }

    updateQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);

        if (item) {
            if (quantity <= 0) {
                this.removeItem(productId);
                return;
            }

            item.quantity = quantity;
            this.saveToLocalStorage();
            this.calculateTotal();
            this.updateCartUI();
        }
    }

    calculateTotal() {
        this.total = this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        this.count = this.items.reduce((count, item) => count + item.quantity, 0);
    }

    saveToLocalStorage() {
        localStorage.setItem('cartItems', JSON.stringify(this.items));
    }

    updateCartUI() {
        const cartItemsEl = document.querySelector('.cart-items');
        const cartCountEl = document.querySelector('.cart-badge');
        const cartTotalEl = document.querySelector('.cart-total-amount');

        // Update cart count in badge
        cartCountEl.textContent = this.count;
        cartCountEl.style.display = 'flex';

        // Update cart total
        cartTotalEl.textContent = this.formatCurrency(this.total);

        // Update items list
        cartItemsEl.innerHTML = '';

        if (this.items.length === 0) {
            cartItemsEl.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                    <p>Your cart is empty</p>
                    <p style="margin-top: 10px; font-size: 0.9rem;">Add some products to your cart and they will appear here.</p>
                </div>
            `;
            return;
        }

        this.items.forEach(item => {
            const cartItemEl = document.createElement('div');
            cartItemEl.className = 'cart-item';

            cartItemEl.innerHTML = `
                <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${this.formatCurrency(item.price)}</div>
                    <div class="cart-item-controls">
                        <button class="quantity-btn decrease" data-id="${item.id}">-</button>
                        <input type="number" class="quantity-input" value="${item.quantity}" data-id="${item.id}" min="1" max="99">
                        <button class="quantity-btn increase" data-id="${item.id}">+</button>
                    </div>
                </div>
                <button class="cart-item-remove" data-id="${item.id}">
                    <i class="fas fa-trash"></i>
                </button>
            `;

            cartItemsEl.appendChild(cartItemEl);
        });

        // Setup quantity buttons
        document.querySelectorAll('.quantity-btn.decrease').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.dataset.id);
                const item = this.items.find(item => item.id === id);
                if (item) {
                    this.updateQuantity(id, item.quantity - 1);
                }
            });
        });

        document.querySelectorAll('.quantity-btn.increase').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.dataset.id);
                const item = this.items.find(item => item.id === id);
                if (item) {
                    this.updateQuantity(id, item.quantity + 1);
                }
            });
        });

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', () => {
                const id = parseInt(input.dataset.id);
                const quantity = parseInt(input.value);
                this.updateQuantity(id, quantity);
            });
        });

        // Setup remove buttons
        document.querySelectorAll('.cart-item-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseInt(btn.dataset.id);
                this.removeItem(id);
            });
        });
    }

    showAddToCartFeedback() {
        const feedback = document.createElement('div');
        feedback.className = 'add-to-cart-feedback';
        feedback.textContent = 'Product added to cart!';
        document.body.appendChild(feedback);

        // Add animation classes
        setTimeout(() => {
            feedback.classList.add('active');
        }, 10);

        // Remove after animation
        setTimeout(() => {
            feedback.classList.remove('active');
            setTimeout(() => {
                document.body.removeChild(feedback);
            }, 300);
        }, 2000);
    }

    formatCurrency(amount) {
        return 'LKR ' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    clearCart() {
        this.items = [];
        this.saveToLocalStorage();
        this.calculateTotal();
        this.updateCartUI();
    }

    // Generate WhatsApp order message with cart details
    generateWhatsAppOrderMessage() {
        if (this.items.length === 0) {
            return "I'm interested in placing an order";
        }

        let message = "Hello! I would like to place an order for the following items:\n\n";

        this.items.forEach((item, index) => {
            message += `${index + 1}. ${item.name} - ${item.quantity} x LKR ${item.price.toFixed(2)} = LKR ${(item.quantity * item.price).toFixed(2)}\n`;
        });

        message += `\nTotal: LKR ${this.total.toFixed(2)}`;
        message += "\n\nPlease let me know how to proceed with payment and delivery. Thank you!";

        return encodeURIComponent(message);
    }
}

// Initialize cart once DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.cart = new ShoppingCart();

    // Add to cart button click handler for product pages
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            // Get product details from data attributes or parent elements
            const productCard = this.closest('.product-card');
            if (!productCard) return;

            const productId = parseInt(productCard.dataset.id);
            const products = JSON.parse(localStorage.getItem('products') || '[]');
            const product = products.find(p => p.id === productId);

            if (product) {
                window.cart.addItem(product);
            }
        });
    });
});

// Add this code at the end of your cart.js file or in a separate script

document.addEventListener('DOMContentLoaded', function() {
    // Wait a short moment after DOM is loaded to ensure cart is fully initialized
    setTimeout(() => {
        // Force a cart UI update
        if (window.cart) {
            // Recalculate the cart total and count
            window.cart.calculateTotal();
            window.cart.updateCartUI();

            // Add debug log to check the count
            console.log('Cart count after delayed update:', window.cart.count);

            // Force display if items exist
            const cartCountEl = document.querySelector('.cart-badge');
            if (window.cart.count > 0) {
                cartCountEl.style.display = 'flex';
                cartCountEl.textContent = window.cart.count;
            }
        }
    }, 500); // 500ms delay
});

// Add this to your cart.js file
document.addEventListener('DOMContentLoaded', function() {
    // Make sure the cart is initialized first
    if (window.cart) {
        // Force immediate update when DOM loads
        updateCartBadge();
    }

    // Add a small delay to ensure everything is loaded
    setTimeout(updateCartBadge, 100);

    function updateCartBadge() {
        const cartCountEl = document.querySelector('.cart-badge');
        if (!cartCountEl) return;

        const count = window.cart ? window.cart.count : 0;
        cartCountEl.textContent = count;

        // Always show the badge, even when count is 0
        cartCountEl.style.display = 'flex';
    }
});