<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('config.php');

$user_id = $_SESSION['user_id'];
$sql = "SELECT name FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
} else {
    echo "User not found.";
    header("Location: login.php");
    exit();
}

// Retrieve the resume path for the current user
$resume_sql = "SELECT resume_path FROM resumes WHERE user_id = $user_id";
$resume_result = $conn->query($resume_sql);
$resume_exists = $resume_result->num_rows > 0;

$conn->close();

// Determine the file name and extension if a resume exists
if ($resume_exists) {
    $resume_row = $resume_result->fetch_assoc();
    $resume_path = $resume_row['resume_path'];
    $file_name = basename($resume_path);
    $file_extension = pathinfo($resume_path, PATHINFO_EXTENSION);
} else {
    $file_name = "Resume not uploaded";
    $file_extension = null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resume Management</title>
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
        .resume-info {
            margin-left: 20px;
            display: inline-block;
            vertical-align: top;
        }
        .resume-link {
            display: flex;
            align-items: center;
        }
        .resume-link img {
            width: 50px;  /* Adjust the width as desired */
            height: 50px; /* Adjust the height as desired */
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Resume Management</h2>
    <p>Welcome, <?php echo $name; ?>!</p>
    <p>Manage your resume here:</p>

    <div style="display: flex;">
        <div>
            <a href="upload_resume.php" class="btn">Upload/Edit Resume</a>
            <br><br>
            <a href="delete_resume.php" class="btn">Delete Resume</a>
            <br><br>
            <a href="dashboard.php" class="btn">Dashboard</a>
            <br><br>
        </div>
        <div class="resume-info">
            <?php if ($resume_exists): ?>
                <div class="resume-link">
                    <?php
                    // Determine icon based on file extension
                    if ($file_extension === 'pdf') {
                        echo '<img src="Images/File%20Icons/pdf_icon.png" alt="PDF Icon">';
                    } elseif ($file_extension === 'doc' || $file_extension === 'docx') {
                        echo '<img src="Images/File%20Icons/word_icon.png" alt="Word Icon">';
                    } else {
                        echo '<img src="Images/File%20Icons/generic_file_icon.png" alt="File Icon">';
                    }
                    ?>
                    <a href="<?php echo $resume_path; ?>" target="_blank"><?php echo $file_name; ?></a>
                </div>
            <?php else: ?>
                <p><?php echo $file_name; ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>