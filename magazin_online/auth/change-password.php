<?php
session_start();
require_once "../config/database.php";

// Verificăm dacă utilizatorul este autentificat
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Definim variabilele și le inițializăm cu valori goale
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
$success_message = $error_message = "";

// Procesăm datele formularului când este trimis
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validăm parola nouă
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Te rugăm să introduci parola nouă.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Parola trebuie să aibă cel puțin 6 caractere.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validăm confirmarea parolei
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Te rugăm să confirmi parola.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Parolele nu corespund.";
        }
    }
    
    // Verificăm erorile de validare înainte de a face update în baza de date
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Pregătim declarația de update
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Legăm variabilele la declarația pregătită ca parametri
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "si", $param_password, $_SESSION["id"]);
            
            // Încercăm să executăm declarația pregătită
            if(mysqli_stmt_execute($stmt)){
                $success_message = "Parola a fost actualizată cu succes!";
            } else{
                $error_message = "A apărut o eroare. Te rugăm să încerci din nou mai târziu.";
            }

            // Închidem declarația
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schimbă parola - Pescarul Expert</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="../imagini/logo.png" alt="Logo Pescarul Expert" class="logo">
            <p class="slogan">Prinde aventura cu echipamentul nostru!</p>
        </div>
        <nav>
            <ul>
                <li><a href="../index.php">Acasă</a></li>
                <li><a href="../produse.php">Produse</a></li>
                <li><a href="../profil.php">Profil</a></li>
                <li><a href="logout.php">Deconectare</a></li>
                <li>
                    <a href="../cos.php" class="cart-link">
                        Coș
                        <span id="cart-counter" class="cart-counter">0</span>
                    </a>
                </li>
                <li><a href="../contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="change-password-container">
            <h1>Schimbă parola</h1>
            
            <?php if(!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="password-form-wrapper">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Parola nouă</label>
                        <div class="password-input-group">
                            <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                            <i class="fas fa-key"></i>
                        </div>
                        <?php if(!empty($new_password_err)): ?>
                            <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirmă parola nouă</label>
                        <div class="password-input-group">
                            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                            <i class="fas fa-key"></i>
                        </div>
                        <?php if(!empty($confirm_password_err)): ?>
                            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Schimbă parola</button>
                        <a href="../profil.php" class="btn btn-secondary">Anulează</a>
                    </div>
                </form>
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

    <script src="../js/cart-manager.js"></script>
</body>
</html>
