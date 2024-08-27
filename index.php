<?php
session_start();

// Define the chat file
$chatFile = 'chat.txt';
$setupFile = 'setup.php';

// Run setup if necessary
if (!file_exists($setupFile)) {
    header('Location: setup.php');
    exit();
}

// Check if the user has submitted their name
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $_SESSION['name'] = htmlspecialchars($_POST['name']);
    header('Location: index.php');
    exit();
}

// Check if the user is already logged in
if (!isset($_SESSION['name'])) {
    echo '<form method="POST">
            <label>Enter your name:</label><br>
            <input type="text" name="name" required><br>
            <button type="submit">Join Chat</button>
          </form>';
    exit();
}

// Load the chat messages
function loadChat() {
    global $chatFile;

    if (file_exists($chatFile)) {
        $lines = file($chatFile, FILE_IGNORE_NEW_LINES);
        $messages = array_slice($lines, -10);  // Show last 10 messages only
        return json_encode($messages);
    } else {
        return json_encode([]);
    }
}

// Handle chat submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = htmlspecialchars($_SESSION['name'] . ': ' . $_POST['message']);
    
    file_put_contents($chatFile, $message . PHP_EOL, FILE_APPEND);
    
    // Keep only the last 10 messages
    $lines = file($chatFile, FILE_IGNORE_NEW_LINES);
    if (count($lines) > 10) {
        file_put_contents($chatFile, implode(PHP_EOL, array_slice($lines, -10)) . PHP_EOL);
    }

    header('Location: index.php');
    exit();
}

// Handle AJAX loader
if (isset($_GET['loader'])) {
    header('Content-Type: application/json');
    echo loadChat();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Chat</title>
    <script>
        function loadChat() {
            fetch('index.php?loader=1')
                .then(response => response.json())
                .then(data => {
                    const chatBox = document.getElementById('chatBox');
                    chatBox.innerHTML = '';
                    data.forEach(line => {
                        const p = document.createElement('p');
                        p.textContent = line;
                        chatBox.appendChild(p);
                    });
                });
        }

        setInterval(loadChat, 300000); // Reload chat every 5 minutes
        window.onload = loadChat;
    </script>
</head>
<body>
    <h1>Welcome to the Chat</h1>
    <div id="chatBox" style="height: 300px; overflow-y: scroll; border: 1px solid #000;"></div>

    <form method="POST">
        <label>Your message:</label><br>
        <input type="text" name="message" required><br>
        <button type="submit">Send</button>
    </form>
</body>
</html>
