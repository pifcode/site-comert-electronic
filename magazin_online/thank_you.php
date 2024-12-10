<?php
session_start();
require_once "config/database.php";

// Verifică dacă există order_id în sesiune
if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_SESSION['last_order_id'];

// Obține detaliile comenzii
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mulțumim pentru comandă - Pescarul Expert</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .thank-you-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .order-details {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .thank-you-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .thank-you-buttons .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .success-icon {
            text-align: center;
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .order-summary {
            margin-top: 20px;
        }

        .order-summary h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .order-info p {
            margin: 5px 0;
        }

        .shipping-details, .payment-details {
            background: white;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .btn-success {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
        }
        .btn-success:hover {
            background-color: #45a049;
        }
        .invoice-download {
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="thank-you-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="text-center">Mulțumim pentru comandă!</h1>
        <p class="text-center">Comanda dumneavoastră cu numărul #<?php echo $order_id; ?> a fost înregistrată cu succes.</p>

        <div class="order-summary">
            <h3>Detalii comandă:</h3>
            <div class="order-info">
                <div class="shipping-details">
                    <h4>Adresa de livrare:</h4>
                    <p><?php echo htmlspecialchars($order['shipping_name']); ?></p>
                    <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                    <p><?php echo htmlspecialchars($order['shipping_city']) . ', ' . htmlspecialchars($order['shipping_county']); ?></p>
                    <p>Cod Poștal: <?php echo htmlspecialchars($order['shipping_postal_code']); ?></p>
                </div>

                <div class="payment-details">
                    <h4>Detalii plată:</h4>
                    <p>Total: <?php echo number_format($order['amount_eur'], 2); ?> EUR</p>
                    <p>Status plată: <?php echo htmlspecialchars($order['payment_status']); ?></p>
                    <p>Metodă plată: PayPal</p>
                </div>
            </div>
        </div>

        <div class="invoice-download">
            <p>Puteți descărca factura proformă aici:</p>
            <a href="download_invoice.php?order_id=<?php echo $order_id; ?>" class="btn btn-success">
                Descarcă Factura Proformă
            </a>
        </div>

        <div class="thank-you-buttons">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Înapoi la Magazin
            </a>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
