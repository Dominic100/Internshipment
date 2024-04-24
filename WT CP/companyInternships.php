<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: companyLogin.php");
    exit();
}
$fromHome=0;

include('config.php');

$company_id = $_SESSION['company_id'];

$company_sql = "SELECT name, email FROM company WHERE id = ?";
$company_stmt = $conn->prepare($company_sql);
$company_stmt->bind_param('i', $company_id);
$company_stmt->execute();
$company_result = $company_stmt->get_result();

if ($company_result->num_rows > 0) {
    $company_row = $company_result->fetch_assoc();
    $company_name = $company_row['name'];
    $email = $company_row['email'];
} else {
    echo "Company not found.";
    exit();
}

$internships_sql = "SELECT internshipcard.*, countries.name AS country_name, states.name AS state_name, cities.name AS city_name FROM internshipcard 
                    LEFT JOIN countries ON internshipcard.country_id = countries.id 
                    LEFT JOIN states ON internshipcard.state_id = states.id 
                    LEFT JOIN cities ON internshipcard.city_id = cities.id 
                    WHERE company_id = ?";
$internships_stmt = $conn->prepare($internships_sql);
$internships_stmt->bind_param('i', $company_id);
$internships_stmt->execute();
$internships_result = $internships_stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Company Internships</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Audiowide&family=Bruno+Ace+SC&family=Bungee+Hairline&family=Flavors&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Sixtyfour&display=swap" rel="stylesheet">
    <style>
        #main_card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        #main_card:hover {
            box-shadow: 0 0 20px deepskyblue;
            transform: translateY(-4px);
        }
        p {
            font-family: Arial, sans-serif;
        }
        .btn-post {
            background-color: #ffd700;
            color: #4f3c00;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-post:hover {
            background-color: #4f3c00;
            color: #ffd700;
        }
        .btn-dash {
            background-color: #00bbff;
            color: #000275;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-dash:hover {
            background-color: #000275;
            color: #00bbff;
        }
        .btn-edit {
            background-color: #d8e4e0;
            color: navy;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-edit:hover {
            background-color: navy;
            color: #d8e4e0;
        }
        .btn-delete {
            background-color: #e8dfd8;
            color: navy;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-delete:hover {
            background-color: navy;
            color: #e8dfd8;
        }
        .btn-details {
            background-color: #f2edf7;
            color: navy;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-details:hover {
            background-color: navy;
            color: #f2edf7;
        }
    </style>
</head>

<body>

<div class="container my-5">
    <div class="card text-center">
        <div class="card-header" style="background-color: #acffff">
            <h2 style="font-family: Anton, sans-serif; font-size: 50px; color: navy;">Company Information</h2>
        </div>
        <div class="card-body" style="background-color: #e8ffff">
            <p><strong>Company ID:</strong> <?php echo $company_id; ?></p>
            <p><strong>Company Name:</strong> <?php echo $company_name; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>

            <div class="btn-group mt-3" role="group">
                <a href="postInternship.php" class="btn btn-post">Post Internship</a>
                <a href="companyDashboard.php" class="btn btn-dash">Back to Dashboard</a>
            </div>

            <hr>
            <h3 style="font-family: Anton, sans-serif; font-size: 50px; color: navy;">Internships</h3>

            <?php if ($internships_result->num_rows > 0): ?>
                <?php while ($internship = $internships_result->fetch_assoc()): ?>
                    <div class="card mb-3 " id="main_card">
                        <div class="card-body">
                            <h4 class="card-title" style="font-family: Arial, sans-serif"><?php echo htmlspecialchars($internship['title']); ?></h4>
                            <h4 class="card-title" style="font-family: Arial, sans-serif">Id: <?php echo htmlspecialchars($internship['id']); ?></h4>
                            <hr>
                            <p><strong>Company Name:</strong> <?php echo $company_name; ?></p>
                            <p><strong>Duration:</strong> <?php echo htmlspecialchars($internship['duration']); ?></p>
                            <p><strong>Stipend:</strong> <?php echo htmlspecialchars($internship['stipend']); ?></p>
                            <p><strong>Subjects:</strong> <?php echo htmlspecialchars($internship['type']); ?></p>
                            <p><strong>Location:</strong>
                                <?php
                                if ($internship['work_from_home'] == 1) {
                                    echo "Work From Home";
                                } else {
                                    // Print city, state, and country information
                                    $location = '';
                                    if (!empty($internship['city_name'])) {
                                        $location .= htmlspecialchars($internship['city_name']);
                                    }
                                    if (!empty($internship['state_name'])) {
                                        $location .= ', ' . htmlspecialchars($internship['state_name']);
                                    }
                                    if (!empty($internship['country_name'])) {
                                        $location .= ', ' . htmlspecialchars($internship['country_name']);
                                    }
                                    echo $location;
                                }
                                ?>
                            </p>
                            <div class="btn-group mt-3" role="group">
                                <a href="editInternship.php?id=<?php echo $internship['id']; ?>" class="btn btn-edit">Edit</a>
                                <a href="deleteInternship.php?id=<?php echo $internship['id']; ?>" class="btn btn-delete">Delete</a>
                                <a href="internshipdetails.php?id=<?php echo $internship['id']; ?>&company_id=<?php echo $company_id?>" class="btn btn-details">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No internships found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>

</html>