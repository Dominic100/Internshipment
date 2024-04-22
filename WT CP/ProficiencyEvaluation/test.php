<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Page</title>
</head>
<body>
<h2>Proceed to evaluation?</h2>
<form method="post" action="aiEvaluation.php">
    <input type="submit" value="Yes">
</form>
</body>
</html>
