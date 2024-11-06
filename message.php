<?php
session_start();
require('fpdf/fpdf.php');

$conn = mysqli_connect("localhost", "dolibarrmysql", "mohamed2017", "dolibarr") or die("Database Error");

// Get user input
$clientRef = mysqli_real_escape_string($conn, $_POST['clientRef']);
$date = mysqli_real_escape_string($conn, $_POST['date']);
$clientCode = mysqli_real_escape_string($conn, $_POST['clientCode']);
$senderName = mysqli_real_escape_string($conn, $_POST['senderName']);
$senderAddress = mysqli_real_escape_string($conn, $_POST['senderAddress']);
$senderPhone = mysqli_real_escape_string($conn, $_POST['senderPhone']);
$recipientName = mysqli_real_escape_string($conn, $_POST['recipientName']);
$recipientAddress = mysqli_real_escape_string($conn, $_POST['recipientAddress']);
$queryText = mysqli_real_escape_string($conn, $_POST['text']);

// Retrieve the email from the client_emails table
$emailQuery = "SELECT email FROM client_emails ORDER BY id DESC LIMIT 1";
$emailResult = mysqli_query($conn, $emailQuery);
$emailRow = mysqli_fetch_assoc($emailResult);
$senderEmail = $emailRow['email']; // Use the retrieved email

// Query chatbot database
$check_data = "SELECT replies, pricing FROM chatbot WHERE queries LIKE '%$queryText%'";
$run_query = mysqli_query($conn, $check_data) or die("Error");

if (mysqli_num_rows($run_query) > 0) {
    $fetch_data = mysqli_fetch_assoc($run_query);
    $pricing = $fetch_data['pricing'];

	$insert_devis = "INSERT INTO devis (client_ref, date, client_code, sender_name, sender_address, sender_phone, recipient_name, recipient_address, query_text, pricing, pdf_path) VALUES ('$clientRef', '$date', '$clientCode', '$senderName', '$senderAddress', '$senderPhone', '$recipientName', '$recipientAddress', '$queryText', '$pricing', '$filename')";
	mysqli_query($conn, $insert_devis);



    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Add custom colors
    $pdf->SetTextColor(0, 102, 204); // Blue for the title
    $pdf->Cell(0, 10, 'Proposal (PROV1) - Not Validated', 0, 1, 'C');

    $pdf->SetFont('Arial', '', 12);
    $pdf->SetTextColor(0, 0, 0); // Black for body text

    // Client Information
    $pdf->Ln(5);
    $pdf->SetFillColor(230, 230, 230); // Light gray background
    $pdf->Cell(0, 10, 'Client Information', 0, 1, 'L', true);
    $pdf->Cell(0, 10, 'Client Ref: ' . $clientRef, 0, 1);
    $pdf->Cell(0, 10, 'Date: ' . $date, 0, 1);
    $pdf->Cell(0, 10, 'Client Code: ' . $clientCode, 0, 1);
    $pdf->Ln(5);

    // Sender Information
    $pdf->Cell(0, 10, 'Sender Information', 0, 1, 'L', true);
    $pdf->Cell(0, 10, $senderName, 0, 1);
    $pdf->Cell(0, 10, $senderAddress, 0, 1);
    $pdf->Cell(0, 10, 'Tel: ' . $senderPhone, 0, 1);
    $pdf->Cell(0, 10, 'Email: ' . $senderEmail, 0, 1); // Use the email from the database
    $pdf->Ln(5);

    // Recipient Information
    $pdf->Cell(0, 10, 'Recipient Information', 0, 1, 'L', true);
    $pdf->Cell(0, 10, $recipientName, 0, 1);
    $pdf->Cell(0, 10, $recipientAddress, 0, 1);
    $pdf->Ln(5);

    // Pricing Table Header
    $pdf->SetFillColor(0, 102, 204); // Blue background for table header
    $pdf->SetTextColor(255, 255, 255); // White text for table header
    $pdf->Cell(95, 10, 'Description', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Unit Price', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'Qty', 1, 0, 'C', true);
    $pdf->Cell(45, 10, 'Total', 1, 1, 'C', true);

    // Pricing Table Data
    $pdf->SetTextColor(0, 0, 0); // Black text for table data
    $pdf->SetFillColor(240, 240, 240); // Light gray for alternating rows
    $pdf->Cell(95, 10, 'Service Description', 1);
    $pdf->Cell(30, 10, number_format($pricing, 2), 1);
    $pdf->Cell(20, 10, '1', 1);
    $pdf->Cell(45, 10, number_format($pricing, 2), 1, 1);

    $pdf->Ln(5);

    // Display Query and Answer
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->SetTextColor(102, 102, 102); // Dark gray for questions and answers
    $pdf->MultiCell(0, 10, 'Question: ' . $queryText, 0, 'L');
    $pdf->MultiCell(0, 10, 'Answer: ' . $fetch_data['replies'], 0, 'L');

    $pdf->Ln(5);

    // Payment Information
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0, 102, 204); // Blue for payment information
    $pdf->Cell(0, 10, 'Payment Terms: On receipt', 0, 1);
    $pdf->Cell(0, 10, 'Total: ' . number_format($pricing, 2), 0, 1);
    $pdf->Cell(0, 10, 'Total with VAT: ' . number_format($pricing, 2), 0, 1);

    // Footer
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(150, 150, 150); // Light gray for footer
    $pdf->Cell(0, 10, 'Thank you for considering our proposal.', 0, 1, 'C');

    $filename = 'invoice.pdf';
    $pdf->Output('F', $filename);

    echo json_encode(["pdf" => $filename]);

    if (!isset($_SESSION['chat_history'])) {
        $_SESSION['chat_history'] = [];
    }
    $_SESSION['chat_history'][] = "User: $queryText";
    $_SESSION['chat_history'][] = "Bot: Sent an invoice PDF";
} else {
    echo json_encode(["error" => "Sorry, can't be able to understand you!"]);
}
?>
