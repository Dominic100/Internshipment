<?php
session_start();
include('config.php');

$issetUserID = isset($_SESSION['user_id']);
$issetCompanyID = isset($_SESSION['company_id']);

$profileInitial = '';
$profilePicture = 'profile.jpg';

$fromHome = 1;

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

$sql = "SELECT * FROM internshipCard ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);

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
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: navy">
    <a class="navbar-brand" href="#" style="font-family: Anton, sans-serif; font-size: 50px;"><span style="color: deepskyblue">INTERNSHIP</span>MENT</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <?php if (!$issetUserID && !$issetCompanyID): ?>
            <!-- (login, register, dashboard) menu on the right end of the navbar -->
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
                    <a class="nav-link" href="internships.php" role="button" aria-expanded="false" style="font-family: Anton, sans-serif; font-size: 30px;">
                        INTERNSHIPS
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

                <li class="nav-item">
                    <a class="nav-link" href="internships.php" role="button" aria-expanded="false" style="font-family: Anton, sans-serif; font-size: 30px;">
                        INTERNSHIPS
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</nav>

<br><br><br><br>

<div class="container my-5">
    <h2 class="text-center mb-4" style="font-family: Anton, sans-serif; font-size: 50px;">Make your dream internship a reality!</h2>

    <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://image.cnbcfm.com/api/v1/image/107135706-1666017748820-gettyimages-1325207070-3q4a0950.jpeg?v=1673025472&w=929&h=523&vtcrop=y" class="d-block w-100" alt="Image 1">
            </div>
            <div class="carousel-item">
                <img src="https://miro.medium.com/v2/resize:fit:852/0*7EQMOXXRo93K1vc6.jpg" class="d-block w-100" alt="Image 2">
            </div>
            <div class="carousel-item">
                <img src="https://cdn.pixabay.com/photo/2019/01/19/19/22/recruitment-3942378_640.jpg" class="d-block w-100" alt="Image 3">
            </div>
        </div>

        <a class="carousel-control-prev" href="#imageCarousel" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </a>
        <a class="carousel-control-next" href="#imageCarousel" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </a>
    </div>
</div>

<br><hr><br>

<div class="container my-5">
    <h2 class="text-center mb-4" style="font-family: Anton, sans-serif; font-size: 50px;">Latest Internships</h2>

    <!-- Bootstrap Grid -->
    <div class="row">
        <?php
        // Initialize counter
        $counter = 0;

        // Loop through the fetched cards
        while ($internship = $result->fetch_assoc()):
            // If the counter is divisible by 3, start a new row
            if ($counter % 3 == 0 && $counter > 0) {
                echo '</div><div class="row">'; // Close current row and start a new one
            }

            $internship_id = htmlspecialchars($internship['id']);
            $company_id = htmlspecialchars($internship['company_id']);

            // Display the card
            echo '<div class="col-md-4 mb-4">'; // Create a column
            echo '<div class="card h-100">'; // Create a card
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($internship['title']) . '</h5>';
            echo '<hr>';
            echo '<p><strong>Company ID:</strong> ' . htmlspecialchars($internship['company_id']) . '</p>';
            echo '<p><strong>Start Date:</strong> ' . htmlspecialchars($internship['start_date']) . '</p>';
            echo '<p><strong>Duration:</strong> ' . htmlspecialchars($internship['duration']) . '</p>';
            echo '<p><strong>Registration Due Date:</strong> ' . htmlspecialchars($internship['registration_due_date']) . '</p>';
            echo '<p><strong>Stipend:</strong> ' . htmlspecialchars($internship['stipend']) . '</p>';
            echo '<p><strong>Subjects:</strong> ' . htmlspecialchars($internship['type']) . '</p>';

            // Display buttons
            echo '<a href="internshipdetails.php?id=' . $internship_id . '&company_id=' . $internship['company_id'] .'&fromHome=' . $fromHome .'" class="btn btn-info">View Details</a>';
            if ($issetUserID) {
                echo '<a href="applyInternship.php?internship_id=' . $internship_id . '&company_id=' . $company_id .'" class="btn btn-success ms-2">Apply</a>';
            }

            echo '</div>'; // Close card body
            echo '</div>'; // Close card
            echo '</div>'; // Close column

            // Increment counter
            $counter++;

        endwhile;
        ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>