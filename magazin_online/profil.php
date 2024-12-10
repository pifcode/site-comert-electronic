<?php
session_start();
require_once "config/database.php";

// Verificăm dacă utilizatorul este autentificat
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: auth/login.php");
    exit;
}

// Preluăm datele utilizatorului
$sql = "SELECT * FROM users WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_array($result)){
            $username = $row['username'];
            $email = $row['email'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $phone = $row['phone'];
            $address = $row['address'];
        }
    }
    mysqli_stmt_close($stmt);
}

// Procesăm actualizarea datelor
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validăm și actualizăm datele
    $update_sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ? WHERE id = ?";
    if($update_stmt = mysqli_prepare($conn, $update_sql)){
        mysqli_stmt_bind_param($update_stmt, "ssssi", 
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['phone'],
            $_POST['address'],
            $_SESSION["id"]
        );
        
        if(mysqli_stmt_execute($update_stmt)){
            $success_message = "Profilul a fost actualizat cu succes!";
            // Actualizăm variabilele pentru afișare
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
        } else {
            $error_message = "A apărut o eroare la actualizarea profilului.";
        }
        mysqli_stmt_close($update_stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Pescarul Expert</title>
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
                <li><a href="profil.php" class="active">Profil</a></li>
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
        <div class="profile-container">
            <h1>Profilul meu</h1>
            
            <?php if(isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="profile-info">
                <div class="info-section">
                    <h2>Informații cont</h2>
                    <p><strong>Nume utilizator:</strong> <?php echo htmlspecialchars($username); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                </div>

                <form class="profile-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <h2>Informații personale</h2>
                    
                    <div class="form-group">
                        <label>Prenume</label>
                        <input type="text" name="first_name" class="form-control" 
                               value="<?php echo htmlspecialchars($first_name); ?>">
                    </div>

                    <div class="form-group">
                        <label>Nume</label>
                        <input type="text" name="last_name" class="form-control" 
                               value="<?php echo htmlspecialchars($last_name); ?>">
                    </div>

                    <div class="form-group">
                        <label>Telefon</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($phone); ?>">
                    </div>

                    <div class="form-group">
                        <label>Adresă</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($address); ?></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Actualizează profilul</button>
                    </div>
                </form>

                <div class="password-change-section">
                    <h2>Securitate</h2>
                    <a href="auth/change-password.php" class="btn btn-secondary">Schimbă parola</a>
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
