<?php
include('config.php'); // Include the database connection

$state_id = isset($_GET['state_id']) ? intval($_GET['state_id']) : 0;

if ($state_id > 0) {
    // Fetch cities for the given state_id
    $stmt = $conn->prepare("SELECT id, name FROM cities WHERE state_id = ?");
    $stmt->bind_param('i', $state_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cities = [];
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }

    // Return cities as JSON response
    header('Content-Type: application/json');
    echo json_encode($cities);
}
?>