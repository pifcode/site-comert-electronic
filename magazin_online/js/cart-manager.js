// Funcție pentru gestionarea coșului de cumpărături
const cartManager = {
    // Inițializare
    init: function() {
        this.updateCartCounter();
        this.setupEventListeners();
    },

    // Setare event listeners
    setupEventListeners: function() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn')) {
                const button = e.target;
                const productData = {
                    id: parseInt(button.dataset.id),
                    name: button.dataset.name,
                    price: parseFloat(button.dataset.price),
                    image: button.dataset.image,
                    quantity: 1
                };
                this.addToCart(productData);
                this.showNotification(`${productData.name} a fost adăugat în coș!`);
            }
        });
    },

    // Adăugare produs în coș
    addToCart: function(product) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Verificăm dacă produsul există deja în coș
        const existingProductIndex = cart.findIndex(item => item.id === product.id);
        
        if (existingProductIndex > -1) {
            // Dacă produsul există, incrementăm cantitatea
            cart[existingProductIndex].quantity += 1;
        } else {
            // Dacă produsul nu există, îl adăugăm cu cantitatea 1
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: 1
            });
        }
        
        // Salvăm coșul actualizat
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Actualizăm contorul coșului
        this.updateCartCounter();
        
        // Afișăm notificarea
        this.showNotification('Produsul a fost adăugat în coș!');
    },

    // Obținere coș din localStorage
    getCart: function() {
        return JSON.parse(localStorage.getItem('cart')) || [];
    },

    // Actualizare contor coș
    updateCartCounter: function() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        const counter = document.getElementById('cart-counter');
        if (counter) {
            counter.textContent = totalItems;
            counter.style.display = totalItems > 0 ? 'inline-block' : 'none';
        }
    },

    // Ștergere produs din coș
    removeFromCart: function(productId) {
        let cart = this.getCart();
        cart = cart.filter(item => item.id !== productId);
        localStorage.setItem('cart', JSON.stringify(cart));
        this.updateCartCounter();
        
        // Verificăm dacă suntem pe pagina coșului și actualizăm afișarea
        if (typeof updateCartDisplay === 'function') {
            updateCartDisplay();
        }
    },

    // Actualizare cantitate produs
    updateQuantity: function(productId, newQuantity) {
        let cart = this.getCart();
        const product = cart.find(item => item.id === productId);
        
        if (product) {
            if (newQuantity > 0) {
                product.quantity = newQuantity;
            } else {
                cart = cart.filter(item => item.id !== productId);
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            this.updateCartCounter();
            
            // Verificăm dacă suntem pe pagina coșului și actualizăm afișarea
            if (typeof updateCartDisplay === 'function') {
                updateCartDisplay();
            }
        }
    },

    // Golire coș
    clearCart: function() {
        localStorage.removeItem('cart');
        this.updateCartCounter();
        
        // Verificăm dacă suntem pe pagina coșului și actualizăm afișarea
        if (typeof updateCartDisplay === 'function') {
            updateCartDisplay();
        }
    },

    // Afișare notificare
    showNotification: function(message) {
        // Eliminăm orice notificare existentă
        const existingNotification = document.querySelector('.cart-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Creăm și afișăm noua notificare
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.textContent = message;
        document.body.appendChild(notification);

        // Eliminăm notificarea după 3 secunde
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
};

// Inițializare cart manager când se încarcă pagina
document.addEventListener('DOMContentLoaded', () => {
    cartManager.init();
});
