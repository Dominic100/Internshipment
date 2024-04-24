<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
    }

    include('config.php');

    // Get the user's ID from the session
    $user_id = $_SESSION['user_id'];

    // Get the user's name and email from the database
    $sql = "SELECT name, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
    } else {
    echo "User not found.";
    exit();
    }

    // Retrieve scores for the user from the scores table
    $scores_sql = "SELECT ai, ml, ds, app_dev, game_dev, web_dev FROM scores WHERE id = ?";
    $scores_stmt = $conn->prepare($scores_sql);
    $scores_stmt->bind_param('i', $user_id);
    $scores_stmt->execute();
    $scores_result = $scores_stmt->get_result();

    // Initialize an array to store scores
    $scores = [];
    while ($row = $scores_result->fetch_assoc()) {
        $scores = [
            'ai' => $row['ai'],
            'ml' => $row['ml'],
            'ds' => $row['ds'],
            'app_dev' => $row['app_dev'],
            'game_dev' => $row['game_dev'],
            'web_dev' => $row['web_dev']
        ];
    }

    // Retrieve proficiencies for the user from the user_proficiency table
    $proficiency_sql = "SELECT ai, ml, ds, app_dev, game_dev, web_dev FROM user_proficiency WHERE id = ?";
    $proficiency_stmt = $conn->prepare($proficiency_sql);
    $proficiency_stmt->bind_param('i', $user_id);
    $proficiency_stmt->execute();
    $proficiency_result = $proficiency_stmt->get_result();

    // Initialize an array to store proficiencies
    $proficiencies = [];
    while ($row = $proficiency_result->fetch_assoc()) {
        $proficiencies = [
            'ai' => $row['ai'],
            'ml' => $row['ml'],
            'ds' => $row['ds'],
            'app_dev' => $row['app_dev'],
            'game_dev' => $row['game_dev'],
            'web_dev' => $row['web_dev']
        ];
    }

    // Define a mapping from short forms to full forms for subjects
    $subject_mapping = [
    'ai' => 'Artificial Intelligence',
    'ml' => 'Machine Learning',
    'ds' => 'Data Science',
    'app_dev' => 'App Development',
    'game_dev' => 'Game Development',
    'web_dev' => 'Web Development'
    ];

    $conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Hello, <?php echo htmlspecialchars($name); ?>!</h2>
    <p>This is the Test Field. You will be evaluated to check your proficiencies in six subjects: AI, ML, DS, App Development, Game Development, and Web Development. If you decide to skip a test, it will be considered that you are not familiar with the subject.</p>

    <div class="text-center">
        <!-- Iterate through each subject and display the button with score and proficiency beside it -->
        <?php
        foreach ($subject_mapping as $short_form => $full_form) {
            // Retrieve the score and proficiency for the current subject
            $score = isset($scores[$short_form]) ? $scores[$short_form] : 'N/A';
            $proficiency = isset($proficiencies[$short_form]) ? $proficiencies[$short_form] : 'N/A';

            // Create a row for each subject
            echo '<div class="row justify-content-center mb-2">';

            // Button column
            echo '<div class="col-6 col-md-5 col-lg-4 col-xl-3">';
            echo '<a href="ProficiencyEvaluation/' . strtolower(str_replace(' ', '_', $short_form)) . 'Evaluation.php" class="btn btn-primary w-100">';
            echo $full_form;
            echo '</a>';
            echo '</div>';

            // Information column
            echo '<div class="col-6 col-md-5 col-lg-4 col-xl-3 text-start">';
            echo "Score: $score, Proficiency: $proficiency";
            echo '</div>';

            echo '</div>';
        }
        ?>
        <br><br>
        <div class="row justify-content-center mt-3">
            <div class="col-4 col-md-3 col-lg-2">
                <a href="dashboard.php" class="btn btn-success w-100">Dashboard</a>
            </div>
            <div class="col-4 col-md-3 col-lg-2">
                <a href="logout.php" class="btn btn-danger w-100">Logout</a>
            </div>
        </div>
    </div>
</div>


</body>
</html>