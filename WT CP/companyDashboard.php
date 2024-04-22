<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: companyLogin.php");
    exit();
}

include('config.php');

$company_id = $_SESSION['company_id'];
$sql = "SELECT name, email FROM company WHERE id = $company_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
} else {
    echo "User not found.";
    header("Location: companyLogin.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
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
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo $name; ?>!</h2>
    <p>Email: <?php echo $email; ?></p>
    <p>This is your dashboard. You can add more content or functionality as needed.</p>
    <a href="logout.php" class="btn">Logout</a>
    <br><br>
    <a href="companyInternships.php" class="btn">Internships</a>
    <br><br>
</div>

</body>
</html>