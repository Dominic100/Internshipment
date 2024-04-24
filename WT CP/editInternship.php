<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: companyLogin.php");
    exit();
}

include('config.php');

$company_id = $_SESSION['company_id'];
$internship_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($internship_id === 0) {
    echo "Invalid internship ID.";
    exit();
}

// If the form is submitted, process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $title = $_POST['title'];
    $start_date = $_POST['start_date'];
    $duration = $_POST['duration'];
    $registration_due_date = $_POST['registration_due_date'];
    $stipend = $_POST['stipend'];
    $company_info = $_POST['company_info'];
    $internship_info = $_POST['internship_info'];
    $requirements = $_POST['requirements'];
    $skills = $_POST['skills'];
    $who_can_apply = $_POST['who_can_apply'];
    $perks = $_POST['perks'];
    $number_of_openings = $_POST['number_of_openings'];

    // Retrieve preferences from form
    $ai = $_POST['ai'];
    $ml = $_POST['ml'];
    $ds = $_POST['ds'];
    $app_dev = $_POST['app_dev'];
    $game_dev = $_POST['game_dev'];
    $web_dev = $_POST['web_dev'];

    // Construct the type attribute based on preferences
    $type = '';
    $subjects = [
        'AI' => $ai,
        'ML' => $ml,
        'DS' => $ds,
        'App Development' => $app_dev,
        'Game Development' => $game_dev,
        'Web Development' => $web_dev
    ];

    foreach ($subjects as $subject => $value) {
        if ($value >= 1) {
            $type .= $subject . ',';
        }
    }

    $type = rtrim($type, ',');

    $data_sql = "UPDATE internshipData SET title = ?, start_date = ?, duration = ?, registration_due_date = ?, stipend = ?, type = ?, company_info = ?, internship_info = ?, requirements = ?, skills = ?, who_can_apply = ?, perks = ?, number_of_openings = ? WHERE id = ? AND company_id = ?";
    $data_stmt = $conn->prepare($data_sql);
    $data_stmt->bind_param('sdidisssssssiii', $title, $start_date, $duration, $registration_due_date, $stipend, $type, $company_info, $internship_info, $requirements, $skills, $who_can_apply, $perks, $number_of_openings, $internship_id, $company_id);
    $data_stmt->execute();

    $preferences_sql = "UPDATE company_preferences SET ai = ?, ml = ?, ds = ?, app_dev = ?, game_dev = ?, web_dev = ? WHERE id = ? AND internship_id = ?";
    $preferences_stmt = $conn->prepare($preferences_sql);
    $preferences_stmt->bind_param('iiiiiiii', $ai, $ml, $ds, $app_dev, $game_dev, $web_dev, $company_id, $internship_id);
    $preferences_stmt->execute();

    $conn->close();

    header("Location: companyInternships.php");
    exit();
}

$internship_sql = "SELECT * FROM internshipData WHERE id = ? AND company_id = ?";
$internship_stmt = $conn->prepare($internship_sql);
$internship_stmt->bind_param('ii', $internship_id, $company_id);
$internship_stmt->execute();
$internship_result = $internship_stmt->get_result();

if ($internship_result->num_rows > 0) {
    $internship = $internship_result->fetch_assoc();
} else {
    echo "Internship not found.";
    exit();
}

$preferences_sql = "SELECT * FROM company_preferences WHERE id = ? AND internship_id = ?";
$preferences_stmt = $conn->prepare($preferences_sql);
$preferences_stmt->bind_param('ii', $company_id, $internship_id);
$preferences_stmt->execute();
$preferences_result = $preferences_stmt->get_result();

if ($preferences_result->num_rows > 0) {
    $preferences = $preferences_result->fetch_assoc();
} else {
    echo "Preferences not found.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Internship</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JavaScript and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        .btn-update {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-update:hover {
            background-color: white;
            color: #4CAF50;
        }
        .btn-back {
            background-color: #ffd700;
            color: #4f3c00;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-back:hover {
            background-color: #4f3c00;
            color: #ffd700;
        }

        textarea.form-control {
            resize: vertical;
        }
    </style>
