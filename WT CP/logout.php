<?php
session_start();


$user_set = isset($_SESSION['user_id']);
$_SESSION = array();

session_destroy();

if($user_set) {
    header("Location: login.php");
}
else {
    header("Location: companyLogin.php");
}

exit();
?>