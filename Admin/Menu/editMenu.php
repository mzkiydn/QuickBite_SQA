<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "quickbite";

// Include all PHP files from the includes folder
    foreach (glob("../../includes/*.php") as $file) {
        include $file;
    }

// Connect to the database
$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$menuID = $_GET['id'] ?? $_POST['menuID'] ?? null;
if (!$menuID) {
    die("Invalid ID");
}



// Fetch existing data
$sql = "SELECT * FROM menu WHERE menuID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $menuID);
$stmt->execute();
$result = $stmt->get_result();
$menu = $result->fetch_assoc();

if (!$menu) {
    die("Menu item not found");
}

// Update if form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $image = $_FILES["image"] ?? null;

    $updateSql = "UPDATE menu SET name=?, description=?, price=?" . ($image && $image["error"] == 0 ? ", image=?" : "") . " WHERE menuID=?";
    if ($image && $image["error"] == 0) {
        $imageName = $menuID . "_" . basename($image["name"]);
        $targetFile = __DIR__ . "/../assets/images/" . $imageName;
        move_uploaded_file($image["tmp_name"], $targetFile);
        $updateStmt = $connection->prepare($updateSql);
        $updateStmt->bind_param("ssdss", $name, $description, $price, $imageName, $menuID);
    } else {
        $updateStmt = $connection->prepare($updateSql);
        $updateStmt->bind_param("ssds", $name, $description, $price, $menuID);
    }

    $updateStmt->execute();
    header("Location: menuAdmin.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Optional: same CSS as menuAdmin.php -->
</head>
<body>

    <div class="container min-vh-100 d-flex flex-column justify-content-start pt-4 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow p-4">
                    <h2 class="mb-4 text-center">Edit Menu</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="menuID" value="<?= htmlspecialchars($menuID) ?>">
                        <div class="mb-3">
                                <label class="form-label">Update Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <?php if (!empty($menu['image'])): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($menu['image']) ?>" alt="Current Image" style="max-width: 100px;" class="mt-2">
                                <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Menu Name</label>
                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($menu['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($menu['description']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (RM)</label>
                            <input type="number" step="0.01" class="form-control" name="price" value="<?= htmlspecialchars($menu['price']) ?>" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="menuAdmin.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

