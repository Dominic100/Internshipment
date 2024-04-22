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
    $ai = $_POST['ai'];
    $ml = $_POST['ml'];
    $ds = $_POST['ds'];
    $app_dev = $_POST['app_dev'];
    $game_dev = $_POST['game_dev'];
    $web_dev = $_POST['web_dev'];

    $sql = "INSERT INTO company_preferences (id, ai, ml, ds, app_dev, game_dev, web_dev)
            VALUES ('$company_id', '$ai', '$ml', '$ds', '$app_dev', '$game_dev', '$web_dev')
            ON DUPLICATE KEY UPDATE
            ai = VALUES(ai),
            ml = VALUES(ml),
            ds = VALUES(ds),
            app_dev = VALUES(app_dev),
            game_dev = VALUES(game_dev),
            web_dev = VALUES(web_dev)";

    if ($conn->query($sql) === TRUE) {
        header("Location: companyInternResults.php");
        echo "Records inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Preferences</title>
</head>
<body>
<h2>Company Preferences</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="ai">AI:</label>
    <select name="ai">
        <option value="9">9</option>
        <option value="7">7</option>
        <option value="5">5</option>
        <option value="3">3</option>
        <option value="1">1</option>
        <option value="0">Nah</option>
    </select><br>

    <label for="ml">ML:</label>
    <select name="ml">
        <option value="9">9</option>
        <option value="7">7</option>
        <option value="5">5</option>
        <option value="3">3</option>
        <option value="1">1</option>
        <option value="0">Nah</option>
    </select><br>

    <label for="ds">DS:</label>
    <select name="ds">
        <option value="9">9</option>
        <option value="7">7</option>
        <option value="5">5</option>
        <option value="3">3</option>
        <option value="1">1</option>
        <option value="0">Nah</option>
    </select><br>

    <label for="app_dev">App Development:</label>
    <select name="app_dev">
        <option value="9">9</option>
        <option value="7">7</option>
        <option value="5">5</option>
        <option value="3">3</option>
        <option value="1">1</option>
        <option value="0">Nah</option>
    </select><br>

    <label for="game_dev">Game Development:</label>
    <select name="game_dev">
        <option value="9">9</option>
        <option value="7">7</option>
        <option value="5">5</option>
        <option value="3">3</option>
        <option value="1">1</option>
        <option value="0">Nah</option>
    </select><br>

    <label for="web_dev">Web Development:</label>
    <select name="web_dev">
        <option value="9">9</option>
        <option value="7">7</option>
        <option value="5">5</option>
        <option value="3">3</option>
        <option value="1">1</option>
        <option value="0">Nah</option>
    </select><br>

    <input type="submit" value="Submit">
</form>
</body>
</html>

<?php
$conn->close();
?>