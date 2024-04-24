<?php
session_start();

if (!isset($_SESSION['company_id']) || !isset($_SESSION['internship_id'])) {
    header("Location: companyLogin.php");
    exit();
}

include('config.php');

$company_id = $_SESSION['company_id'];
$internship_id = $_SESSION['internship_id'];

$userProficiencyData = [];
$sql = "SELECT * FROM user_proficiency";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row["id"];
        $userProficiencyData[$userId] = [
            'ai' => $row['ai'],
            'ml' => $row['ml'],
            'ds' => $row['ds'],
            'app_dev' => $row['app_dev'],
            'game_dev' => $row['game_dev'],
            'web_dev' => $row['web_dev']
        ];
    }
}

$sql = "SELECT * FROM company_preferences WHERE id = ? AND internship_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $company_id, $internship_id);
$stmt->execute();
$result = $stmt->get_result();

$companyData = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $companyData = [
        'ai' => $row['ai'],
        'ml' => $row['ml'],
        'ds' => $row['ds'],
        'app_dev' => $row['app_dev'],
        'game_dev' => $row['game_dev'],
        'web_dev' => $row['web_dev']
    ];
}

$userIds = array_keys($userProficiencyData);

function calculateAHP($userProficiency, $companyPreferences, $userIds, $conn) {
    $userProficiencyMatrix = [];
    $rows = 0;
    foreach ($userProficiency as $skills) {
        $rows++;
        $userProficiencyMatrix[] = array_values($skills);
    }

    $companyPreferencesArray = array_values($companyPreferences);
    $skillsCount = count($userProficiencyMatrix[0]);

    $criteriaWeights = [];
    $sum=0;
    for($i=0; $i < $skillsCount; $i++) {
        $sum+=$companyPreferencesArray[$i];
    }
    for($i=0; $i < $skillsCount; $i++) {
        $criteriaWeights[$i] = $companyPreferencesArray[$i] / $sum;
    }

    $normalizedAlternativeMatrix = [];
    for($i = 0; $i < $skillsCount; $i++) {
        $sum = 0;
        for ($j = 0; $j < $rows; $j++) {
            $sum += $userProficiencyMatrix[$j][$i];
        }
        for ($j = 0; $j < $rows; $j++) {
            $normalizedAlternativeMatrix[$j][$i] = $userProficiencyMatrix[$j][$i] / $sum;
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
        $alternativeIDs[$i] = $userIds[$i];
    }

    array_multisort($alternativeScores, SORT_DESC, $alternativeIDs);
    echo '<div class="container">'; // Add a container div to center the content
    echo "<p style='font-family: Anton, sans-serif; font-size: 50px; color: navy; margin-top: -10px'><strong>Top Scores:</strong></p>";
    $company_id = $_SESSION['company_id'];
    $internship_id = $_SESSION['internship_id'];
    foreach ($alternativeScores as $index => $score) {
        $userId = $alternativeIDs[$index];
        $userQuery = "SELECT name, email FROM users WHERE id = $userId";
        $userResult = $conn->query($userQuery);
        if ($userResult->num_rows > 0) {
            $userRow = $userResult->fetch_assoc();
            $userName = $userRow['name'];
            $userEmail = $userRow['email'];

            echo '<div class="row justify-content-center mb-3">'; // Create a row for each card and center it
            echo '<div class="col-md-6">'; // Center the card by using col-md-6
            echo '<div class="card card-hover shadow-sm" style="border: 2px solid deepskyblue; border-radius: 10px;">';
            echo '<div class="card-body">';
            echo "<p><strong>User ID:</strong> $userId</p>";
            echo "<p><strong>Name:</strong> $userName</p>";
            echo "<p><strong>Email:</strong> $userEmail</p>";
            echo "<p><strong>Score:</strong> $score</p>";
            echo '<button class="btn btn-primary" id="primaryBtn" onclick="location.href=`studentProfile.php?user_id=' . $userId . '&internship_id=' . $internship_id . '&company_id=' . $company_id . '`">View Profile</button>';
            echo '</div>';
            echo '</div>'; // Close card
            echo '</div>'; // Close col-md-6
            echo '</div>'; // Close row
        }
    }
    echo '</div>'; // Close the container div

    return $alternativeScores;
}

$alternativeScores = calculateAHP($userProficiencyData, $companyData, $userIds, $conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Results</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Audiowide&family=Bruno+Ace+SC&family=Bungee+Hairline&family=Flavors&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Sixtyfour&display=swap" rel="stylesheet">
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
            margin-bottom: 10px;
            padding: 10px;
        }
        .card:hover {
            transition: box-shadow 0.3s ease-in-out;
            box-shadow: 0 0 50px deepskyblue;
            border-color: deepskyblue;
        }
        #primaryBtn {
            background-color: navy;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #primaryBtn:hover {
            background-color: white;
            color: navy;
        }
        .btn-dash {
            background-color: #ffd700;
            color: #4f3c00;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
            text-decoration: none;
        }
        .btn-dash:hover {
            background-color: #4f3c00;
            color: #ffd700;
        }
        .btn-back {
            background-color: navy;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: white;
            color: navy;
        }
    </style>
</head>

<body>
<!--<div id="debug-info">-->
<!--    <h3>User Proficiency</h3>-->
<!--    <pre>--><?php //echo json_encode($userProficiencyData, JSON_PRETTY_PRINT); ?><!--</pre>-->
<!--    <h3>Company Preferences</h3>-->
<!--    <pre>--><?php //echo json_encode($companyData, JSON_PRETTY_PRINT); ?><!--</pre>-->
<!--</div>-->

<div class="container my-3">
    <div class="d-flex justify-content-center">
        <a href="companyDashboard.php" class="btn btn-dash me-2">Dashboard</a>
        <a href="internshipdetails.php?id=<?php echo $internship_id; ?>&company_id=<?php echo $company_id?>" class="btn btn-back">Back</a>
    </div>
</div>

</body>

</html>