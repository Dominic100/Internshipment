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
            margin-top: 40px; /* Add some margin at the top */
        }

        .btn {
            margin-right: 10px; /* Add some spacing between buttons */
        }

        .btn-home {
            background-color: dimgray;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-home:hover {
            background-color: white;
            color: dimgray;
        }
        .btn-internships {
            background-color: #ffd700;
            color: #4f3c00;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-internships:hover {
            background-color: #4f3c00;
            color: #ffd700;
        }
        .btn-logout {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-logout:hover {
            background-color: white;
            color: red;
        }
        .btn-applications {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-applications:hover {
            background-color: white;
            color: green;
        }
    </style>
</head>

<body>

<div class="container my-5" style="width: 60%;">
    <h2 style='font-family: Anton, sans-serif; font-size: 50px; color: navy; margin-top: -10px; margin-bottom: 25px'><strong>Dashboard</strong></h2>
    <div class="card text-center">
        <div class="card-header">
            <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
        </div>
        <div class="card-body">
            <p class="card-text">Email: <?php echo htmlspecialchars($email); ?></p>
            <hr>
            <div class="row align-items-center mb-3">
                <!-- Left-aligned button with description on the right -->
                <div class="col text-start d-flex">
                    <a href="logout.php" class="btn btn-logout w-40">Logout</a>
                    <span class="ms-2">Sign out of your account.<br>
                                You might wanna go out, touch some grass.</span>
                </div>
            </div>
            <hr>
            <div class="row align-items-center mb-3">
                <!-- Right-aligned button with description on the left -->
                <div class="col text-end d-flex justify-content-end">
                            <span class="me-2">List of internships posted by you.<br>
                                You can post, edit and delete your internships!</span>
                    <a href="companyInternships.php" class="btn btn-internships w-40">Internships</a>
                </div>
            </div>
            <hr>
            <div class="row align-items-center mb-3">
                <!-- Left-aligned button with description on the right -->
                <div class="col text-start d-flex">
                    <a href="company_inbox.php" class="btn btn-applications w-40">Applications</a>
                    <span class="ms-2">View student applications here.<br>
                                You will be able to approach students you are interested in.</span>
                </div>
            </div>
            <hr>
            <div class="row align-items-center mb-3">
                <!-- Right-aligned button with description on the left -->
                <div class="col text-end d-flex justify-content-end">
                            <span class="me-2">Had enough scouting for the day?<br>
                                Go back to home.</span>
                    <a href="tempIndex.php" class="btn btn-home w-40">Home</a>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            Enjoy your time here!
        </div>
    </div>
</div>
<!-- Bootstrap 5 JavaScript and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>