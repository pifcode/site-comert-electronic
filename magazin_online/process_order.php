<?php
// Activăm afișarea erorilor
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Setăm header-ul pentru JSON
header('Content-Type: application/json');

try {
    session_start();
    
    // Debug: Afișează toate datele primite
    $debug_data = [
        'POST' => $_POST,
        'SESSION' => $_SESSION
    ];

    // Verifică dacă utilizatorul este autentificat
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        throw new Exception('Utilizatorul nu este autentificat');
    }

    // Verificăm conexiunea la baza de date
    require_once "config/database.php";
    if (!isset($conn) || !$conn) {
        throw new Exception("Eroare la conexiunea cu baza de date: " . mysqli_connect_error());
    }

    // Verificăm datele primite
    if (empty($_POST['cart_items'])) {
        throw new Exception("Nu există produse în coș");
    }

    $cart_items = json_decode($_POST['cart_items'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Eroare la decodarea produselor din coș: " . json_last_error_msg());
    }

    // Verificăm câmpurile necesare
    $required_fields = [
        'nume', 'email', 'telefon', 'adresa', 'oras', 'judet', 
        'cod_postal', 'payment_id', 'payment_status', 'currency', 'amount_eur'
    ];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Câmpul '$field' lipsește sau este gol");
        }
    }

    // Convertim amount_eur la float pentru siguranță
    $amount_eur = (float)$_POST['amount_eur'];
    
    // Calculăm total_amount în RON (folosim un curs de schimb fix pentru exemplu)
    $exchange_rate = 4.97; // RON per EUR
    $total_amount = $amount_eur * $exchange_rate;

    // Începe tranzacția
    mysqli_begin_transaction($conn);

    // Inserează comanda în tabelul orders
    $sql = "INSERT INTO orders (
        user_id, status, payment_id, payment_status, 
        shipping_name, shipping_email, shipping_phone, 
        shipping_address, shipping_city, shipping_county, 
        shipping_postal_code, currency, amount_eur, total_amount
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Eroare la pregătirea statement-ului pentru comandă: " . mysqli_error($conn));
    }

    $status = 'new';
    if (!mysqli_stmt_bind_param($stmt, "isssssssssssdd", 
        $_SESSION["id"],
        $status,
        $_POST['payment_id'],
        $_POST['payment_status'],
        $_POST['nume'],
        $_POST['email'],
        $_POST['telefon'],
        $_POST['adresa'],
        $_POST['oras'],
        $_POST['judet'],
        $_POST['cod_postal'],
        $_POST['currency'],
        $amount_eur,
        $total_amount
    )) {
        throw new Exception("Eroare la legarea parametrilor pentru comandă: " . mysqli_error($conn));
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Eroare la salvarea comenzii: " . mysqli_error($conn));
    }

    $order_id = mysqli_insert_id($conn);
    if (!$order_id) {
        throw new Exception("Nu s-a putut obține ID-ul comenzii");
    }

    // Inserează produsele comenzii
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Eroare la pregătirea statement-ului pentru produse: " . mysqli_error($conn));
    }

    foreach ($cart_items as $item) {
        if (!isset($item['id']) || !isset($item['quantity']) || !isset($item['price'])) {
            throw new Exception("Date invalide pentru produs în coș");
        }

        $item_price = (float)$item['price'];
        if (!mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['id'], $item['quantity'], $item_price)) {
            throw new Exception("Eroare la legarea parametrilor pentru produs: " . mysqli_error($conn));
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Eroare la salvarea produsului în comandă: " . mysqli_error($conn));
        }
    }

    // Commit tranzacția
    if (!mysqli_commit($conn)) {
        throw new Exception("Nu s-a putut finaliza tranzacția");
    }

    // Salvăm ID-ul comenzii în sesiune pentru pagina de thank you
    $_SESSION['last_order_id'] = $order_id;
    
    // Generează factura
    require_once 'generate_invoice.php';
    generateInvoice($order_id);
    
    // Returnează succes și ID-ul comenzii
    echo json_encode([
        'success' => true, 
        'order_id' => $order_id,
        'debug_data' => $debug_data
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && mysqli_ping($conn)) {
        mysqli_rollback($conn);
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug_data' => $debug_data ?? null,
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>
