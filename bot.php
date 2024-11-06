<?php
session_start();

// Connexion à la base de données dans dolibarr
$conn = mysqli_connect("localhost", "dolibarrmysql", "mohamed2017", "dolibarr");

// Handle email submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $insert_email = "INSERT INTO client_emails (email) VALUES ('$email')";
    mysqli_query($conn, $insert_email);
    $_SESSION['email_entered'] = true;
}

// Handle chat visibility toggle
if (isset($_GET['toggle_chat'])) {
    $_SESSION['chat_visible'] = !isset($_SESSION['chat_visible']) || !$_SESSION['chat_visible'];
    header("Location: bot.php");
    exit();
}

// Handle chat history clear
if (isset($_GET['clear_chat'])) {
    unset($_SESSION['chat_history']);
    header("Location: bot.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: bot.php");
    exit();
}

// Récupère les options prédéfinies depuis la base de données
$sql = "SELECT queries, replies FROM chatbot";
$result = mysqli_query($conn, $sql);
$options = array();
while ($row = mysqli_fetch_assoc($result)) {
    $options[$row['queries']] = $row['replies'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<?php if (!isset($_SESSION['email_entered'])): ?>
    <!-- Formulaire pour entrer l'email -->
    <form method="post" action="">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Submit</button>
    </form>
<?php else: ?>
    <!-- Bouton pour afficher/masquer le chat -->
    <button class="chat-toggle-btn" onclick="toggleChat()">
        <i class="fas fa-comments"></i>
    </button>
    <!-- Bouton pour effacer l'historique du chat -->
    <button class="chat-clear-btn" onclick="clearChat()">
        <i class="fas fa-trash-alt"></i>
    </button>
    <!-- Bouton pour déconnexion -->
    <button class="chat-logout-btn" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i>
    </button>

    <?php if ($_SESSION['chat_visible']): ?>
    <!-- Conteneur du chat affiché si la session chat_visible est vraie -->
    <div class="chat-container chat-container-fixed">
        <div class="wrapper">
            <div class="title">Chat with us</div>
            <div class="form">
                <div class="bot-inbox inbox">
                    <div class="icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="msg-header">
                        <p>Welcome sir</p>
                        <p>Please select an option:</p>
                        <div class="options-container">
                            <!-- Affiche les options prédéfinies -->
                            <?php foreach ($options as $query => $reply): ?>
                                <button class="option-btn" onclick="sendMessage('<?= $query ?>')"><?= $query ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php if (isset($_SESSION['chat_history'])): ?>
                    <!-- Affiche l'historique du chat -->
                    <?php foreach ($_SESSION['chat_history'] as $message): ?>
                        <div class="inbox <?= strpos($message, 'User:') === 0 ? 'user-inbox' : 'bot-inbox' ?>">
                            <div class="msg-header">
                                <p><?= $message ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<script>
function sendMessage(message) {
    var msg = '<div class="user-inbox inbox"><div class="msg-header"><p>User: ' + message + '</p></div></div>';
    $(".form").append(msg);

    $.ajax({
        url: 'message.php',
        type: 'POST',
        data: { text: message },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.pdf) {
                var replay = '<div class="bot-inbox inbox"><div class="icon"><i class="fas fa-user"></i></div><div class="msg-header"><p>Bot: <a href="' + result.pdf + '" target="_blank">Download PDF</a></p></div></div>';
            } else {
                var replay = '<div class="bot-inbox inbox"><div class="icon"><i class="fas fa-user"></i></div><div class="msg-header"><p>Bot: ' + result.error + '</p></div></div>';
            }
            $(".form").append(replay);
            $(".form").animate({ scrollTop: $(".form")[0].scrollHeight}, 1000);

            var optionsHtml = '<div class="bot-inbox inbox"><div class="icon"><i class="fas fa-user"></i></div><div class="msg-header"><p>Please select another option:</p><div class="options-container">';
            <?php foreach ($options as $query => $reply): ?>
                optionsHtml += '<button class="option-btn" onclick="sendMessage(\'<?= $query ?>\')"><?= $query ?></button>';
            <?php endforeach; ?>
            optionsHtml += '</div></div></div>';
            $(".form").append(optionsHtml);
        }
    });
}

function toggleChat() {
    window.location.href = "bot.php?toggle_chat=true";
}

function clearChat() {
    window.location.href = "bot.php?clear_chat=true";
}

function logout() {
    window.location.href = "bot.php?logout=true";
}
</script>

</body>
</html>
