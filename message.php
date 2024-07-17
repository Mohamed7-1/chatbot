<?php
session_start();

// Connecting to the database
$conn = mysqli_connect("localhost", "root", "", "bot") or die("Database Error");

// Getting user message through AJAX
$getMesg = mysqli_real_escape_string($conn, $_POST['text']);

// Checking user query against database
$check_data = "SELECT replies FROM chatbot WHERE queries LIKE '%$getMesg%'";
$run_query = mysqli_query($conn, $check_data) or die("Error");

// If user query matched to database query, we'll show the reply
if (mysqli_num_rows($run_query) > 0) {
    // Fetching reply from the database according to the user query
    $fetch_data = mysqli_fetch_assoc($run_query);
    // Storing reply in a variable which we'll send to AJAX
    $replay = $fetch_data['replies'];
    echo $replay;

    // Store chat history in session
    if (!isset($_SESSION['chat_history'])) {
        $_SESSION['chat_history'] = [];
    }
    $_SESSION['chat_history'][] = "User: $getMesg";
    $_SESSION['chat_history'][] = "Bot: $replay";
} else {
    echo "Sorry, can't be able to understand you!";
}
?>