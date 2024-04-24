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
    $title = $_POST['title'];

    function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    $start_date = $_POST['start_date'];
    $registration_due_date = $_POST['registration_due_date'];

    if (!validateDate($start_date) || !validateDate($registration_due_date)) {
        echo "Invalid date format";
        exit();
    }


// Convert the dates to the correct format before inserting them into the database
//    $start_date = date('Y-m-d', strtotime($start_date));
//    $registration_due_date = date('Y-m-d', strtotime($registration_due_date));
//    $start_date = date('Y-m-d', strtotime($_POST['start_date']));
    $duration = $_POST['duration'];
//    $registration_due_date = date('Y-m-d', strtotime($_POST['registration_due_date']));
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

    // Work from Home / Location handling
    $work_type = $_POST['work_type'];
    if ($work_type === 'work_from_home') {
        $work_from_home = 1;
        $country_id = $state_id = $city_id = null;
    } else {
        $work_from_home = 0;
        $country_id = isset($_POST['country_id']) ? intval($_POST['country_id']) : null;
        $state_id = isset($_POST['state_id']) ? intval($_POST['state_id']) : null;
        $city_id = isset($_POST['city_id']) ? intval($_POST['city_id']) : null;
    }

    // Insert data into internshipData table
    $data_sql = "INSERT INTO internshipdata (title, company_id, start_date, duration, registration_due_date, stipend, type, company_info, internship_info, requirements, skills, who_can_apply, perks, number_of_openings, work_from_home, country_id, state_id, city_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $data_stmt = $conn->prepare($data_sql);
    $data_stmt->bind_param('sisisisssssssiiiii', $title, $company_id, $start_date, $duration, $registration_due_date, $stipend, $type, $company_info, $internship_info, $requirements, $skills, $who_can_apply, $perks, $number_of_openings, $work_from_home, $country_id, $state_id, $city_id);
    $data_stmt->execute();
    if ($data_stmt->error) {
        echo "Data insertion error: " . $data_stmt->error;
        exit();
    }

    // Get the ID of the newly inserted internship
    $internship_id = $conn->insert_id;

    // Insert data into internshipCard table
    $card_sql = "INSERT INTO internshipcard (id, title, company_id, start_date, duration, registration_due_date, stipend, type, work_from_home, country_id, state_id, city_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $card_stmt = $conn->prepare($card_sql);
    $card_stmt->bind_param('issisissiiii', $internship_id, $title, $company_id, $start_date, $duration, $registration_due_date, $stipend, $type, $work_from_home, $country_id, $state_id, $city_id);
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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Load Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <style>
        .form-control {
            margin-bottom: 10px;
        }

        /* Button styles */
        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