</head>

<body>

<div class="container" style="border: 3px solid deepskyblue">
    <h2 class="mb-4" style="font-family: Anton, sans-serif; font-size: 50px; color: navy; margin-top: 20px">Edit Internship</h2>

    <form action="editInternship.php?id=<?php echo $internship_id; ?>" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($internship['title']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date:</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($internship['start_date']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label">Duration (in days):</label>
            <input type="number" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($internship['duration']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="registration_due_date" class="form-label">Registration Due Date:</label>
            <input type="date" class="form-control" id="registration_due_date" name="registration_due_date" value="<?php echo htmlspecialchars($internship['registration_due_date']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="stipend" class="form-label">Stipend:</label>
            <input type="number" step="0.01" class="form-control" id="stipend" name="stipend" value="<?php echo htmlspecialchars($internship['stipend']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="company_info" class="form-label">Company Info:</label>
            <textarea class="form-control" id="company_info" name="company_info" rows="3" required><?php echo htmlspecialchars($internship['company_info']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="internship_info" class="form-label">Internship Info:</label>
            <textarea class="form-control" id="internship_info" name="internship_info" rows="3" required><?php echo htmlspecialchars($internship['internship_info']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="requirements" class="form-label">Requirements:</label>
            <textarea class="form-control" id="requirements" name="requirements" rows="3" required><?php echo htmlspecialchars($internship['requirements']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="skills" class="form-label">Skills:</label>
            <textarea class="form-control" id="skills" name="skills" rows="3" required><?php echo htmlspecialchars($internship['skills']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="who_can_apply" class="form-label">Who Can Apply:</label>
            <textarea class="form-control" id="who_can_apply" name="who_can_apply" rows="3" required><?php echo htmlspecialchars($internship['who_can_apply']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="perks" class="form-label">Perks:</label>
            <textarea class="form-control" id="perks" name="perks" rows="3" required><?php echo htmlspecialchars($internship['perks']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="number_of_openings" class="form-label">Number of Openings:</label>
            <input type="number" class="form-control" id="number_of_openings" name="number_of_openings" value="<?php echo htmlspecialchars($internship['number_of_openings']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="ai" class="form-label">AI:</label>
            <select id="ai" name="ai" class="form-select">
                <option value="9" <?php echo ($preferences['ai'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['ai'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['ai'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['ai'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['ai'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['ai'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="ml" class="form-label">ML:</label>
            <select id="ml" name="ml" class="form-select">
                <option value="9" <?php echo ($preferences['ml'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['ml'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['ml'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['ml'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['ml'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['ml'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="ds" class="form-label">DS:</label>
            <select id="ds" name="ds" class="form-select">
                <option value="9" <?php echo ($preferences['ds'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['ds'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['ds'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['ds'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['ds'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['ds'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="app_dev" class="form-label">App Development:</label>
            <select id="app_dev" name="app_dev" class="form-select">
                <option value="9" <?php echo ($preferences['app_dev'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['app_dev'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['app_dev'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['app_dev'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['app_dev'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['app_dev'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="game_dev" class="form-label">Game Development:</label>
            <select id="game_dev" name="game_dev" class="form-select">
                <option value="9" <?php echo ($preferences['game_dev'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['game_dev'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['game_dev'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['game_dev'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['game_dev'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['game_dev'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="web_dev" class="form-label">Web Development:</label>
            <select id="web_dev" name="web_dev" class="form-select">
                <option value="9" <?php echo ($preferences['web_dev'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['web_dev'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['web_dev'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['web_dev'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['web_dev'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['web_dev'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <button class="btn btn-update" type="submit">Update Internship</button>

    </form>
    <button class="btn btn-back" type="submit" onclick="window.location.href='companyInternships.php'" style="margin-top: 10px;">Back</button>

</div>

</body>

</html>