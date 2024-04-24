<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('config.php');

$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
} else {
    echo "User not found.";
    header("Location: login.php");
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
    <!-- Bootstrap 5 JavaScript and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Audiowide&family=Bruno+Ace+SC&family=Bungee+Hairline&family=Flavors&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Sixtyfour&display=swap" rel="stylesheet">

    <style>
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
        .btn-test {
            background-color: deepskyblue;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-test:hover {
            background-color: white;
            color: deepskyblue;
        }
        .btn-resume {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-resume:hover {
            background-color: white;
            color: green;
        }
        .btn-profile {
            background-color: navy;
            color: cyan;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-profile:hover {
            background-color: cyan;
            color: navy;
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
        .btn-inbox {
            background-color: #007bff; /* Customize the color as per your preference */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-inbox:hover {
            background-color: white;
            color: #007bff; /* Customize the hover color as per your preference */
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
            <!-- Ladder-style button layout -->
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
                    <span class="me-2">Take a test to evaluate your skills.<br>
                        You won't be able to apply for internships unless you give the tests.</span>
                    <a href="test.php" class="btn btn-test w-40">Test</a>
                </div>
            </div>
            <hr>
            <div class="row align-items-center mb-3">
                <!-- Left-aligned button with description on the right -->
                <div class="col text-start d-flex">
                    <a href="resumeIntermediate.php" class="btn btn-resume w-40">Resume</a>
                    <span class="ms-2">Manage and update your resume.<br>
                        A higher chance of getting selected.</span>
                </div>
            </div>
            <hr>
            <div class="row align-items-center mb-3">
                <!-- Right-aligned button with description on the left -->
                <div class="col text-end d-flex justify-content-end">
                    <span class="me-2">View and manage your profile.<br>
                        Your test scores, proficiencies and resume will be displayed here</span>
                    <a href="studentProfile.php" class="btn btn-profile w-40">View Profile</a>
                </div>
            </div>
            <hr>
            <div class="row align-items-center mb-3">
                <!-- Left-aligned button with description on the right -->
                <div class="col text-start d-flex">
                    <a href="tempIndex.php" class="btn btn-home w-40">Home</a>
                    <span class="ms-2">Go back to the home page.</span>
                </div>
            </div>
            <hr>
            <div class="row align-items-center mb-3">
                <!-- Right-aligned button with description on the left -->
                <div class="col text-end d-flex justify-content-end">
                    <span class="me-2">Browse available internships.<br>
                        The best suited internships for you will be displayed here!</span>
                    <a href="displayInternships.php" class="btn btn-internships w-40">View Internships</a>
                </div>
            </div>
            <hr>
            <div class="row align-items-center mb-3">

                <div class="col text-start d-flex">
                    <a href="inbox.php" class="btn btn-inbox w-40">Inbox</a>
                    <span class="ms-2">Check your messages.<br>
                        See what others have sent you!</span>
                </div>
            </div>
            <hr>
<!--            <div class="row align-items-center mb-3">-->
<!--                <div class="col text-end d-flex justify-content-end">-->
<!--                    <span class="me-2">Compose a new message to another user.<br>-->
<!--                        Keep in touch with your connections!</span>-->
<!--                    <a href="write_message.php" class="btn btn-inbox w-40">Write Message</a>-->
<!--                </div>-->
<!--            </div>-->



        </div>
        <div class="card-footer text-muted">
            Enjoy your time here!
        </div>
    </div>
</div>

</body>

</html>