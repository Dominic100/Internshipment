<?php
session_start();

if (!isset($_SESSION['company_id'])) {
header("Location: companyLogin.php");
exit();
}

include('config.php');

$company_id = $_SESSION['company_id'];

// Fetch messages for the company and join with the users table to get sender details
$sql = "SELECT messages.id, messages.content, messages.timestamp, messages.from_student, messages.sender_id,
users.name AS sender_name, users.email AS sender_email
FROM messages
JOIN users ON messages.sender_id = users.id
WHERE messages.receiver_id = ?
ORDER BY messages.timestamp DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Company Inbox</title>
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

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .unread {
            background-color: #f0f8ff; /* Light blue color for unread messages */
        }
        .btn-profile {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-profile:hover {
            background-color: white;
            color: green;
        }
        .btn-dashboard {
            background-color: #ffd700;
            color: #4f3c00;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-dashboard:hover {
            background-color: #4f3c00;
            color: #ffd700;
        }
    </style>
</head>

<body>

<div class="container">
    <h2 class="mb-4" style="font-family: Anton, sans-serif; font-size: 40px; color: navy;">Company Inbox</h2>

    <table class="table">
        <thead>
        <tr>
            <th style="width: 15%">User ID</th>
            <th>Sender</th>
            <th>Sender Email</th>
            <th>Content</th>
            <th>Timestamp</th>
            <th>Status</th>
            <th>Profile</th>
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
                <td><?php echo "User has applied for internship id:".htmlspecialchars($row['content']); ?></td>
                <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                <td><?php echo $row['from_student'] ? 'From Student' : 'From Company'; ?></td>
                <td><?php echo '<button class="btn btn-profile" id="primaryBtn" onclick="location.href=`studentProfile.php?user_id=' . $user_id . '&internship_id=' . $internship_id . '&company_id=' . $company_id . '`">View Profile</button>'; ?></td>
            </tr>


        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="companyDashboard.php" class="btn btn-dashboard">Back to Dashboard</a>
</div>

<!-- Bootstrap 5 JavaScript and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
