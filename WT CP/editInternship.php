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
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-control {
            margin-bottom: 15px;
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
    <h2>Edit Internship</h2>

    <form action="editInternship.php?id=<?php echo $internship_id; ?>" method="POST">
        <!-- Title -->
        <div class="form-control">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($internship['title']); ?>" required>
        </div>

        <!-- Start Date -->
        <div class="form-control">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($internship['start_date']); ?>" required>
        </div>

        <!-- Duration -->
        <div class="form-control">
            <label for="duration">Duration (in days):</label>
            <input type="number" id="duration" name="duration" value="<?php echo htmlspecialchars($internship['duration']); ?>" required>
        </div>

        <!-- Registration Due Date -->
        <div class="form-control">
            <label for="registration_due_date">Registration Due Date:</label>
            <input type="date" id="registration_due_date" name="registration_due_date" value="<?php echo htmlspecialchars($internship['registration_due_date']); ?>" required>
        </div>

        <!-- Stipend -->
        <div class="form-control">
            <label for="stipend">Stipend:</label>
            <input type="number" step="0.01" id="stipend" name="stipend" value="<?php echo htmlspecialchars($internship['stipend']); ?>" required>
        </div>

        <!-- Company Info -->
        <div class="form-control">
            <label for="company_info">Company Info:</label>
            <textarea id="company_info" name="company_info" rows="3" required><?php echo htmlspecialchars($internship['company_info']); ?></textarea>
        </div>

        <!-- Internship Info -->
        <div class="form-control">
            <label for="internship_info">Internship Info:</label>
            <textarea id="internship_info" name="internship_info" rows="3" required><?php echo htmlspecialchars($internship['internship_info']); ?></textarea>
        </div>

        <!-- Requirements -->
        <div class="form-control">
            <label for="requirements">Requirements:</label>
            <textarea id="requirements" name="requirements" rows="3" required><?php echo htmlspecialchars($internship['requirements']); ?></textarea>
        </div>

        <!-- Skills -->
        <div class="form-control">
            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" rows="3" required><?php echo htmlspecialchars($internship['skills']); ?></textarea>
        </div>

        <!-- Who Can Apply -->
        <div class="form-control">
            <label for="who_can_apply">Who Can Apply:</label>
            <textarea id="who_can_apply" name="who_can_apply" rows="3" required><?php echo htmlspecialchars($internship['who_can_apply']); ?></textarea>
        </div>

        <!-- Perks -->
        <div class="form-control">
            <label for="perks">Perks:</label>
            <textarea id="perks" name="perks" rows="3" required><?php echo htmlspecialchars($internship['perks']); ?></textarea>
        </div>

        <!-- Number of Openings -->
        <div class="form-control">
            <label for="number_of_openings">Number of Openings:</label>
            <input type="number" id="number_of_openings" name="number_of_openings" value="<?php echo htmlspecialchars($internship['number_of_openings']); ?>" required>
        </div>

        <!-- AI -->
        <div class="form-control">
            <label for="ai">AI:</label>
            <select id="ai" name="ai">
                <option value="9" <?php echo ($preferences['ai'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['ai'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['ai'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['ai'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['ai'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['ai'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <!-- ML -->
        <div class="form-control">
            <label for="ml">ML:</label>
            <select id="ml" name="ml">
                <option value="9" <?php echo ($preferences['ml'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['ml'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['ml'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['ml'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['ml'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['ml'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <!-- DS -->
        <div class="form-control">
            <label for="ds">DS:</label>
            <select id="ds" name="ds">
                <option value="9" <?php echo ($preferences['ds'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['ds'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['ds'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['ds'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['ds'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['ds'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <!-- App Development -->
        <div class="form-control">
            <label for="app_dev">App Development:</label>
            <select id="app_dev" name="app_dev">
                <option value="9" <?php echo ($preferences['app_dev'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['app_dev'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['app_dev'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['app_dev'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['app_dev'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['app_dev'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <!-- Game Development -->
        <div class="form-control">
            <label for="game_dev">Game Development:</label>
            <select id="game_dev" name="game_dev">
                <option value="9" <?php echo ($preferences['game_dev'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['game_dev'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['game_dev'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['game_dev'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['game_dev'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['game_dev'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <!-- Web Development -->
        <div class="form-control">
            <label for="web_dev">Web Development:</label>
            <select id="web_dev" name="web_dev">
                <option value="9" <?php echo ($preferences['web_dev'] == 9) ? 'selected' : ''; ?>>9</option>
                <option value="7" <?php echo ($preferences['web_dev'] == 7) ? 'selected' : ''; ?>>7</option>
                <option value="5" <?php echo ($preferences['web_dev'] == 5) ? 'selected' : ''; ?>>5</option>
                <option value="3" <?php echo ($preferences['web_dev'] == 3) ? 'selected' : ''; ?>>3</option>
                <option value="1" <?php echo ($preferences['web_dev'] == 1) ? 'selected' : ''; ?>>1</option>
                <option value="0" <?php echo ($preferences['web_dev'] == 0) ? 'selected' : ''; ?>>Nah</option>
            </select>
        </div>

        <button class="btn" type="submit">Update Internship</button>
    </form>
</div>

</body>
</html>