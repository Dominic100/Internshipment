<?php
session_start();
$company_id_set = isset($_SESSION['company_id']);

if (!$company_id_set) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

include('config.php');

$internship_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($internship_id === 0) {
    echo "Invalid internship ID.";
    exit();
}

$_SESSION['internship_id'] = $internship_id;

$internship_sql = "SELECT * FROM internshipdata WHERE id = ?";
$internship_stmt = $conn->prepare($internship_sql);
$internship_stmt->bind_param('i', $internship_id);
$internship_stmt->execute();
$internship_result = $internship_stmt->get_result();

if ($internship_result->num_rows === 0) {
    echo "No internship found.";
    exit();
}

$internship = $internship_result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Internship Details</title>
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
    </style>
</head>
<body>

<div class="container">
    <h2>Internship Details</h2>

    <p><strong>Title:</strong> <?php echo htmlspecialchars($internship['title']); ?></p>
    <p><strong>Company ID:</strong> <?php echo htmlspecialchars($internship['company_id']); ?></p>
    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($internship['start_date']); ?></p>
    <p><strong>Duration:</strong> <?php echo htmlspecialchars($internship['duration']); ?></p>
    <p><strong>Registration Due Date:</strong> <?php echo htmlspecialchars($internship['registration_due_date']); ?></p>
    <p><strong>Stipend:</strong> <?php echo htmlspecialchars($internship['stipend']); ?></p>
    <p><strong>Subjects:</strong> <?php echo htmlspecialchars($internship['type']); ?></p>
    <p><strong>Company Info:</strong> <?php echo nl2br(htmlspecialchars($internship['company_info'])); ?></p>
    <p><strong>Internship Info:</strong> <?php echo nl2br(htmlspecialchars($internship['internship_info'])); ?></p>
    <p><strong>Requirements:</strong> <?php echo nl2br(htmlspecialchars($internship['requirements'])); ?></p>
    <p><strong>Skills:</strong> <?php echo nl2br(htmlspecialchars($internship['skills'])); ?></p>
    <p><strong>Who Can Apply:</strong> <?php echo nl2br(htmlspecialchars($internship['who_can_apply'])); ?></p>
    <p><strong>Perks:</strong> <?php echo nl2br(htmlspecialchars($internship['perks'])); ?></p>
    <p><strong>Number of Openings:</strong> <?php echo htmlspecialchars($internship['number_of_openings']); ?></p>

    <br>
    <a href="<?php echo $company_id_set ? 'companyInternships.php' : 'displayInternships.php'; ?>" class="btn"><?php echo $company_id_set ? 'Back to Company Internships' : 'Back to Available Internships'; ?></a>

    <?php if ($company_id_set): ?>
        <br><br>
        <button class="btn" onclick="location.href='companyInternResults.php'">Go to Company Intern Details</button>
    <?php endif; ?>
</div>

</body>
</html>