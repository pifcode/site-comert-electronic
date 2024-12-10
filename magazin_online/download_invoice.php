<?php
session_start();
require_once 'config/database.php';
require_once 'generate_invoice.php';

// Verifică dacă avem un order_id valid
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = (int)$_GET['order_id'];

// Verifică dacă acest order_id este același cu cel din sesiune
// Acest lucru asigură că utilizatorul poate descărca doar factura comenzii sale recente
if (!isset($_SESSION['last_order_id']) || $_SESSION['last_order_id'] != $order_id) {
    header('Location: index.php');
    exit();
}

// Verifică dacă comanda există
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!mysqli_fetch_assoc($result)) {
    header('Location: index.php');
    exit();
}

// Generează factura dacă nu există
$filename = 'factura_proforma_' . $order_id . '.pdf';
$filepath = __DIR__ . '/invoices/' . $filename;

if (!file_exists($filepath)) {
    $generated = generateInvoice($order_id);
    if (!$generated) {
        die('Ne cerem scuze, dar factura proformă nu poate fi generată momentan. Vă rugăm să contactați serviciul clienți.');
    }
}

// Verifică din nou dacă fișierul există
if (!file_exists($filepath)) {
    die('Ne cerem scuze, dar factura proformă nu poate fi găsită. Vă rugăm să contactați serviciul clienți.');
}

// Trimite fișierul către browser
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

readfile($filepath);
exit();
?>
