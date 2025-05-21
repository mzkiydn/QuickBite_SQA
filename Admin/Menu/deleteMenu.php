<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "quickbite";

// Create connection
$connection = new mysqli($servername, $username, $password, $database);

if (isset($_GET["id"])) {
    $menuID = $_GET["id"];

    $sql = "DELETE FROM menu WHERE menuID = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $menuID);

    if ($stmt->execute()) {
        header("Location: ../Menu/menuAdmin.php");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
} else {
    header("Location: ../Menu/menuAdmin.php");
    exit;
}
?>
