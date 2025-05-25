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

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch all menu rows
$sql = "SELECT * FROM menu";
$result = $connection->query($sql);

if (!$result) {
    die("Invalid query: " . $connection->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .btn-custom {
            background-color: #6db3b3;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #5aa3a3;
            color: white;
        }
    </style>
<body>
    
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>List Of Menu</h2>
            <a class="btn btn-sm btn-custom" href="../Menu/createMenu.php" role="button">New Menu</a>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="text-center align-middle">
                <tr>
                    <th>Menu ID</th>
                    <th>Image</th>
                    <th>Menu</th>
                    <th>Description</th>
                    <th>Price (RM)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['menuID']) ?></td>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="../assets/images/<?= htmlspecialchars($row['image']) ?>" alt="Image" style="width: 120px; height: auto; display: block; margin: 0 auto;">
                            <?php else: ?>
                                <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['price']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-custom " href="../Menu/editMenu.php?id=<?= $row['menuID'] ?>">Edit</a>
                            <a class="btn btn-sm btn-custom " href="../Menu/deleteMenu.php?id=<?= $row['menuID'] ?>" onclick="return confirm('Are you sure you want to delete this menu item?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
