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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JavaScript and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-center">Resume Management</h2>
            <div class="row mt-4">
                <div class="col-md-6">
                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <a href="upload_resume.php" class="btn btn-primary">Upload/Edit Resume</a>
                        <a href="delete_resume.php" class="btn btn-danger">Delete Resume</a>
                        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Resume information -->
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <h5 class="card-subtitle mb-3">Resume Status:</h5>
                            <?php if ($resume_exists): ?>
                                <div class="resume-info d-flex align-items-center">
                                    <?php
                                    // Determine icon based on file extension
                                    if ($file_extension === 'pdf') {
                                        echo '<img src="Images/File%20Icons/pdf_icon.png" alt="PDF Icon" class="img-fluid me-3" style="width: 50px; height: 50px;">';
                                    } elseif ($file_extension === 'doc' || $file_extension === 'docx') {
                                        echo '<img src="Images/File%20Icons/word_icon.png" alt="Word Icon" class="img-fluid me-3" style="width: 50px; height: 50px;">';
                                    } else {
                                        echo '<img src="Images/File%20Icons/generic_file_icon.png" alt="File Icon" class="img-fluid me-3" style="width: 50px; height: 50px;">';
                                    }
                                    ?>
                                    <a href="<?php echo htmlspecialchars($resume_path); ?>" target="_blank" class="text-decoration-none"><?php echo htmlspecialchars($file_name); ?></a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">Resume not uploaded.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>