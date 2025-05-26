<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "quickbite";

// Connect to the database
$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$menuID = $_GET['id'] ?? null;
if (!$menuID) {
    die("Invalid ID");
}

$sql = "DELETE FROM menu WHERE menuID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $menuID);
$stmt->execute();

header("Location: menuAdmin.php");
exit;
?>
