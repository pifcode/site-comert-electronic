<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'pescarul_expert');

// Încercăm să ne conectăm la baza de date
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificăm conexiunea
if($conn === false){
    die("EROARE: Nu s-a putut conecta la baza de date. " . mysqli_connect_error());
}

// Setăm caracterele pentru UTF-8
mysqli_set_charset($conn, "utf8");
?>