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

// Check if the internship exists and belongs to the logged-in company
$check_sql = "SELECT * FROM internshipdata WHERE id = ? AND company_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('ii', $internship_id, $company_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo "Internship not found or you do not have permission to delete this internship.";
    exit();
}

// Begin transaction
$conn->begin_transaction();

try {
    // Delete from company_preferences
    $delete_preferences_sql = "DELETE FROM company_preferences WHERE internship_id = ? AND id = ?";
    $delete_preferences_stmt = $conn->prepare($delete_preferences_sql);
    $delete_preferences_stmt->bind_param('ii', $internship_id, $company_id);
    $delete_preferences_stmt->execute();

    // Delete from internshipCard
    $delete_card_sql = "DELETE FROM internshipcard WHERE id = ? AND company_id = ?";
    $delete_card_stmt = $conn->prepare($delete_card_sql);
    $delete_card_stmt->bind_param('ii', $internship_id, $company_id);
    $delete_card_stmt->execute();

    // Delete from internshipData
    $delete_data_sql = "DELETE FROM internshipdata WHERE id = ? AND company_id = ?";
    $delete_data_stmt = $conn->prepare($delete_data_sql);
    $delete_data_stmt->bind_param('ii', $internship_id, $company_id);
    $delete_data_stmt->execute();

    // Commit transaction
    $conn->commit();

    // Redirect to companyInternships.php after successful deletion
    header("Location: companyInternships.php");
    exit();
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "Error deleting internship: " . $e->getMessage();
}

// Close database connection
$conn->close();
?>