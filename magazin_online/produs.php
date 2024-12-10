<?php
session_start();
require_once "config/database.php";

// Verificăm dacă avem un ID valid
if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("location: produse.php");
    exit;
}

// Preluăm detaliile produsului
$sql = "SELECT * FROM products WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_GET["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) == 0) {
        header("location: produse.php");
        exit;
    }
    
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    header("location: produse.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Pescarul Expert</title>
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
                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <li><a href="profil.php">Profil (<?php echo htmlspecialchars($_SESSION["username"]); ?>)</a></li>
                    <li><a href="auth/logout.php">Deconectare</a></li>
                <?php else: ?>
                    <li><a href="auth/login.php">Autentificare</a></li>
                    <li><a href="auth/register.php">Înregistrare</a></li>
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
    </header>

    <main>
        <div class="product-details-container">
            <div class="product-details">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <nav class="breadcrumb">
                        <a href="index.php">Acasă</a> &gt;
                        <a href="produse.php">Produse</a> &gt;
                        <span><?php echo htmlspecialchars($product['name']); ?></span>
                    </nav>
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-meta">
                        <p class="product-category">Categorie: <?php echo htmlspecialchars($product['category']); ?></p>
                        <p class="product-stock">
                            <?php if($product['stock'] > 0): ?>
                                <span class="in-stock">În stoc</span>
                            <?php else: ?>
                                <span class="out-of-stock">Stoc epuizat</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="product-description">
                        <h2>Descriere</h2>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        
                        <h2>Specificații tehnice</h2>
                        <div class="specifications">
                            <?php if(isset($product['specifications']) && !empty($product['specifications'])): ?>
                                <?php foreach($product['specifications'] as $label => $value): ?>
                                    <div class="spec-item">
                                        <span class="spec-label"><?php echo htmlspecialchars($label); ?>:</span>
                                        <span class="spec-value"><?php echo htmlspecialchars($value); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="spec-item">
                                    <span class="spec-label">Material:</span>
                                    <span class="spec-value">Carbon de înaltă calitate</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Greutate:</span>
                                    <span class="spec-value">280 grame</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Dimensiuni:</span>
                                    <span class="spec-value">30 x 15 x 5 cm</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Țară de origine:</span>
                                    <span class="spec-value">Germania</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Garanție:</span>
                                    <span class="spec-value">24 luni</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h2>Caracteristici principale</h2>
                        <ul class="features-list">
                            <li><i class="fas fa-check"></i> Rezistență sporită la uzură</li>
                            <li><i class="fas fa-check"></i> Design ergonomic pentru confort maxim</li>
                            <li><i class="fas fa-check"></i> Tehnologie avansată de fabricație</li>
                            <li><i class="fas fa-check"></i> Compatibil cu accesorii standard</li>
                            <li><i class="fas fa-check"></i> Recomandat pentru pescuit profesionist</li>
                        </ul>

                        <h2>Recomandări de utilizare</h2>
                        <div class="usage-tips">
                            <div class="tip">
                                <i class="fas fa-fish"></i>
                                <h3>Tipuri de pești recomandați</h3>
                                <p>Perfect pentru pescuitul la crap, știucă și șalău</p>
                            </div>
                            <div class="tip">
                                <i class="fas fa-water"></i>
                                <h3>Mediu de utilizare</h3>
                                <p>Potrivit pentru ape dulci, lacuri și râuri</p>
                            </div>
                            <div class="tip">
                                <i class="fas fa-temperature-high"></i>
                                <h3>Condiții optime</h3>
                                <p>Utilizabil în toate anotimpurile</p>
                            </div>
                        </div>

                        <h2>Pachetul include</h2>
                        <ul class="package-contents">
                            <li><i class="fas fa-box"></i> Produsul principal</li>
                            <li><i class="fas fa-book"></i> Manual de utilizare în limba română</li>
                            <li><i class="fas fa-tools"></i> Set de întreținere de bază</li>
                            <li><i class="fas fa-certificate"></i> Certificat de garanție</li>
                        </ul>

                        <h2>Sfaturi de întreținere</h2>
                        <div class="maintenance-tips">
                            <p><i class="fas fa-hand-sparkles"></i> Curățați după fiecare utilizare</p>
                            <p><i class="fas fa-box-open"></i> Păstrați în husa dedicată când nu este folosit</p>
                            <p><i class="fas fa-sun"></i> Evitați expunerea prelungită la soare</p>
                            <p><i class="fas fa-temperature-low"></i> Depozitați la temperatura camerei</p>
                        </div>
                    </div>
                    <div class="product-price">
                        <p class="price"><?php echo number_format($product['price'], 2); ?> RON</p>
                        <?php if($product['stock'] > 0): ?>
                            <button class="add-to-cart-btn"
                                    data-id="<?php echo $product['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                    data-price="<?php echo $product['price']; ?>"
                                    data-image="<?php echo htmlspecialchars($product['image']); ?>">
                                <i class="fas fa-shopping-cart"></i> Adaugă în coș
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/cart-manager.js"></script>
    <script>
        // Inițializăm cart manager-ul
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof cartManager !== 'undefined') {
                cartManager.updateCartCounter();
            } else {
                console.error('Cart manager not loaded!');
            }
        });
    </script>
</body>
</html>
