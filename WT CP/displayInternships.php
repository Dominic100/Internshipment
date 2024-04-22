<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

include('config.php');

$sql = "SELECT * FROM internshipcard";
$result = $conn->query($sql);

//$companyPreferencesData = [];
//$sql = "SELECT * FROM company_preferences";
//$result = $conn->query($sql);
//if ($result->num_rows > 0) {
//    while ($row = $result->fetch_assoc()) {
//        $companyId = $row["id"];
//        $internship_id = $row["internship_id"];
//        $companyPreferencesData[$companyId][$internship_id] = [
//            'ai' => $row['ai'],
//            'ml' => $row['ml'],
//            'ds' => $row['ds'],
//            'app_dev' => $row['app_dev'],
//            'game_dev' => $row['game_dev'],
//            'web_dev' => $row['web_dev']
//        ];
//    }
//}
//
//$sql = "SELECT * FROM user_proficiency WHERE id = ?";
//$stmt = $conn->prepare($sql);
//$stmt->bind_param('i', $user_id);
//$stmt->execute();
//$result = $stmt->get_result();
//
//$userProficiencyData = [];
//if ($result->num_rows > 0) {
//    $row = $result->fetch_assoc();
//    $userProficiencyData = [
//        'ai' => $row['ai'],
//        'ml' => $row['ml'],
//        'ds' => $row['ds'],
//        'app_dev' => $row['app_dev'],
//        'game_dev' => $row['game_dev'],
//        'web_dev' => $row['web_dev']
//    ];
//}
//
//$CIids = [];
//
//foreach ($companyPreferencesData as $companyId => $internships) {
//    foreach ($internships as $internshipId => $preferences) {
//        $CIids[] = ['company_id' => $companyId, 'internship_id' => $internshipId];
//    }
//}
//
//print_r($CIids);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Internships</title>
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
    <h2>Available Internships</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($internship = $result->fetch_assoc()): ?>
            <div class="card">
                <h4><?php echo htmlspecialchars($internship['title']); ?></h4>
                <p>Start Date: <?php echo htmlspecialchars($internship['start_date']); ?></p>
                <p>Duration: <?php echo htmlspecialchars($internship['duration']); ?></p>
                <p>Registration Due Date: <?php echo htmlspecialchars($internship['registration_due_date']); ?></p>
                <p>Stipend: <?php echo htmlspecialchars($internship['stipend']); ?></p>
                <p>Subjects: <?php echo htmlspecialchars($internship['type']); ?></p>
                <!-- View Details button -->
                <button class="btn" onclick="location.href='internshipDetails.php?id=<?php echo $internship['id']; ?>'">View Details</button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No internships available at the moment.</p>
    <?php endif; ?>
</div>

</body>
</html>