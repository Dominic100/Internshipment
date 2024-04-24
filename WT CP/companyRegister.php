<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO company (name, email, password) VALUES ('$name', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        header("Location: companyLogin.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registration Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery and Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

<div class="container mt-4">
    <h2 class="text-center">Employer Registration Form</h2>
    <!-- Registration form -->
    <form method="post" action="" class="form-group mx-auto" style="max-width: 400px;">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <input type="submit" value="Register" class="btn btn-primary btn-block">
    </form>
    <p class="text-center mt-3">Already have an account? <a href="companyLogin.php">Login here</a>.</p>
    <div class="text-center">
        <a href="tempIndex.php" class="btn btn-secondary mt-2">Home</a>
    </div>
    <!-- Display error message if needed -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($conn) && $conn->error) {
        echo '<div class="alert alert-danger mt-3">Error: ' . htmlspecialchars($conn->error) . '</div>';
    }
    ?>
</div>

</body>
</html>