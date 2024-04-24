<?php
session_start();
$company_id_get = isset($_GET['company_id']);
$company_id_set = isset($_SESSION['company_id']);

if (!$company_id_set && !$company_id_get) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

$fromHome = isset($_GET['fromHome']);

$fromInternships = isset($_GET['fromInternships']);

include('config.php');

if($company_id_set) {
    echo $_SESSION['company_id'];
}
if($company_id_get) {
    echo $_GET['company_id'];
}

if($company_id_set) {
    $company_id = $_SESSION['company_id'];
}
else if ($company_id_get) {
    $company_id = $_GET['company_id'];
}
$internship_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($internship_id === 0) {
    echo "Invalid internship ID.";
    exit();
}

// Query to fetch internship data and join with country, state, and city tables
$internship_sql = "SELECT i.*, c.name AS country_name, s.name AS state_name, ci.name AS city_name
                   FROM internshipdata AS i
                   LEFT JOIN countries AS c ON i.country_id = c.id
                   LEFT JOIN states AS s ON i.state_id = s.id
                   LEFT JOIN cities AS ci ON i.city_id = ci.id
                   WHERE i.id = ?";
$internship_stmt = $conn->prepare($internship_sql);
$internship_stmt->bind_param('i', $internship_id);
$internship_stmt->execute();
$internship_result = $internship_stmt->get_result();

if ($internship_result->num_rows === 0) {
    echo "No internship found.";
    exit();
}

$internship = $internship_result->fetch_assoc();

//$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Internship Details</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Audiowide&family=Bruno+Ace+SC&family=Bungee+Hairline&family=Flavors&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Sixtyfour&display=swap" rel="stylesheet">
    <style>
        #back {
            background-color: navy;
            color: #f2edf7;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #back:hover {
            background-color: #f2edf7;
            color: navy;
        }
        #details {
            background-color: gray;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #details:hover {
            background-color: white;
            color: gray;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h2 style="font-family: Anton, sans-serif; font-size: 50px; color: navy;">Internship Details</h2>
        </div>
        <div class="card-body">
            <p><strong>Title:</strong> <?php echo htmlspecialchars($internship['title']); ?></p>
            <p><strong>Company ID:</strong> <?php echo htmlspecialchars($internship['company_id']); ?></p>
            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($internship['start_date']); ?></p>
            <p><strong>Duration:</strong> <?php echo htmlspecialchars($internship['duration']); ?> days</p>
            <p><strong>Registration Due Date:</strong> <?php echo htmlspecialchars($internship['registration_due_date']); ?></p>
            <p><strong>Stipend:</strong> <?php echo htmlspecialchars($internship['stipend']); ?> Rupees</p>
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
            <p><strong>Company Info:</strong> <?php echo nl2br(htmlspecialchars($internship['company_info'])); ?></p>
            <p><strong>Internship Info:</strong> <?php echo nl2br(htmlspecialchars($internship['internship_info'])); ?></p>
            <p><strong>Requirements:</strong> <?php echo nl2br(htmlspecialchars($internship['requirements'])); ?></p>
            <p><strong>Skills:</strong> <?php echo nl2br(htmlspecialchars($internship['skills'])); ?></p>
            <p><strong>Who Can Apply:</strong> <?php echo nl2br(htmlspecialchars($internship['who_can_apply'])); ?></p>
            <p><strong>Perks:</strong> <?php echo nl2br(htmlspecialchars($internship['perks'])); ?></p>
            <p><strong>Number of Openings:</strong> <?php echo htmlspecialchars($internship['number_of_openings']); ?></p>

            <div class="mt-4">
                <a href="<?php echo $fromHome ? 'tempIndex.php' : ($fromInternships ? 'internships.php' : ($company_id_set ? 'companyInternships.php' : 'displayInternships.php')); ?>" class="btn btn-primary me-2" id="back">Back</a>

                <?php if ($company_id_set && $_GET['company_id']===$_SESSION['company_id']): ?>
                    <?php $_SESSION['company_id'] = $company_id;?>
                    <?php $_SESSION['internship_id'] = $internship_id;?>
                    <a href="companyInternResults.php" class="btn btn-secondary" id="details">Go to Company Intern Details</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>