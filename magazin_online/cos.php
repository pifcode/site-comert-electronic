<?php
session_start();
require_once "config/database.php";
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coș de cumpărături - Pescarul Expert</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="imagini/logo.png" alt="Logo Pescarul Expert" class="logo">
                <span class="slogan">Prinde aventura cu echipamentul nostru!</span>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Acasă</a></li>
                    <li><a href="produse.php">Produse</a></li>
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <li><a href="profil.php">Profil (<?php echo htmlspecialchars($_SESSION["username"]); ?>)</a></li>
                        <li><a href="auth/logout.php">Deconectare</a></li>
                    <?php else: ?>
                        <li><a href="auth/login.php">Cont</a></li>
                    <?php endif; ?>
                    <li>
                        <a href="cos.php" class="cart-link active">
                            Coș
                            <span id="cart-counter" class="cart-counter">0</span>
                        </a>
                    </li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="cart-container">
            <h1>Coșul tău de cumpărături</h1>
            <div id="cart-items">
                <!-- Aici vor fi afișate produsele din coș -->
            </div>
            <div class="cart-summary">
                <div class="total">Total: <span id="cart-total">0.00</span> RON</div>
                <button id="checkout-btn" class="btn btn-primary">Finalizează comanda</button>
                <button id="clear-cart-btn" class="btn btn-secondary">Golește coșul</button>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="company-info">
                <h3>Pescarul Expert SRL</h3>
                <p>Str. Pescărușului nr. 123</p>
                <p>București, Sector 1</p>
                <p>Tel: 0722 123 456</p>
                <p>Email: contact@pescarulexpert.ro</p>
            </div>
            <div class="program">
                <h3>Program</h3>
                <p>Luni - Vineri: 09:00 - 18:00</p>
                <p>Sâmbătă: 09:00 - 14:00</p>
                <p>Duminică: Închis</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 Pescarul Expert. Toate drepturile rezervate.</p>
        </div>
    </footer>

    <script src="js/cart-manager.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Funcție pentru actualizarea afișării coșului
            function updateCartDisplay() {
                const cartItems = document.getElementById('cart-items');
                const cart = cartManager.getCart();
                
                if (cart.length === 0) {
                    cartItems.innerHTML = '<p class="empty-cart">Coșul tău este gol.</p>';
                    document.getElementById('cart-total').textContent = '0.00';
                    return;
                }

                let html = '<div class="cart-items-list">';
                let total = 0;

                cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    
                    html += `
                        <div class="cart-item">
                            <img src="${item.image}" alt="${item.name}">
                            <div class="item-details">
                                <h3>${item.name}</h3>
                                <p class="item-price">${item.price.toFixed(2)} RON</p>
                            </div>
                            <div class="item-quantity">
                                <button class="quantity-btn" onclick="cartManager.updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                <span>${item.quantity}</span>
                                <button class="quantity-btn" onclick="cartManager.updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                            </div>
                            <div class="item-total">
                                <p>${itemTotal.toFixed(2)} RON</p>
                                <button class="remove-item" onclick="cartManager.removeFromCart(${item.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                cartItems.innerHTML = html;
                document.getElementById('cart-total').textContent = total.toFixed(2);

                // Adăugăm event listeners pentru butoanele de ștergere
                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        setTimeout(updateCartDisplay, 100); // Actualizăm după o scurtă pauză
                    });
                });

                // Adăugăm event listeners pentru butoanele de cantitate
                document.querySelectorAll('.quantity-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        setTimeout(updateCartDisplay, 100); // Actualizăm după o scurtă pauză
                    });
                });
            }

            // Actualizare inițială a coșului
            updateCartDisplay();

            // Event listener pentru golirea coșului
            document.getElementById('clear-cart-btn').addEventListener('click', function() {
                if (confirm('Ești sigur că vrei să golești coșul?')) {
                    cartManager.clearCart();
                    updateCartDisplay();
                }
            });

            // Event listener pentru finalizarea comenzii
            document.getElementById('checkout-btn').addEventListener('click', function() {
                const cart = cartManager.getCart();
                if (cart.length === 0) {
                    alert('Coșul tău este gol.');
                    return;
                }
                
                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    window.location.href = 'checkout.php';
                <?php else: ?>
                    if (confirm('Trebuie să fii autentificat pentru a finaliza comanda. Vrei să te autentifici?')) {
                        window.location.href = 'auth/login.php';
                    }
                <?php endif; ?>
            });

            // Actualizare coș la modificări
            document.addEventListener('cartUpdated', function() {
                updateCartDisplay();
            });
        });
    </script>
</body>
</html>
