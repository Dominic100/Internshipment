<?php
session_start();
include('config.php');

$issetUserID = isset($_SESSION['user_id']);
$issetCompanyID = isset($_SESSION['company_id']);

$profileInitial = '';
$profilePicture = 'profile.jpg';

$fromInternships = 1;

if ($issetUserID) {
    $user_id = $_SESSION['user_id'];

    // Fetch user name from the database
    $sql = "SELECT name FROM users WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];

        // Get the first letter of the name
        $profileInitial = strtoupper(substr($name, 0, 1));
    }
} elseif ($issetCompanyID) {
    $company_id = $_SESSION['company_id'];

    // Fetch company name from the database
    $sql = "SELECT name FROM company WHERE id = $company_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];

        // Get the first letter of the name
        $profileInitial = strtoupper(substr($name, 0, 1));
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Home Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery and Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Audiowide&family=Bruno+Ace+SC&family=Bungee+Hairline&family=Flavors&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Sixtyfour&display=swap" rel="stylesheet">

    <!-- Custom CSS to shift profile picture to the left -->
    <style>
        /* Shift the profile picture to the left */
        .profile-pic {
            margin-left: -50px; /* Adjust the value as needed */
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: green; /* Set arrow color to green */
        }
        .carousel-item {
            width: 1200px;
            height: 600px;
        }
        .btn-info {
            background-color: deepskyblue;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-info:hover {
            background-color: white;
            color: deepskyblue;
        }
        /* Search and filter form styling */
        .search-form {
            margin-top: 100px; /* Adjust margin to account for the navbar */
        }
        .search-form input,
        .search-form select {
            margin-right: 10px;
        }
        .internship-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: box-shadow 0.3s ease, color 0.3s ease;
        }
        .internship-card:hover {
            transform: scale(1.03);
            box-shadow: 0 0 50px deepskyblue;
            border-color: deepskyblue;
        }
    </style>
</head>

<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: navy">
    <a class="navbar-brand" href="#" style="font-family: Anton, sans-serif; font-size: 50px;"><span style="color: deepskyblue">INTERNSHIP</span>S</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <?php if (!$issetUserID && !$issetCompanyID): ?>
            <!-- Login, register, dashboard menu on the right end of the navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Login dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-family: Anton, sans-serif; font-size: 30px;">
                        LOGIN
                    </a>
                    <div class="dropdown-menu" aria-labelledby="loginDropdown">
                        <a class="dropdown-item" href="login.php">Candidate</a>
                        <a class="dropdown-item" href="companyLogin.php">Company</a>
                    </div>
                </li>
                <!-- Register dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-family: Anton, sans-serif; font-size: 30px;">
                        REGISTER
                    </a>
                    <div class="dropdown-menu" aria-labelledby="registerDropdown">
                        <a class="dropdown-item" href="register.php">Candidate</a>
                        <a class="dropdown-item" href="companyRegister.php">Company</a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="tempIndex.php" role="button" aria-expanded="false" style="font-family: Anton, sans-serif; font-size: 30px;">
                        HOME
                    </a>
                </li>
            </ul>
        <?php else: ?>
            <!-- Profile dropdown -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php if ($profileInitial): ?>
                            <!-- Display the first letter of the name in a circular element -->
                            <span class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <?php echo $profileInitial; ?>
                            </span>
                        <?php else: ?>
                            <!-- Profile picture -->
                            <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="rounded-circle profile-pic" width="60" height="60">
                        <?php endif; ?>
                    </a>
                    <!-- Dropdown menu for profile -->
                    <div class="dropdown-menu" style="margin-left: -100px" aria-labelledby="profileDropdown">
                        <?php if (!$issetUserID): ?>
                            <a class="dropdown-item" href="companyDashboard.php">Dashboard</a>
                        <?php else: ?>
                            <a class="dropdown-item" href="dashboard.php">Dashboard</a>
                        <?php endif; ?>

                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</nav>

<!-- Search and filter form -->
<div class="container search-form">
    <form method="POST" action="" id="filterForm">
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <input type="text" name="search_term" class="form-control" placeholder="Search by title or info">
            </div>
            <div class="col-md-2 mb-3">
                <input type="number" name="stipend" class="form-control" placeholder="Stipend (Min)">
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" name="location" class="form-control" placeholder="Location (City, State, Country)">
            </div>
            <div class="col-md-2 mb-3">
                <input type="text" name="subject" class="form-control" placeholder="Subject">
            </div>
            <div class="col-md-2 mb-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <button type="button" class="btn btn-secondary btn-reset">Reset</button>
            </div>
        </div>
    </form>
</div>

<!-- Internship cards -->
<!-- Internship cards container -->
<div class="container mt-4" id="internshipContainer">
    <!-- Internship cards will be loaded here via AJAX -->
</div>

<script>
    $(document).ready(function() {
        function loadInternships() {
            $.ajax({
                url: 'internship_display.php?<?php echo ($issetUserID ? "user_id=".$user_id : ($issetCompanyID ? "company_id=".$company_id : ""))?>', // URL to fetch internships
                method: 'POST',
                data: $('#filterForm').serialize(), // Form data for filtering
                success: function(response) {
                    $('#internshipContainer').html(response); // Load internships into container
                },
                error: function(xhr, status, error) {
                    // Log any AJAX errors to the console
                    console.error(xhr.responseText);
                }
            });
        }

        // Load internships on page load
        loadInternships();

        // Submit form on filter button click
        $('#filterForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            loadInternships(); // Load internships with filters
        });

        // Reset filters and reload internships
        $('.btn-reset').on('click', function() {
            $('#filterForm')[0].reset(); // Reset form
            loadInternships(); // Load internships without filters
        });
    });
</script>

</body>
</html>