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

    do {
        if (empty($name) || empty($description) || empty($price)) {
            $errorMessage = "All fields are required";
            break;
        }

        // ✅ Generate unique menuID
        $menuID = generateMenuID($connection);

        // ✅ Insert new menu including menuID
        $insertQuery = "INSERT INTO menu (menuID, name, description, price) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($insertQuery);
        $stmt->bind_param("ssss", $menuID, $name, $description, $price);

        if (!$stmt->execute()) {
            $errorMessage = "Insert failed: " . $stmt->error;
            break;
        }

        $name = $description = $price = "";
        $successMessage = "New Menu Added!";
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
    <title>Manage Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/css/styles.css">

</head>
<body>
    <div class="container">
        <h2>New Menu</h2>

        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><?= $errorMessage ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?= $name ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Description</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="description" value="<?= $description ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Price</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="price" value="<?= $price ?>">
                </div>
            </div>

            <?php if (!empty($successMessage)) : ?>
                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-6">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><?= $successMessage ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="../Menu/menuAdmin.php">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>