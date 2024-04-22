<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: companyLogin.php");
    exit();
}

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

$internships_sql = "SELECT * FROM internshipCard WHERE company_id = ?";
$internships_stmt = $conn->prepare($internships_sql);
$internships_stmt->bind_param('i', $company_id);
$internships_stmt->execute();
$internships_result = $internships_stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Internships</title>
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
        .card {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Company ID: <?php echo $company_id; ?></h2>
    <p>Company Name: <?php echo $company_name; ?></p>
    <p>Email: <?php echo $email; ?></p>

    <button class="btn" onclick="location.href='postInternship.php'">Post Internship</button>
    <button class="btn" onclick="location.href='companyDashboard.php'">Back to dashboard</button>

    <h3>Internships</h3>

    <?php if ($internships_result->num_rows > 0): ?>
        <?php while ($internship = $internships_result->fetch_assoc()): ?>
            <div class="card">
                <h4><?php echo $internship['title']; ?></h4>
                <p>Company Name: <?php echo $company_name; ?></p>
                <p>Start Date: <?php echo $internship['start_date']; ?></p>
                <p>Duration: <?php echo $internship['duration']; ?></p>
                <p>Registration Due Date: <?php echo $internship['registration_due_date']; ?></p>
                <p>Stipend: <?php echo $internship['stipend']; ?></p>
                <p>Subjects: <?php echo $internship['type']; ?></p>
                <button class="btn" onclick="location.href='editInternship.php?id=<?php echo $internship['id']; ?>'">Edit</button>
                <button class="btn" onclick="location.href='deleteInternship.php?id=<?php echo $internship['id']; ?>'">Delete</button>
                <button class="btn" onclick="location.href='internshipdetails.php?id=<?php echo $internship['id']; ?>'">View Details</button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No internships found.</p>
    <?php endif; ?>
</div>

</body>
</html>