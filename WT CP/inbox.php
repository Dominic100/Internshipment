<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('config.php');

$user_id = $_SESSION['user_id'];

// Fetch messages for the user

$sql = "SELECT messages.id, messages.content, messages.timestamp, messages.from_student, messages.sender_id,
company.name AS sender_name, company.email AS sender_email
FROM messages
JOIN company ON messages.sender_id = company.id
WHERE messages.receiver_id = ?
ORDER BY messages.timestamp DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Close the database connection
//$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JavaScript and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        .card {
            margin-bottom: 15px;
        }

        .card-title {
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Inbox</h2>

    <table class="table">
        <thead>
        <tr>
            <th>User ID</th>
            <th>Sender</th>
            <th>Sender Email</th>
            <th>Content</th>
            <th>Timestamp</th>
            <th>Status</th>
<!--            <th>Profile</th>-->
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php $user_id = htmlspecialchars($row['sender_id']); ?>
            <?php $internship_id = htmlspecialchars($row['content']); ?>
            <tr>
                <td><?php echo htmlspecialchars($row['sender_id']); ?></td>
                <td><?php echo htmlspecialchars($row['sender_name']); ?></td>
                <td><?php echo htmlspecialchars($row['sender_email']); ?></td>
                <td><?php echo htmlspecialchars($row['content']); ?></td>
                <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                <td><?php echo $row['from_student'] ? 'From Student' : 'From Company'; ?></td>
<!--                <td>--><?php //echo '<button class="btn btn-primary" id="primaryBtn" onclick="location.href=`studentProfile.php?user_id=' . $user_id . '&internship_id=' . $internship_id . '&company_id=' . $company_id . '`">View Profile</button>'; ?><!--</td>-->
            </tr>


        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Add a back button to the dashboard -->
    <div class="text-center mt-3">
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

</div>

</body>

</html>