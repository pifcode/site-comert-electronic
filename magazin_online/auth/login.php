<?php
session_start();
require_once "../config/database.php";

// Verificăm dacă utilizatorul este deja autentificat
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}

// Definim variabilele și le inițializăm cu valori goale
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Procesăm datele formularului când este trimis
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Verificăm dacă username-ul este gol
    if(empty(trim($_POST["username"]))){
        $username_err = "Vă rugăm introduceți username-ul.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Verificăm dacă parola este goală
    if(empty(trim($_POST["password"]))){
        $password_err = "Vă rugăm introduceți parola.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validăm credențialele
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            
                            // Stocăm datele în variabile de sesiune
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: ../index.php");
                        } else{
                            $login_err = "Username sau parolă invalidă.";
                        }
                    }
                } else{
                    $login_err = "Username sau parolă invalidă.";
                }
            } else{
                echo "Oops! Ceva nu a mers bine. Încercați din nou mai târziu.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare - Pescarul Expert</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="../imagini/logo.png" alt="Logo Pescarul Expert" class="logo">
                <span class="slogan">Prinde aventura cu echipamentul nostru!</span>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php">Acasă</a></li>
                    <li><a href="../produse.php">Produse</a></li>
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <li><a href="../profil.php">Profil (<?php echo htmlspecialchars($_SESSION["username"]); ?>)</a></li>
                        <li><a href="logout.php">Deconectare</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="active">Cont</a></li>
                        <li><a href="register.php">Înregistrare</a></li>
                    <?php endif; ?>
                    <li>
                        <a href="../cos.php" class="cart-link">
                            Coș
                            <span id="cart-counter" class="cart-counter">0</span>
                        </a>
                    </li>
                    <li><a href="../contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <h2>Autentificare</h2>
            <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="auth-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <?php if(!empty($username_err)): ?>
                        <div class="error-message"><?php echo $username_err; ?></div>
                    <?php endif; ?>
                </div>    
                <div class="form-group">
                    <label for="password">Parolă</label>
                    <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <?php if(!empty($password_err)): ?>
                        <div class="error-message"><?php echo $password_err; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <button type="submit" class="auth-button">Autentificare</button>
                </div>
                <p class="auth-link">Nu aveți cont? <a href="register.php">Înregistrați-vă acum</a></p>
            </form>
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

    <script src="../js/cart-manager.js"></script>
</body>
</html>