<div class="container my-5" style="border: 3px solid deepskyblue;">
    <h2 class="mb-4" style="font-family: Anton, sans-serif; font-size: 50px; color: navy; margin-top: 20px">Post Internship</h2>

    <form action="postInternship.php" method="POST">
        <!-- Form elements -->
        <div class="row mb-3">
            <div class="col-lg-6 col-md-12 mb-3">
                <label for="title" class="form-label">Title:</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <label for="start_date" class="form-label">Start Date:</label>
                <input type="text" id="start_date" name="start_date" class="form-control" required placeholder="YYYY-MM-DD">
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <label for="duration" class="form-label">Duration (in days):</label>
                <input type="number" id="duration" name="duration" class="form-control" required min="1">
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <label for="registration_due_date" class="form-label">Registration Due Date:</label>
                <input type="text" id="registration_due_date" name="registration_due_date" class="form-control" required placeholder="YYYY-MM-DD">
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <label for="stipend" class="form-label">Stipend:</label>
                <input type="number" step="0.01" id="stipend" name="stipend" class="form-control" required min="0">
            </div>

            <!-- Work Type (Work from Home/Location) -->
            <div class="col-lg-6 col-md-12 mb-3">
                <label for="work_type" class="form-label">Work Type:</label>
                <select id="work_type" name="work_type" class="form-select" required>
                    <option value="work_from_home">Work from Home</option>
                    <option value="location">Location</option>
                </select>
            </div>
        </div>

        <!-- Country, State, and City dropdowns -->
        <div class="mb-3" id="country_group" style="display: none;">
            <label for="country_id" class="form-label">Country:</label>
            <select id="country_id" name="country_id" class="form-select">
                <option value="">Select Country</option>
                <?php
                $countries = $conn->query("SELECT id, name FROM countries ORDER BY name");
                while ($country = $countries->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($country['id']) . '">' . htmlspecialchars($country['name']) . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="mb-3" id="state_group" style="display: none;">
            <label for="state_id" class="form-label">State:</label>
            <select id="state_id" name="state_id" class="form-select">
                <option value="">Select State</option>
                <!-- States will be loaded based on selected country -->
            </select>
        </div>

        <div class="mb-3" id="city_group" style="display: none;">
            <label for="city_id" class="form-label">City:</label>
            <select id="city_id" name="city_id" class="form-select">
                <option value="">Select City</option>
                <!-- Cities will be loaded based on selected state -->
            </select>
        </div>

        <!-- Textarea fields -->
        <div class="mb-3">
            <label for="company_info" class="form-label">Company Info:</label>
            <textarea id="company_info" name="company_info" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="internship_info" class="form-label">Internship Info:</label>
            <textarea id="internship_info" name="internship_info" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="requirements" class="form-label">Requirements:</label>
            <textarea id="requirements" name="requirements" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="skills" class="form-label">Skills:</label>
            <textarea id="skills" name="skills" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="who_can_apply" class="form-label">Who Can Apply:</label>
            <textarea id="who_can_apply" name="who_can_apply" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="perks" class="form-label">Perks:</label>
            <textarea id="perks" name="perks" class="form-control" rows="3" required></textarea>
        </div>

        <!-- Number of Openings -->
        <div class="mb-3">
            <label for="number_of_openings" class="form-label">Number of Openings:</label>
            <input type="number" id="number_of_openings" name="number_of_openings" class="form-control" required min="1">
        </div>

        <!-- Preferences -->
        <div class="row mb-3">
            <?php
            $subjects = [
                'ai' => 'AI',
                'ml' => 'ML',
                'ds' => 'DS',
                'app_dev' => 'App Development',
                'game_dev' => 'Game Development',
                'web_dev' => 'Web Development'
            ];

            foreach ($subjects as $key => $name) {
                echo '<div class="col-lg-2 col-md-4 col-sm-6 mb-3">';
                echo '<label for="' . $key . '" class="form-label">' . $name . ':</label>';
                echo '<select id="' . $key . '" name="' . $key . '" class="form-select">';
                for ($i = 9; $i >= 1; $i -= 2) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                echo '<option value="0">0</option>';
                echo '</select>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="row mb-3">
            <div class="col-12 text-center">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 text-center">
                <button class="btn btn-primary" type="button" onclick="window.location.href='companyInternships.php'">Back</button>
            </div>
        </div>
    </form>
</div>

<!-- Bootstrap 5 JavaScript and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('work_type').addEventListener('change', function() {
        var workType = this.value;
        if (workType === 'location') {
            document.getElementById('country_group').style.display = 'block';
            document.getElementById('state_group').style.display = 'none';
            document.getElementById('city_group').style.display = 'none';
        } else {
            document.getElementById('country_group').style.display = 'none';
            document.getElementById('state_group').style.display = 'none';
            document.getElementById('city_group').style.display = 'none';
        }
    });

    document.getElementById('country_id').addEventListener('change', function() {
        var countryId = this.value;
        if (countryId) {
            // Fetch states based on country ID
            fetch('getStates.php?country_id=' + countryId)
                .then(response => response.json())
                .then(data => {
                    var stateDropdown = document.getElementById('state_id');
                    stateDropdown.innerHTML = '<option value="">Select State</option>'; // Reset options
                    data.forEach(function(state) {
                        var option = document.createElement('option');
                        option.value = state.id;
                        option.textContent = state.name;
                        stateDropdown.appendChild(option);
                    });
                    document.getElementById('state_group').style.display = 'block';
                });
        } else {
            // Hide state and city groups if no country is selected
            document.getElementById('state_group').style.display = 'none';
            document.getElementById('city_group').style.display = 'none';
        }
    });

    document.getElementById('state_id').addEventListener('change', function() {
        var stateId = this.value;
        if (stateId) {
            // Fetch cities based on state ID
            fetch('getCities.php?state_id=' + stateId)
                .then(response => response.json())
                .then(data => {
                    var cityDropdown = document.getElementById('city_id');
                    cityDropdown.innerHTML = '<option value="">Select City</option>'; // Reset options
                    data.forEach(function(city) {
                        var option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        cityDropdown.appendChild(option);
                    });
                    document.getElementById('city_group').style.display = 'block';
                });
        } else {
            // Hide city group if no state is selected
            document.getElementById('city_group').style.display = 'none';
        }
    });
</script>

</body>

</html>