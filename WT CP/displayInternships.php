<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

include('config.php');

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

function applyForInternship($user_id, $company_id, $internship_id, $conn) {
    // Define the content of the message (internship ID)
    $content = $internship_id;
    $student = 1;

    // Insert the message into the messages table
    $sql = "INSERT INTO messages (sender_id, receiver_id, content, from_student) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iisi', $user_id, $company_id, $content, $student);
    $stmt->execute();

    // Close the statement
    $stmt->close();
}

function calculateAHP($companyPreferencesData, $userProficiencyData, $CIids, $conn) {
    $companyPreferencesMatrix = [];
    $rows = 0;
    foreach ($companyPreferencesData as $skills) {
        $rows++;
        $companyPreferencesMatrix[] = array_values($skills);
    }

    $userProficiencyArray = array_values($userProficiencyData);
    $skillsCount = count($companyPreferencesMatrix[0][0]);
    $skills = ["ai", "ml", "ds", "app_dev", "game_dev", "web_dev"];

    $criteriaWeights = [];
    $sum=0;
    for($i=0; $i < $skillsCount; $i++) {
        $sum+=$userProficiencyArray[$i];
    }
    for($i=0; $i < $skillsCount; $i++) {
        $criteriaWeights[$i] = $userProficiencyArray[$i] / $sum;
    }

//    print_r($companyPreferencesMatrix);
//    print_r($companyPreferencesMatrix[0][1]);
//    echo $companyPreferencesMatrix[0][1][$skills[1]];
//    print_r(count($companyPreferencesMatrix[0]));
    $numCompanies = count($companyPreferencesMatrix);

    $normalizedAlternativeMatrix = [];
    for($k = 0; $k < $skillsCount; $k++) {
        for($j = 0; $j < $numCompanies; $j++) {
            $numInternships = count($companyPreferencesMatrix[$j]);
            $sum = 0;

            for($i = 0; $i < $numInternships; $i++) {
                $sum += $companyPreferencesMatrix[$j][$i][$skills[$k]];
//                print_r($companyPreferencesMatrix[$j][$i][$skills[$k]]);
            }
//            echo $sum."<br>";

            for($i = 0; $i < $numInternships; $i++) {
                if($sum!==0) {
                    $normalizedAlternativeMatrix[$j][$i][$skills[$k]] = $companyPreferencesMatrix[$j][$i][$skills[$k]] / $sum;
                }
                else {
                    $normalizedAlternativeMatrix[$j][$i][$skills[$k]] = $companyPreferencesMatrix[$j][$i][$skills[$k]];
                }
            }
        }
    }

//    print_r($normalizedAlternativeMatrix);

    $alternativeIDs = [];
    $alternativeScores = [];
    $count = 0;

    for($i = 0; $i < $numCompanies; $i++) {
        $numInternships = count($normalizedAlternativeMatrix[$i]);
//        print_r($normalizedAlternativeMatrix[$i]);
        for($j = 0; $j < $numInternships; $j++) {
            $score = 0;
            for($k = 0; $k < $skillsCount; $k++) {
                $score += $normalizedAlternativeMatrix[$i][$j][$skills[$k]] * $criteriaWeights[$k];
            }

            $alternativeScores[$count] = $score;
            $alternativeIDs[$count] = $CIids[$count];
            $count++;
        }
    }

//    echo "<br>";
//    print_r($alternativeScores);
//    echo "<br>";
//    print_r($alternativeIDs);

    array_multisort($alternativeScores, SORT_DESC, $alternativeIDs);
//    echo "<br>";
//    print_r($alternativeScores);
//    echo "<br>";
//    print_r($alternativeIDs);


    echo "<p style='font-family: Anton, sans-serif; font-size: 50px; color: navy; text-align: center; margin-bottom: 10px'><strong>BEST SUITED INTERNSHIPS FOR <span style='color: deepskyblue'>YOU</span>!</strong></p>";
    for ($i = 0; $i < $count; $i++) {
        $company_id = $alternativeIDs[$i]['company_id'];
        $internship_id = $alternativeIDs[$i]['internship_id'];

        // Join `internshipCard` with `countries`, `states`, and `cities` tables
        $internship_sql = "SELECT ic.*, co.name AS country_name, s.name AS state_name, ci.name AS city_name
                           FROM internshipCard AS ic
                           LEFT JOIN countries AS co ON ic.country_id = co.id
                           LEFT JOIN states AS s ON ic.state_id = s.id
                           LEFT JOIN cities AS ci ON ic.city_id = ci.id
                           WHERE ic.company_id = ? AND ic.id = ?";
        $internship_stmt = $conn->prepare($internship_sql);
        $internship_stmt->bind_param('ii', $company_id, $internship_id);
        $internship_stmt->execute();
        $internships_result = $internship_stmt->get_result();

        $company_sql = "SELECT name, email FROM company WHERE id = ?";
        $company_stmt = $conn->prepare($company_sql);
        $company_stmt->bind_param('i', $company_id);
        $company_stmt->execute();
        $company_result = $company_stmt->get_result();

        if ($company_result->num_rows > 0) {
            $company_row = $company_result->fetch_assoc();
            $company_name = $company_row['name'];
        } else {
            echo "Company not found.";
            exit();
        }

        if ($internships_result->num_rows > 0) {
            echo '<div class="container">';
            echo '<div class="row justify-content-center">';
            while ($internship = $internships_result->fetch_assoc()) {
                echo '<div class="col-md-6 col-lg-4 col-xl-3 mb-3">';
                echo '<div class="card shadow-sm h-100" style="border: 3px solid navy; transition: transform 0.3s ease, box-shadow 0.3s ease;">';
                echo '<div class="card-body">';
                echo '<h3 class="card-title" style="font-family: Audiowide, sans-serif">' . htmlspecialchars($internship['title']) . '</h3>';
                echo '<hr style="border: 3px solid navy">';
                echo '<p><strong>Company Name:</strong> ' . htmlspecialchars($company_name) . '</p>';
                echo '<p><strong>Duration:</strong> ' . htmlspecialchars($internship['duration']) . '</p>';
                echo '<p><strong>Stipend:</strong> ' . htmlspecialchars($internship['stipend']) . '</p>';

                // Display location depending on work_from_home value
                echo '<p><strong>Location:</strong> ';
                if ($internship['work_from_home'] == 1) {
                    echo 'Work From Home';
                } else {
                    // Construct location from city, state, and country names
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
                echo '</p>';

                echo '<p><strong>Subjects:</strong> ' . htmlspecialchars($internship['type']) . '</p>';

                echo '<div class="d-flex justify-content-end mt-3">';
                echo '<a href="internshipdetails.php?id=' . htmlspecialchars($internship['id']) . '" class="btn btn-info" style="text-decoration: none; margin-right: 10px;">VIEW DETAILS</a>';
                // Apply button that sends a message
                echo '<form method="POST" style="display: inline-block;">
                      <input type="hidden" name="company_id" value="' . htmlspecialchars($company_id) . '">';
                echo '<input type="hidden" name="internship_id" value="' . htmlspecialchars($internship['id']) . '">';
                echo '<button type="submit" class="btn btn-success">APPLY</button>';
                echo '</form>';

                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning text-center">No internships found.</div>';
        }
    }

    return $alternativeScores;
}

$alternativeScores = calculateAHP($companyPreferencesData, $userProficiencyData, $CIids, $conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['company_id']) && isset($_POST['internship_id'])) {
    $company_id = intval($_POST['company_id']);
    $internship_id = intval($_POST['internship_id']);
    applyForInternship($user_id, $company_id, $internship_id, $conn);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Internships</title>
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
            background-color: navy;
            font-size: 13px;
            color: white;
            padding: 6px 7px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: cyan;
            color: navy;
        }
        .card {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .card:hover {
            transform: scale(1.03);
            box-shadow: 0 0 50px deepskyblue;
            border-color: deepskyblue;
        }
        .btn-bottom {
            padding: 10px 20px;
            margin: auto;
            text-align: center;
        }
    </style>
</head>

<body>
<div style="text-align: center; margin-bottom: 20px;">
    <button class="btn-bottom" onclick="location.href='dashboard.php'">Back to Dashboard</button>
</div>

</body>
</html>