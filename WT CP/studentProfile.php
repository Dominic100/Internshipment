<?php
session_start();

include('config.php');

$isgetUserID = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$issetUserID = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($isgetUserID) {
    $user_id = intval($_GET['user_id']);
}
else if ($issetUserID) {
    $user_id = intval($_SESSION['user_id']);
}

if ($user_id === 0) {
    echo "User ID not provided.";
    exit();
}

$issetCompanyID = isset($_GET['company_id']);
$issetInternshipID = isset($_GET['internship_id']);

if($issetCompanyID) {
    $company_id = intval($_GET['company_id']);
}
else {
    $company_id = 0;
}

if($issetInternshipID) {
    $internship_id = intval($_GET['internship_id']);
}
else {
    $internship_id = 0;
}

if(!isset($user_id)) {
    if (!$issetCompanyID && !$issetInternshipID) {
        header("Location: login.php");
        exit();
    }
}

$user_sql = "SELECT id, name, email FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user_row = $user_result->fetch_assoc();
    $name = $user_row['name'];
    $email = $user_row['email'];
    $id = $user_row['id'];
} else {
    echo "User not found.";
    exit();
}

$scores_sql = "SELECT * FROM scores WHERE id = ?";
$scores_stmt = $conn->prepare($scores_sql);
$scores_stmt->bind_param('i', $user_id);
$scores_stmt->execute();
$scores_result = $scores_stmt->get_result();

$proficiency_sql = "SELECT * FROM user_proficiency WHERE id = ?";
$proficiency_stmt = $conn->prepare($proficiency_sql);
$proficiency_stmt->bind_param('i', $user_id);
$proficiency_stmt->execute();
$proficiency_result = $proficiency_stmt->get_result();

$resume_sql = "SELECT resume_path FROM resumes WHERE user_id = ?";
$resume_stmt = $conn->prepare($resume_sql);
$resume_stmt->bind_param('i', $user_id);
$resume_stmt->execute();
$resume_result = $resume_stmt->get_result();

function approachStudent($userID, $companyID, $internshipID, $conn) {
    // Define the content of the message (internship ID)
    $content = "Hey, we would like to reach out to you!";
    $student = 0;
    // Insert the message into the messages table
    $sql = "INSERT INTO messages (sender_id, receiver_id, content, from_student) VALUES (?, ?, ?, ?) on duplicate key update sender_id = values(sender_id),
                                                                                                                                 receiver_id = values(receiver_id),
                                                                                                                                                   content = values(content),
                                                                                                                                                                 from_student = values(from_student)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iisi', $companyID, $userID, $content, $student);
    $stmt->execute();

    // Check for any errors
//    if ($stmt->error) {
//        echo "Error: " . $stmt->error;
//    } else {
//        echo '<div class="alert alert-success">You have successfully applied for the internship!</div>';
//    }

    // Close the statement
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $issetCompanyID && $issetInternshipID) {
    approachStudent($user_id, $company_id, $internship_id, $conn);
}
//else {
//    echo $issetCompanyID ? "company":"no";
//    echo $issetInternshipID ? "internship":"no";
//    echo $_SERVER['REQUEST_METHOD'];
//    echo "problem";
//}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Profile</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JavaScript and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<div class="container mt-4">
    <h2 class="mb-4">Student Profile</h2>
    <div class="card mb-4">
        <div class="card-header">
            <h3>ID: <?php echo htmlspecialchars($id); ?></h3>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        </div>
    </div>

    <!-- Scores Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Scores:</h3>
        </div>
        <div class="card-body">
            <?php if ($scores_result->num_rows > 0): ?>
                <ul class="list-group list-group-flush">
                    <?php while ($score_row = $scores_result->fetch_assoc()): ?>
                        <li class="list-group-item">AI: <?php echo $score_row['ai']; ?></li>
                        <li class="list-group-item">ML: <?php echo $score_row['ml']; ?></li>
                        <li class="list-group-item">DS: <?php echo $score_row['ds']; ?></li>
                        <li class="list-group-item">App Dev: <?php echo $score_row['app_dev']; ?></li>
                        <li class="list-group-item">Game Dev: <?php echo $score_row['game_dev']; ?></li>
                        <li class="list-group-item">Web Dev: <?php echo $score_row['web_dev']; ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No scores found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Proficiency Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Proficiency:</h3>
        </div>
        <div class="card-body">
            <?php if ($proficiency_result->num_rows > 0): ?>
                <?php $proficiency_row = $proficiency_result->fetch_assoc(); ?>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">AI: <?php echo $proficiency_row['ai']; ?></li>
                    <li class="list-group-item">ML: <?php echo $proficiency_row['ml']; ?></li>
                    <li class="list-group-item">DS: <?php echo $proficiency_row['ds']; ?></li>
                    <li class="list-group-item">App Dev: <?php echo $proficiency_row['app_dev']; ?></li>
                    <li class="list-group-item">Game Dev: <?php echo $proficiency_row['game_dev']; ?></li>
                    <li class="list-group-item">Web Dev: <?php echo $proficiency_row['web_dev']; ?></li>
                </ul>
            <?php else: ?>
                <p>No proficiency data found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Resume Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Resume:</h3>
        </div>
        <div class="card-body">
            <?php if ($resume_result->num_rows > 0): ?>
                <?php $resume_row = $resume_result->fetch_assoc(); ?>
                <p><a href="<?php echo htmlspecialchars($resume_row['resume_path']); ?>" target="_blank" class="btn btn-link">View Resume</a></p>
            <?php else: ?>
                <p>Resume not uploaded</p>
            <?php endif; ?>
        </div>
    </div>

    <?php
    if($issetCompanyID) {
        echo '<div class="text-center mb-4">
                      <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
                            <input type="hidden" name="internship_id" value="<?php echo $internship_id; ?>">
                            <button type="submit" class="btn btn-success" formmethod="post">Approach</button>
                        </form>

                    </div>';
    }?>

    <div class="text-center mb-4">
        <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
    </div>
</div>

</body>

</html>