<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header("Location: companyLogin.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wtcp";

$company_id = $_SESSION['company_id'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
//    $id = $_POST['id'];
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

    // Remove trailing comma
    $type = rtrim($type, ',');

    // Validate data (you may add additional validation as needed)

    // Insert data into internshipData table
    $data_sql = "INSERT INTO internshipData (title, company_id, start_date, duration, registration_due_date, stipend, type, company_info, internship_info, requirements, skills, who_can_apply, perks, number_of_openings)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $data_stmt = $conn->prepare($data_sql);
    $data_stmt->bind_param('sididisssssssi', $title, $company_id, $start_date, $duration, $registration_due_date, $stipend, $type, $company_info, $internship_info, $requirements, $skills, $who_can_apply, $perks, $number_of_openings);
    $data_stmt->execute();

    // Get the ID of the newly inserted internship
    $internship_id = $conn->insert_id;

    // Insert data into internshipCard table
    $card_sql = "INSERT INTO internshipCard (id, title, company_id, start_date, duration, registration_due_date, stipend, type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $card_stmt = $conn->prepare($card_sql);
    $card_stmt->bind_param('isissdss', $internship_id, $title, $company_id, $start_date, $duration, $registration_due_date, $stipend, $type);
    $card_stmt->execute();

    // Insert data into company_preferences table
    $preferences_sql = "INSERT INTO company_preferences (id, ai, ml, ds, app_dev, game_dev, web_dev, internship_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    ai = VALUES(ai),
                    ml = VALUES(ml),
                    ds = VALUES(ds),
                    app_dev = VALUES(app_dev),
                    game_dev = VALUES(game_dev),
                    web_dev = VALUES(web_dev)";

    $preferences_stmt = $conn->prepare($preferences_sql);
    $preferences_stmt->bind_param('iiiiiiii', $company_id, $ai, $ml, $ds, $app_dev, $game_dev, $web_dev, $internship_id);
    $preferences_stmt->execute();

    // Close the database connection
    $conn->close();

    // Redirect to companyInternships.php after successful insertion
    header("Location: companyInternships.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Internship</title>
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
    <h2>Post Internship</h2>

    <form action="postInternship.php" method="POST">
        <!-- Title -->
        <div class="form-control">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <!-- Start Date -->
        <div class="form-control">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>

        <!-- Duration -->
        <div class="form-control">
            <label for="duration">Duration (in days):</label>
            <input type="number" id="duration" name="duration" required>
        </div>

        <!-- Registration Due Date -->
        <div class="form-control">
            <label for="registration_due_date">Registration Due Date:</label>
            <input type="date" id="registration_due_date" name="registration_due_date" required>
        </div>

        <!-- Stipend -->
        <div class="form-control">
            <label for="stipend">Stipend:</label>
            <input type="number" step="0.01" id="stipend" name="stipend" required>
        </div>

        <!-- Company Info -->
        <div class="form-control">
            <label for="company_info">Company Info:</label>
            <textarea id="company_info" name="company_info" rows="3" required></textarea>
        </div>

        <!-- Internship Info -->
        <div class="form-control">
            <label for="internship_info">Internship Info:</label>
            <textarea id="internship_info" name="internship_info" rows="3" required></textarea>
        </div>

        <!-- Requirements -->
        <div class="form-control">
            <label for="requirements">Requirements:</label>
            <textarea id="requirements" name="requirements" rows="3" required></textarea>
        </div>

        <!-- Skills -->
        <div class="form-control">
            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" rows="3" required></textarea>
        </div>

        <!-- Who Can Apply -->
        <div class="form-control">
            <label for="who_can_apply">Who Can Apply:</label>
            <textarea id="who_can_apply" name="who_can_apply" rows="3" required></textarea>
        </div>

        <!-- Perks -->
        <div class="form-control">
            <label for="perks">Perks:</label>
            <textarea id="perks" name="perks" rows="3" required></textarea>
        </div>

        <!-- Number of Openings -->
        <div class="form-control">
            <label for="number_of_openings">Number of Openings:</label>
            <input type="number" id="number_of_openings" name="number_of_openings" required>
        </div>

        <!-- AI -->
        <div class="form-control">
            <label for="ai">AI:</label>
            <select id="ai" name="ai">
                <option value="9">9</option>
                <option value="7">7</option>
                <option value="5">5</option>
                <option value="3">3</option>
                <option value="1">1</option>
                <option value="0">Nah</option>
            </select>
        </div>

        <!-- ML -->
        <div class="form-control">
            <label for="ml">ML:</label>
            <select id="ml" name="ml">
                <option value="9">9</option>
                <option value="7">7</option>
                <option value="5">5</option>
                <option value="3">3</option>
                <option value="1">1</option>
                <option value="0">Nah</option>
            </select>
        </div>

        <!-- DS -->
        <div class="form-control">
            <label for="ds">DS:</label>
            <select id="ds" name="ds">
                <option value="9">9</option>
                <option value="7">7</option>
                <option value="5">5</option>
                <option value="3">3</option>
                <option value="1">1</option>
                <option value="0">Nah</option>
            </select>
        </div>

        <!-- App Development -->
        <div class="form-control">
            <label for="app_dev">App Development:</label>
            <select id="app_dev" name="app_dev">
                <option value="9">9</option>
                <option value="7">7</option>
                <option value="5">5</option>
                <option value="3">3</option>
                <option value="1">1</option>
                <option value="0">Nah</option>
            </select>
        </div>

        <!-- Game Development -->
        <div class="form-control">
            <label for="game_dev">Game Development:</label>
            <select id="game_dev" name="game_dev">
                <option value="9">9</option>
                <option value="7">7</option>
                <option value="5">5</option>
                <option value="3">3</option>
                <option value="1">1</option>
                <option value="0">Nah</option>
            </select>
        </div>

        <!-- Web Development -->
        <div class="form-control">
            <label for="web_dev">Web Development:</label>
            <select id="web_dev" name="web_dev">
                <option value="9">9</option>
                <option value="7">7</option>
                <option value="5">5</option>
                <option value="3">3</option>
                <option value="1">1</option>
                <option value="0">Nah</option>
            </select>
        </div>

        <button class="btn" type="submit">Submit</button>
    </form>
</div>

</body>
</html>