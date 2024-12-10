<?php
session_start();
require_once "../config/database.php";

// Verificăm dacă utilizatorul este deja autentificat
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}

// Definim și inițializăm variabilele
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Procesăm datele formularului când este trimis
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validăm username-ul
    if(empty(trim($_POST["username"]))){
        $username_err = "Vă rugăm introduceți un username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username-ul poate conține doar litere, cifre și underscore.";
    } else{
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Acest username este deja folosit.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Ceva nu a mers bine. Încercați din nou mai târziu.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validăm email-ul
    if(empty(trim($_POST["email"]))){
        $email_err = "Vă rugăm introduceți adresa de email.";
    } else{
        $sql = "SELECT id FROM users WHERE email = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "Această adresă de email este deja folosită.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Ceva nu a mers bine. Încercați din nou mai târziu.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validăm parola
    if(empty(trim($_POST["password"]))){
        $password_err = "Vă rugăm introduceți o parolă.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Parola trebuie să aibă cel puțin 6 caractere.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validăm confirmarea parolei
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Vă rugăm confirmați parola.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Parolele nu se potrivesc.";
        }
    }
    
    // Verificăm erorile înainte de a insera în baza de date
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            if(mysqli_stmt_execute($stmt)){
                header("location: login.php");
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
    <title>Înregistrare - Pescarul Expert</title>
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
                        <li><a href="login.php">Autentificare</a></li>
                        <li><a href="register.php" class="active">Înregistrare</a></li>
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
            <h2>Înregistrare</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="auth-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <?php if(!empty($username_err)): ?>
                        <div class="error-message"><?php echo $username_err; ?></div>
                    <?php endif; ?>
                </div>    
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                    <?php if(!empty($email_err)): ?>
                        <div class="error-message"><?php echo $email_err; ?></div>
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
                    <label for="confirm_password">Confirmă parola</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                    <?php if(!empty($confirm_password_err)): ?>
                        <div class="error-message"><?php echo $confirm_password_err; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group button-group">
                    <button type="submit" class="auth-button">Înregistrare</button>
                    <button type="reset" class="auth-button-secondary">Resetare</button>
                </div>
                <p class="auth-link">Aveți deja cont? <a href="login.php">Autentificați-vă aici</a></p>
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
