<?php
$sname = "localhost:3308";
$uname = "root";
$password = "";
$db_name = "tugaswad";

$conn = new mysqli($sname, $uname, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
