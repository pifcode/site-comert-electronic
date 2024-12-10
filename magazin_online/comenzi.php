<?php
session_start();
require_once "config/database.php";

// Verificăm dacă utilizatorul este autentificat
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: auth/login.php");
    exit;
}

// Preluăm comenzile utilizatorului
$sql = "SELECT o.*, 
        COUNT(oi.id) as total_items,
        GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as products
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC";

$orders = [];
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_array($result)){
            $orders[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Istoricul comenzilor - Pescarul Expert</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="imagini/logo.png" alt="Logo Pescarul Expert" class="logo">
            <p class="slogan">Prinde aventura cu echipamentul nostru!</p>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Acasă</a></li>
                <li><a href="produse.php">Produse</a></li>
                <li><a href="profil.php">Profil (<?php echo htmlspecialchars($_SESSION["username"]); ?>)</a></li>
                <li><a href="comenzi.php" class="active">Comenzile mele</a></li>
                <li><a href="auth/logout.php">Deconectare</a></li>
                <li>
                    <a href="cos.php" class="cart-link">
                        Coș
                        <span id="cart-counter" class="cart-counter">0</span>
                    </a>
                </li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="orders-container">
            <h1>Istoricul comenzilor mele</h1>

            <?php if(empty($orders)): ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <h2>Nu ai încă nicio comandă</h2>
                    <p>Începe să cumperi din magazinul nostru!</p>
                    <a href="produse.php" class="btn btn-primary">Vezi produsele</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Comanda #<?php echo $order['id']; ?></h3>
                                    <span class="order-date">
                                        <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?>
                                    </span>
                                </div>
                                <span class="order-status <?php echo $order['status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'pending' => 'În așteptare',
                                        'processing' => 'În procesare',
                                        'shipped' => 'Expediată',
                                        'delivered' => 'Livrată',
                                        'cancelled' => 'Anulată'
                                    ];
                                    echo $status_text[$order['status']] ?? $order['status'];
                                    ?>
                                </span>
                            </div>
                            <div class="order-details">
                                <p><strong>Produse:</strong> <?php echo htmlspecialchars($order['products']); ?></p>
                                <p><strong>Total produse:</strong> <?php echo $order['total_items']; ?></p>
                                <p><strong>Total comandă:</strong> <?php echo number_format($order['total_amount'], 2); ?> RON</p>
                            </div>
                            <?php if($order['status'] === 'pending'): ?>
                                <div class="order-actions">
                                    <button class="btn btn-secondary" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                        Anulează comanda
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
    function cancelOrder(orderId) {
        if(confirm('Sigur doriți să anulați această comandă?')) {
            fetch('api/cancel-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Comanda a fost anulată cu succes!');
                    location.reload();
                } else {
                    alert(data.message || 'A apărut o eroare la anularea comenzii.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A apărut o eroare la anularea comenzii.');
            });
        }
    }
    </script>
</body>
</html>
