<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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
    </div>
</header>
