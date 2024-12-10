<?php
session_start();
require_once "config/database.php";
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Pescarul Expert</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="contact-container">
            <div class="contact-info">
                <h1>Contactează-ne</h1>
                <div class="info-section">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="info-text">
                            <h3>Adresa noastră</h3>
                            <p>Str. Pescărușului nr. 123</p>
                            <p>București, Sector 1</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div class="info-text">
                            <h3>Telefon</h3>
                            <p>0722 123 456</p>
                            <p>Luni - Vineri: 09:00 - 18:00</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div class="info-text">
                            <h3>Email</h3>
                            <p>contact@pescarulexpert.ro</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h2>Trimite-ne un mesaj</h2>
                <form action="process_contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nume complet</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subiect</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Mesaj</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Trimite mesajul</button>
                </form>
            </div>

            <div class="map-container">
                <h2>Locația noastră</h2>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d91100.06963147726!2d26.02236835!3d44.4377401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40b1f93abf3cad4f%3A0xac0632e37c9ca628!2sBucure%C8%99ti!5e0!3m2!1sro!2sro!4v1635959562000!5m2!1sro!2sro" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
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
