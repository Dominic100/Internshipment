<?php
include('config.php'); // Include the database connection

$country_id = isset($_GET['country_id']) ? intval($_GET['country_id']) : 0;

if ($country_id > 0) {
    // Fetch states for the given country_id
    $stmt = $conn->prepare("SELECT id, name FROM states WHERE country_id = ?");
    $stmt->bind_param('i', $country_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $states = [];
    while ($row = $result->fetch_assoc()) {
        $states[] = $row;
    }

    // Return states as JSON response
    header('Content-Type: application/json');
    echo json_encode($states);
}
?>