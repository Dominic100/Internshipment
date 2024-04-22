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

$companyPreferencesData = [];
$sql = "SELECT * FROM company_preferences";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $companyId = $row["id"];
        $internship_id = $row["internship_id"];
        $companyPreferencesData[$companyId][$internship_id] = [
            'ai' => $row['ai'],
            'ml' => $row['ml'],
            'ds' => $row['ds'],
            'app_dev' => $row['app_dev'],
            'game_dev' => $row['game_dev'],
            'web_dev' => $row['web_dev']
        ];
    }
}

$sql = "SELECT * FROM user_proficiency WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$userProficiencyData = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userProficiencyData = [
        'ai' => $row['ai'],
        'ml' => $row['ml'],
        'ds' => $row['ds'],
        'app_dev' => $row['app_dev'],
        'game_dev' => $row['game_dev'],
        'web_dev' => $row['web_dev']
    ];
}

$CIids = [];

foreach ($companyPreferencesData as $companyId => $internships) {
    foreach ($internships as $internshipId => $preferences) {
        $CIids[] = ['company_id' => $companyId, 'internship_id' => $internshipId];
    }
}

//print_r($CIids);

function calculateAHP($companyPreferencesData, $userProficiencyData, $CIids, $conn) {
    $companyPreferencesMatrix = [];
    $rows = 0;
    foreach ($companyPreferencesData as $skills) {
        $rows++;
        $companyPreferencesMatrix[] = array_values($skills);
    }

    $userProficiencyArray = array_values($userProficiencyData);
    $skillsCount = count($companyPreferencesMatrix[0]);

    $criteriaWeights = [];
    $sum=0;
    for($i=0; $i < $skillsCount; $i++) {
        $sum+=$userProficiencyArray[$i];
    }
    for($i=0; $i < $skillsCount; $i++) {
        $criteriaWeights[$i] = $userProficiencyArray[$i] / $sum;
    }

    $normalizedAlternativeMatrix = [];
    for($i = 0; $i < $skillsCount; $i++) {
        $sum = 0;
        for ($j = 0; $j < $rows; $j++) {
            $sum += $companyPreferencesMatrix[$j][$i];
        }
        for ($j = 0; $j < $rows; $j++) {
            $normalizedAlternativeMatrix[$j][$i] = $companyPreferencesMatrix[$j][$i] / $sum;
        }
    }

    $alternativeIDs = [];
    $alternativeScores = [];
    for ($i = 0; $i < $rows; $i++) {
        $score = 0;
        for ($j = 0; $j < $skillsCount; $j++) {
            $score += $normalizedAlternativeMatrix[$i][$j] * $criteriaWeights[$j];
        }
        $alternativeScores[$i] = $score;
        $alternativeIDs[$i] = $CIids[$i];
    }

    print_r($alternativeIDs);

//    array_multisort($alternativeScores, SORT_DESC, $alternativeIDs);
//    echo "<p><strong>Top Scores:</strong></p>";
//    foreach ($alternativeScores as $index => $score) {
//        $userId = $alternativeIDs[$index];
//        $userQuery = "SELECT name, email FROM users WHERE id = $userId";
//        $userResult = $conn->query($userQuery);
//        if ($userResult->num_rows > 0) {
//            $userRow = $userResult->fetch_assoc();
//            $userName = $userRow['name'];
//            $userEmail = $userRow['email'];
//
//            echo '<div style="border: 1px solid #ccc; border-radius: 5px; padding: 15px; margin-bottom: 10px; display: flex;">';
//            echo '<div style="flex-grow: 1;">';
//            echo "<p><strong>User ID:</strong> $userId</p>";
//            echo "<p><strong>Name:</strong> $userName</p>";
//            echo "<p><strong>Email:</strong> $userEmail</p>";
//            echo "<p><strong>Score:</strong> $score</p>";
//            // Add "View Profile" button
////            echo $userId;
//            echo '<button class="btn" onclick="location.href=`studentProfile.php?user_id=' . $userId . '`">View Profile</button>';
//            echo '</div>';
//            echo '</div>';
//        }
//    }
//
//    return $alternativeScores;
}

$alternativeScores = calculateAHP($userProficiencyData, $companyData, $userIds, $conn);
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