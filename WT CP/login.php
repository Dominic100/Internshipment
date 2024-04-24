<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Login failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login Page</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

<div class="container mt-4">
    <h2 class="text-center">Student Login</h2>
    <form method="post" action="" class="form-group mx-auto" style="max-width: 400px;">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <input type="submit" value="Login" class="btn btn-primary btn-block">
    </form>
    <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
    <div class="text-center">
        <a href="tempIndex.php" class="btn btn-secondary mt-2">Home</a>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($result) && $result->num_rows != 1) {
        echo '<div class="alert alert-danger mt-3">Login failed. Please try again.</div>';
    }
    ?>
</div>

</body>
</html>