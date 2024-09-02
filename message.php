<?php
session_start();
require('fpdf/fpdf.php'); // Include FPDF library

// Connecting to the database
$conn = mysqli_connect("localhost", "dolibarrmysql", "mohamed2017", "dolibarr") or die("Database Error");


// Getting user message through AJAX
$getMesg = mysqli_real_escape_string($conn, $_POST['text']);

// Checking user query against database
$check_data = "SELECT replies FROM chatbot WHERE queries LIKE '%$getMesg%'";
$run_query = mysqli_query($conn, $check_data) or die("Error");

// If user query matched to database query, we'll show the reply
if (mysqli_num_rows($run_query) > 0) {
    // Fetching reply from the database according to the user query
    $fetch_data = mysqli_fetch_assoc($run_query);
    $replay = $fetch_data['replies'];

    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, $replay); // Use MultiCell for wrapping text
    $filename = 'reply.pdf';
    $pdf->Output('F', $filename);

    // Output PDF link
    echo json_encode(["pdf" => $filename]);

    // Store chat history in session
    if (!isset($_SESSION['chat_history'])) {
        $_SESSION['chat_history'] = [];
    }
    $_SESSION['chat_history'][] = "User: $getMesg";
    $_SESSION['chat_history'][] = "Bot: Sent a PDF";
} else {
    echo json_encode(["error" => "Sorry, can't be able to understand you!"]);
}
?>



