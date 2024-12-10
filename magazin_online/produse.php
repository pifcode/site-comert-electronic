<?php
session_start();
require_once "config/database.php";

// Preluăm produsele din baza de date
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produse - Pescarul Expert</title>
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
                    <li><a href="produse.php" class="active">Produse</a></li>
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <li><a href="profil.php">Profil (<?php echo htmlspecialchars($_SESSION["username"]); ?>)</a></li>
                        <li><a href="auth/logout.php">Deconectare</a></li>
                    <?php else: ?>
                        <li><a href="auth/login.php">Cont</a></li>
                    <?php endif; ?>
                    <li>
                        <a href="cos.php" class="cart-link">
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
        <div class="page-container">
            <div class="products-section">
                <h1>Produsele noastre</h1>
                <div class="product-grid">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="product-card">';
                            echo '<a href="produs.php?id=' . $row['id'] . '" class="product-link">';
                            echo '<img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                            echo '<div class="product-info">';
                            echo '<h3 class="product-title">' . htmlspecialchars($row['name']) . '</h3>';
                            echo '<p class="description">' . htmlspecialchars($row['description']) . '</p>';
                            echo '<p class="price">' . number_format($row['price'], 2) . ' RON</p>';
                            echo '</div>';
                            echo '</a>';
                            echo '<button class="add-to-cart-btn" 
                                    data-id="' . $row['id'] . '"
                                    data-name="' . htmlspecialchars($row['name']) . '"
                                    data-price="' . $row['price'] . '"
                                    data-image="' . htmlspecialchars($row['image']) . '">
                                    Adaugă în coș</button>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="no-products">Nu există produse disponibile momentan.</p>';
                    }
                    ?>
                </div>
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
</body>
</html>