<?php
// Inițializăm sesiunea
session_start();
 
// Ștergem toate variabilele de sesiune
$_SESSION = array();
 
// Distrugem sesiunea
session_destroy();
 
// Redirecționăm către pagina de login
header("location: login.php");
exit;
?>
