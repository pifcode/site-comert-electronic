<?php
require_once('config/database.php');
require_once('tcpdf/tcpdf.php');

function generateInvoice($order_id) {
    global $conn;
    
    // Verifică dacă folderul invoices există și are permisiuni de scriere
    $invoice_dir = dirname(__FILE__) . '/invoices';
    if (!file_exists($invoice_dir)) {
        mkdir($invoice_dir, 0777, true);
    }
    
    if (!is_writable($invoice_dir)) {
        error_log("Folderul invoices nu are permisiuni de scriere: " . $invoice_dir);
        return false;
    }
    
    // Obține detaliile comenzii
    $sql = "SELECT o.*, u.username, u.email as user_email 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    
    if (!$order) {
        error_log("Nu s-au găsit detaliile comenzii pentru ID: " . $order_id);
        return false;
    }

    try {
        // Creează PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Pescarul Expert');
        $pdf->SetAuthor('Pescarul Expert');
        $pdf->SetTitle('Factură Proformă - #' . $order_id);

        // Set default header data
        $pdf->SetHeaderData('', 0, 'Pescarul Expert SRL', 'Factură Proformă', array(0,64,255), array(0,64,128));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        // Set header and footer fonts
        $pdf->setHeaderFont(Array('dejavusans', '', 20));
        $pdf->setFooterFont(Array('dejavusans', '', 8));

        // Set margins
        $pdf->SetMargins(15, 25, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 25);

        // Set image scale factor
        $pdf->setImageScale(1.25);

        // Set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('dejavusans', '', 10);

        // Informații companie
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Furnizor:', 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(0, 6, 'Pescarul Expert SRL', 0, 1, 'L');
        $pdf->Cell(0, 6, 'Str. Pescărușului nr. 123', 0, 1, 'L');
        $pdf->Cell(0, 6, 'București, România', 0, 1, 'L');
        $pdf->Cell(0, 6, 'CUI: RO12345678', 0, 1, 'L');
        $pdf->Cell(0, 6, 'Nr. Reg. Com.: J40/1234/2023', 0, 1, 'L');

        $pdf->Ln(10);

        // Informații client
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Client:', 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(0, 6, $order['shipping_name'], 0, 1, 'L');
        $pdf->Cell(0, 6, $order['shipping_address'], 0, 1, 'L');
        $pdf->Cell(0, 6, $order['shipping_city'] . ', ' . $order['shipping_county'], 0, 1, 'L');
        $pdf->Cell(0, 6, 'Cod Poștal: ' . $order['shipping_postal_code'], 0, 1, 'L');
        $pdf->Cell(0, 6, 'Email: ' . $order['user_email'], 0, 1, 'L');

        $pdf->Ln(10);

        // Informații factură
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Factură Proformă nr. ' . $order_id, 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(0, 6, 'Data: ' . date('d.m.Y'), 0, 1, 'L');

        $pdf->Ln(10);

        // Header tabel produse
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(80, 7, 'Produs', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Cantitate', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Preț Unitar', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Total', 1, 1, 'C', true);

        // Obține produsele comenzii
        $sql = "SELECT oi.*, p.name, p.price 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $total = 0;
        $pdf->SetFont('dejavusans', '', 10);
        
        while ($item = mysqli_fetch_assoc($result)) {
            $itemTotal = $item['quantity'] * $item['price'];
            $total += $itemTotal;
            
            $pdf->Cell(80, 7, $item['name'], 1, 0, 'L');
            $pdf->Cell(25, 7, $item['quantity'], 1, 0, 'C');
            $pdf->Cell(40, 7, number_format($item['price'], 2) . ' RON', 1, 0, 'R');
            $pdf->Cell(40, 7, number_format($itemTotal, 2) . ' RON', 1, 1, 'R');
        }

        // Transport
        $pdf->Cell(145, 7, 'Transport:', 1, 0, 'R');
        $pdf->Cell(40, 7, '20.00 RON', 1, 1, 'R');

        // Total
        $total += 20; // adăugăm transportul
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(145, 7, 'Total RON:', 1, 0, 'R');
        $pdf->Cell(40, 7, number_format($total, 2) . ' RON', 1, 1, 'R');
        
        // Total EUR
        $total_eur = $order['amount_eur'];
        $pdf->Cell(145, 7, 'Total EUR:', 1, 0, 'R');
        $pdf->Cell(40, 7, number_format($total_eur, 2) . ' EUR', 1, 1, 'R');

        $pdf->Ln(10);

        // Mențiuni
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->MultiCell(0, 6, "Mențiuni:\n1. Aceasta este o factură proformă și nu reprezintă document fiscal.\n2. Plata a fost efectuată prin PayPal.\n3. Cursul de schimb utilizat: 1 EUR = " . number_format($total / $total_eur, 4) . ' RON', 0, 'L');

        // Generate PDF
        $filename = 'factura_proforma_' . $order_id . '.pdf';
        $filepath = $invoice_dir . '/' . $filename;
        
        if ($pdf->Output($filepath, 'F')) {
            return $filename;
        } else {
            error_log("Eroare la salvarea facturii: " . $filepath);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Eroare la generarea facturii: " . $e->getMessage());
        return false;
    }
}
?>
