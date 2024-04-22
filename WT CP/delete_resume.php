<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('config.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT resume_path FROM resumes WHERE user_id = $user_id";
$result = $conn->query($sql);

$redirect_to_resume_intermediate = false;
$message = '';

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $resume_path = $row['resume_path'];

    if (file_exists($resume_path)) {
        if (unlink($resume_path)) {
            $delete_sql = "DELETE FROM resumes WHERE user_id = $user_id";
            if ($conn->query($delete_sql)) {
                $message = "Resume deleted successfully.";
                $redirect_to_resume_intermediate = true;
            } else {
                $message = "Error deleting resume record from the database.";
            }
        } else {
            $message = "Error deleting resume file.";
        }
    } else {
        $message = "Resume file not found.";
    }
} else {
    $message = "No resume found for the current user.";
    $redirect_to_resume_intermediate = true;
}

$conn->close();

if ($redirect_to_resume_intermediate) {
    echo "<div>$message</div>";
    echo '<meta http-equiv="refresh" content="3;url=resumeIntermediate.php">';
} else {
    echo "<div>$message</div>";
}
?>