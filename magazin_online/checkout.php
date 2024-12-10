<?php
// Activăm afișarea erorilor pentru debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pornim sesiunea
session_start();

// Verificăm conexiunea la baza de date
try {
    require_once "config/database.php";
    echo "<!-- Database connection successful -->\n";
} catch (Exception $e) {
    die("Eroare la conectarea la baza de date: " . $e->getMessage());
}

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: auth/login.php");
    exit;
}

echo "<!-- User is logged in -->\n";
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizare Comandă - Pescarul Expert</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Debugging script -->
    <script>
        console.log('Page is loading...');
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('Error: ' + msg + '\nURL: ' + url + '\nLine: ' + lineNo + '\nColumn: ' + columnNo + '\nError object: ' + JSON.stringify(error));
            return false;
        };
    </script>
</head>
<body>
    <?php 
    // Include header with error checking
    $headerPath = "includes/header.php";
    if (file_exists($headerPath)) {
        include $headerPath;
        echo "<!-- Header included successfully -->\n";
    } else {
        echo "<!-- Error: Header file not found at " . $headerPath . " -->\n";
    }
    ?>

    <main>
        <div class="checkout-container">
            <h2>Finalizare Comandă</h2>
            
            <div class="checkout-grid">
                <!-- Informații livrare și facturare -->
                <div class="checkout-form">
                    <form id="shipping-form" method="post" class="auth-form">
                        <h3>Informații Livrare</h3>
                        
                        <div class="form-group">
                            <label for="nume">Nume Complet*</label>
                            <input type="text" id="nume" name="nume" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="telefon">Telefon*</label>
                            <input type="tel" id="telefon" name="telefon" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="adresa">Adresa*</label>
                            <input type="text" id="adresa" name="adresa" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="oras">Oraș*</label>
                            <input type="text" id="oras" name="oras" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="judet">Județ*</label>
                            <input type="text" id="judet" name="judet" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="cod_postal">Cod Poștal*</label>
                            <input type="text" id="cod_postal" name="cod_postal" class="form-control" required>
                        </div>
                    </form>
                </div>

                <!-- Sumar comandă -->
                <div class="order-summary">
                    <h3>Sumar Comandă</h3>
                    <div id="cart-items"></div>
                    <div class="order-totals">
                        <div class="subtotal">
                            <span>Subtotal:</span>
                            <span id="subtotal">0.00 RON</span>
                        </div>
                        <div class="shipping">
                            <span>Transport:</span>
                            <span>20.00 RON</span>
                        </div>
                        <div class="total">
                            <span>Total:</span>
                            <span id="total">0.00 RON</span>
                        </div>
                        <div class="total-eur">
                            <span>Total EUR:</span>
                            <span id="total-eur">0.00 EUR</span>
                        </div>
                    </div>
                </div>

                <!-- PayPal Button Container -->
                <div id="paypal-button-container"></div>
                <div id="payment-message" style="display: none;" class="alert"></div>
            </div>
        </div>
    </main>

    <?php 
    // Include footer with error checking
    $footerPath = "includes/footer.php";
    if (file_exists($footerPath)) {
        include $footerPath;
        echo "<!-- Footer included successfully -->\n";
    } else {
        echo "<!-- Error: Footer file not found at " . $footerPath . " -->\n";
    }
    ?>

    <!-- JavaScript Files -->
    <script src="js/cart-manager.js"></script>
    <script>
        // Debugging
        console.log('Scripts are loading...');
        
        function showMessage(message, isError = false) {
            const messageDiv = document.getElementById('payment-message');
            messageDiv.textContent = message;
            messageDiv.className = 'alert ' + (isError ? 'alert-error' : 'alert-success');
            messageDiv.style.display = 'block';
        }

        function convertRONtoEUR(amount) {
            const rate = 0.20;
            return amount * rate;
        }

        function updateOrderSummary() {
            const cartItems = JSON.parse(localStorage.getItem('cart')) || [];
            const cartContainer = document.getElementById('cart-items');
            let subtotal = 0;

            cartContainer.innerHTML = '';
            cartItems.forEach(item => {
                const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                subtotal += itemTotal;

                cartContainer.innerHTML += `
                    <div class="cart-item">
                        <span class="item-name">${item.name}</span>
                        <span class="item-quantity">x${item.quantity}</span>
                        <span class="item-price">${itemTotal.toFixed(2)} RON</span>
                    </div>
                `;
            });

            const shipping = 20;
            const total = subtotal + shipping;
            const totalEUR = convertRONtoEUR(total);

            document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' RON';
            document.getElementById('total').textContent = total.toFixed(2) + ' RON';
            document.getElementById('total-eur').textContent = totalEUR.toFixed(2) + ' EUR';

            localStorage.setItem('cartTotal', total.toString());
            return totalEUR;
        }

        // Verificăm conexiunea la internet
        function checkInternetConnection() {
            return navigator.onLine;
        }

        // Verificăm dacă putem accesa PayPal
        async function checkPayPalAccess() {
            try {
                const response = await fetch('https://www.paypal.com/sdk/js?client-id=Ad9kAkuOeKpjh1oaC-We6tIZ1bqVRmSV00w4lQHatiHvswDXEJrBoes_szIaEWEm456UcLoL8IYK089w', { method: 'HEAD' });
                return response.ok;
            } catch (error) {
                console.error('PayPal access check failed:', error);
                return false;
            }
        }

        // Funcție pentru încărcarea scriptului PayPal
        function loadPayPalScript() {
            return new Promise((resolve, reject) => {
                if (typeof paypal !== 'undefined') {
                    console.log('PayPal already loaded, using existing instance');
                    resolve();
                    return;
                }

                console.log('Creating new PayPal script...');
                const script = document.createElement('script');
                script.src = 'https://www.paypal.com/sdk/js?client-id=Ad9kAkuOeKpjh1oaC-We6tIZ1bqVRmSV00w4lQHatiHvswDXEJrBoes_szIaEWEm456UcLoL8IYK089w&currency=EUR&components=buttons&debug=true';
                
                // Adăugăm un timeout pentru încărcare
                const timeoutId = setTimeout(() => {
                    console.error('PayPal script load timeout after 10s');
                    reject(new Error('PayPal script load timeout'));
                }, 10000);

                script.addEventListener('load', () => {
                    console.log('PayPal script loaded successfully');
                    clearTimeout(timeoutId);
                    
                    // Verificăm dacă obiectul PayPal este disponibil
                    if (typeof paypal === 'undefined') {
                        console.error('PayPal object not available after script load');
                        reject(new Error('PayPal object not available'));
                        return;
                    }
                    
                    resolve(script);
                });

                script.addEventListener('error', (error) => {
                    console.error('PayPal script failed to load:', error);
                    clearTimeout(timeoutId);
                    reject(new Error('PayPal script failed to load'));
                });
                
                // Adăugăm scriptul în head în loc de body
                document.head.appendChild(script);
            });
        }

        // Inițializare PayPal
        async function initializePayPal() {
            try {
                if (!checkInternetConnection()) {
                    console.error('No internet connection detected');
                    throw new Error('No internet connection');
                }

                console.log('Checking PayPal access...');
                const hasPayPalAccess = await checkPayPalAccess();
                if (!hasPayPalAccess) {
                    console.error('Cannot access PayPal servers');
                    throw new Error('Cannot access PayPal servers');
                }

                console.log('Loading PayPal script...');
                await loadPayPalScript();
                
                console.log('PayPal script loaded, initializing buttons...');
                
                // Verificăm din nou dacă PayPal este disponibil
                if (typeof paypal === 'undefined') {
                    console.error('PayPal object not available after initialization');
                    throw new Error('PayPal initialization failed');
                }

                return await new Promise((resolve, reject) => {
                    try {
                        const buttons = paypal.Buttons({
                            createOrder: function(data, actions) {
                                console.log('Creating order...');
                                const formData = new FormData(document.getElementById('shipping-form'));
                                
                                // Verificăm câmpurile formularului
                                const requiredFields = ['nume', 'email', 'telefon', 'adresa', 'oras', 'judet', 'cod_postal'];
                                const missingFields = requiredFields.filter(field => !formData.get(field));
                                
                                if (missingFields.length > 0) {
                                    console.error('Missing required fields:', missingFields);
                                    showMessage('Vă rugăm să completați toate câmpurile obligatorii: ' + missingFields.join(', '), true);
                                    return null;
                                }

                                // Verificăm coșul și calculăm totalul
                                const totalEUR = updateOrderSummary();
                                if (totalEUR <= 0) {
                                    console.error('Invalid total amount:', totalEUR);
                                    showMessage('Eroare la calcularea totalului comenzii.', true);
                                    return null;
                                }

                                console.log('Creating PayPal order with amount:', totalEUR);
                                return actions.order.create({
                                    purchase_units: [{
                                        amount: {
                                            value: totalEUR.toFixed(2),
                                            currency_code: 'EUR'
                                        }
                                    }]
                                });
                            },
                            onApprove: function(data, actions) {
                                console.log('Payment approved, capturing order...');
                                return actions.order.capture().then(function(details) {
                                    console.log('Order captured:', details);
                                    
                                    const formData = new FormData(document.getElementById('shipping-form'));
                                    const cartItems = localStorage.getItem('cart');
                                    
                                    if (!cartItems) {
                                        console.error('Cart is empty!');
                                        showMessage('Eroare: coșul este gol!', true);
                                        return;
                                    }
                                    
                                    formData.append('payment_id', details.id);
                                    formData.append('payment_status', details.status);
                                    formData.append('currency', 'EUR');
                                    formData.append('amount_eur', details.purchase_units[0].amount.value);
                                    formData.append('cart_items', cartItems);

                                    console.log('Processing order with payment ID:', details.id);
                                    showMessage('Procesăm comanda dumneavoastră...');
                                    
                                    fetch('process_order.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => {
                                        console.log('Server response:', response);
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Order processing response:', data);
                                        if (data.success) {
                                            showMessage('Plata a fost procesată cu succes! Vă mulțumim pentru comandă.');
                                            localStorage.removeItem('cart');
                                            localStorage.removeItem('cartTotal');
                                            setTimeout(() => {
                                                window.location.href = 'thank_you.php?order_id=' + data.order_id;
                                            }, 2000);
                                        } else {
                                            console.error('Order processing failed:', data.message);
                                            showMessage('A apărut o eroare: ' + data.message, true);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error processing order:', error);
                                        showMessage('A apărut o eroare la procesarea comenzii. Vă rugăm să încercați din nou.', true);
                                    });
                                }).catch(function(error) {
                                    console.error('Error capturing order:', error);
                                    showMessage('A apărut o eroare la finalizarea plății. Vă rugăm să încercați din nou.', true);
                                });
                            },
                            onError: function(err) {
                                console.error('PayPal error:', err);
                                showMessage('A apărut o eroare la procesarea plății. Vă rugăm să încercați din nou.', true);
                            },
                            onCancel: function(data) {
                                console.log('Payment cancelled by user');
                                showMessage('Plata a fost anulată. Puteți încerca din nou când doriți.', true);
                            }
                        }).render('#paypal-button-container').catch(function(error) {
                            console.error('Error rendering PayPal buttons:', error);
                            showMessage('Eroare la afișarea butonului PayPal. Vă rugăm să reîncărcați pagina.', true);
                        });
                    } catch (error) {
                        console.error('Error in PayPal button initialization:', error);
                        reject(error);
                    }
                });
            } catch (error) {
                console.error('PayPal initialization error:', error);
                showMessage('Eroare la inițializarea PayPal. Vă rugăm să verificați conexiunea la internet și să reîncărcați pagina.', true);
                throw error;
            }
        }

        // Verificăm dacă există produse în coș înainte de a încărca PayPal
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded event fired');
            
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            console.log('Cart contents:', cart);
            
            if (cart.length === 0) {
                console.log('Cart is empty, redirecting to cos.php');
                window.location.href = 'cos.php';
                return;
            }

            console.log('Updating order summary...');
            updateOrderSummary();
            
            console.log('Updating cart counter...');
            if (typeof cartManager !== 'undefined') {
                cartManager.updateCartCounter();
            }

            // Inițializăm PayPal
            initializePayPal().catch(error => {
                console.error('Failed to initialize PayPal:', error);
                showMessage('Eroare la inițializarea PayPal. Vă rugăm să reîncărcați pagina.', true);
            });
        });
    </script>
</body>
</html>