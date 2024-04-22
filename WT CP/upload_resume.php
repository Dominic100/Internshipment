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

// Check if the user already has a resume
$resume_sql = "SELECT resume_path FROM resumes WHERE user_id = $user_id";
$resume_result = $conn->query($resume_sql);
$resume_row = $resume_result->fetch_assoc();
$resume_path = isset($resume_row['resume_path']) ? $resume_row['resume_path'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle resume file upload
    if (isset($_FILES['resume'])) {
        $file = $_FILES['resume'];
        $filename = basename($file['name']);
        $file_tmp_name = $file['tmp_name'];

        // Specify the directory where the resumes will be saved
        $upload_directory = 'uploads/resumes/';

        // Ensure the upload directory exists, if not create it
        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0755, true);
        }

        // Construct the full path for the resume file
        $file_path = $upload_directory . $user_id . '_' . $filename;

        // Move the uploaded file to the designated directory
        if (move_uploaded_file($file_tmp_name, $file_path)) {
            // Check if the user already has a resume
            if ($resume_path) {
                // Update the existing resume path in the database
                $update_sql = "UPDATE resumes SET resume_path = '$file_path' WHERE user_id = $user_id";
                $conn->query($update_sql);
            } else {
                // Insert a new resume record in the database
                $insert_sql = "INSERT INTO resumes (user_id, resume_path) VALUES ($user_id, '$file_path')";
                $conn->query($insert_sql);
            }

            // Provide JavaScript code to display the success prompt
            echo '<script>alert("Resume uploaded successfully.");</script>';
            // Refresh page to update displayed resume information
            echo '<meta http-equiv="refresh" content="0;url=upload_resume.php">';
        } else {
            echo "Error uploading resume: Unable to move file to the designated directory.";
        }
    } else {
        echo "Please upload a resume file.";
    }
}
// else {
//    echo "Invalid request method.";
//}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload/Edit Resume</title>
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
    <h2>Upload/Edit Resume</h2>
    <p>Welcome, <?php echo $name; ?>!</p>

    <form action="upload_resume.php" method="POST" enctype="multipart/form-data">
        <label for="resume">Choose your resume file:</label>
        <input type="file" id="resume" name="resume" required>
        <br><br>
        <input type="submit" class="btn" value="Upload Resume">
    </form>

    <br><br>
    <a href="resumeIntermediate.php" class="btn">Back</a>

    <br><br>
    <!-- Display existing resume file name or "Resume not uploaded" -->
    <div>
        <?php
        if ($resume_path) {
            // Extract the file name from the path
            $file_name = basename($resume_path);
            echo "Your current resume: $file_name";
        } else {
            echo "Resume not uploaded";
        }
        ?>
    </div>
</div>

</body>
</html>