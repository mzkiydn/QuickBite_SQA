<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "quickbite";

// Include all PHP files from the includes folder
    foreach (glob("../../includes/*.php") as $file) {
        include $file;
    }

// Create connection
$connection = new mysqli($servername, $username, $password, $database);

$menuID = "";
$name = "";
$description = "";
$price = "";

$errorMessage = "";
$successMessage = "";

// Get ID from URL
if (!isset($_GET["id"])) {
    header("Location: menuAdmin.php");
    exit;
}

$menuID = $_GET["id"];

// Fetch existing data
$sql = "SELECT * FROM menu WHERE menuID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $menuID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header("Location: menuAdmin.php");
    exit;
}

$row = $result->fetch_assoc();
$name = $row["name"];
$description = $row["description"];
$price = $row["price"];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];

    do {
        if (empty($name) || empty($description) || empty($price)) {
            $errorMessage = "All fields are required";
            break;
        }

        $sql = "UPDATE menu SET name=?, description=?, price=? WHERE menuID=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssi", $name, $description, $price, $menuID);

        if (!$stmt->execute()) {
            $errorMessage = "Update failed: " . $stmt->error;
            break;
        }

        $successMessage = "Menu updated successfully!";
        header("Location: ../Menu/menuAdmin.php");
        exit;

    } while (false);
}
?>

<!-- HTML form for editing -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    
</head>
<body>

<div class="container">
    <h2>Edit Menu</h2>
    <?php if (!empty($errorMessage)) : ?>
        <div class="alert alert-warning"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" value="<?= $name ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" class="form-control" name="description" value="<?= $description ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="text" class="form-control" name="price" value="<?= $price ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="menuAdmin.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
