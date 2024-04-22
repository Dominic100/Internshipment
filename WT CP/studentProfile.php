<?php
session_start();

include('config.php');

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id === 0) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        echo "User ID not provided.";
        exit();
    }
}

if(!isset($user_id)) {
    if (!isset($_SESSION['user_id'])) {
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

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
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
    <h2>Student Profile</h2>
    <p>ID: <?php echo $id; ?></p>
    <p>Name: <?php echo $name; ?></p>
    <p>Email: <?php echo $email; ?></p>

    <h3>Scores:</h3>
    <?php if ($scores_result->num_rows > 0): ?>
        <ul>
            <?php while ($score_row = $scores_result->fetch_assoc()): ?>
                <li>AI: <?php echo $score_row['ai']; ?></li>
                <li>ML: <?php echo $score_row['ml']; ?></li>
                <li>DS: <?php echo $score_row['ds']; ?></li>
                <li>App_Dev: <?php echo $score_row['app_dev']; ?></li>
                <li>Game_Dev: <?php echo $score_row['game_dev']; ?></li>
                <li>Web_Dev: <?php echo $score_row['web_dev']; ?></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No scores found.</p>
    <?php endif; ?>

    <h3>Proficiency:</h3>
    <?php if ($proficiency_result->num_rows > 0): ?>
        <?php $proficiency_row = $proficiency_result->fetch_assoc(); ?>
        <ul>
            <li>AI: <?php echo $proficiency_row['ai']; ?></li>
            <li>ML: <?php echo $proficiency_row['ml']; ?></li>
            <li>DS: <?php echo $proficiency_row['ds']; ?></li>
            <li>App_Dev: <?php echo $proficiency_row['app_dev']; ?></li>
            <li>Game_Dev: <?php echo $proficiency_row['game_dev']; ?></li>
            <li>Web_Dev: <?php echo $proficiency_row['web_dev']; ?></li>
        </ul>
    <?php else: ?>
        <p>No proficiency data found.</p>
    <?php endif; ?>

    <h3>Resume:</h3>
    <?php if ($resume_result->num_rows > 0): ?>
        <?php $resume_row = $resume_result->fetch_assoc(); ?>
        <p><a href="<?php echo $resume_row['resume_path']; ?>" target="_blank">View Resume</a></p>
    <?php else: ?>
        <p>Resume not uploaded</p>
    <?php endif; ?>
</div>

</body>
</html>