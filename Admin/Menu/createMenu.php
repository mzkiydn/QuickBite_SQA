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

$name = "";
$description = "";
$price = "";

$errorMessage = "";
$successMessage = "";

function generateMenuID($connection) {
    $prefix = "M";
    $sql = "SELECT MAX(menuID) AS maxID FROM menu WHERE menuID LIKE '$prefix%'";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if ($row['maxID']) {
        $num = intval(substr($row['maxID'], 1)) + 1;
        return $prefix . str_pad($num, 3, "0", STR_PAD_LEFT);
    } else {
        return $prefix . "001";
    }
}

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $image = $_FILES["image"] ?? null;

    do {
        if (empty($name) || empty($description) || empty($price) || !$image || $image["error"] != 0) {
            $errorMessage = "All fields including image are required.";
            break;
        }


        $menuID = generateMenuID($connection);

        // Handle image upload
        $imageName = $menuID . "_" . basename($image["name"]);
        $targetDir = __DIR__ . "/../assets/images/";
        $targetFile = $targetDir . $imageName;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!move_uploaded_file($image["tmp_name"], $targetFile)) {
            $errorMessage = "Failed to upload image.";
            break;
        }

        // Save to DB
        $insertQuery = "INSERT INTO menu (menuID, name, description, price, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($insertQuery);
        $stmt->bind_param("sssss", $menuID, $name, $description, $price, $imageName);

        if (!$stmt->execute()) {
            $errorMessage = "Insert failed: " . $stmt->error;
            break;
        }


        header("Location: ../Menu/menuAdmin.php");
        exit;

    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- same CSS as menuAdmin.php -->
</head>
<body>

    <div class="container min-vh-100 d-flex flex-column justify-content-start pt-4 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow p-4">
                    <h2 class="mb-4 text-center">New Menu</h2>

                    <?php if (!empty($errorMessage)) : ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong><?= $errorMessage ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                             <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="<?= $name ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" value="<?= $description ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (RM)</label>
                            <input type="text" class="form-control" name="price" value="<?= $price ?>">
                        </div>

                        <?php if (!empty($successMessage)) : ?>
                            <div class="mb-3">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><?= $successMessage ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a class="btn btn-outline-primary" href="../Menu/menuAdmin.php">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</body>
</html>
