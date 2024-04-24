<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('config.php');

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient_email = trim($_POST['recipient_email']);
    $message_content = trim($_POST['message_content']);

    // Validate input
    if (empty($recipient_email) || empty($message_content)) {
        echo "Recipient email and message content are required.";
    } else {
        // Find the recipient's user ID
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $recipient_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $recipient_row = $result->fetch_assoc();
            $recipient_id = $recipient_row['id'];

            // Insert message into the database
            $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iis', $user_id, $recipient_id, $message_content);
            $stmt->execute();

            echo "Message sent successfully.";
        } else {
            echo "Recipient not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Write Message</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Load Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Compose a Message</h2>

    <form action="write_message.php" method="POST">
        <div class="mb-3">
            <label for="recipient_email" class="form-label">Recipient Email:</label>
            <input type="email" id="recipient_email" name="recipient_email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="message_content" class="form-label">Message:</label>
            <textarea id="message_content" name="message_content" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>

    <div class="text-center mt-3">
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

</div>

<!-- Bootstrap 5 JavaScript and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>