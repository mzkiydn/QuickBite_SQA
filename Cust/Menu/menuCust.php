<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "quickbite";
$orderID = $_GET['orderID'] ?? null;

$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if orderID is set in URL or session
if (!empty($_GET['orderID'])) {
    $orderID = $_GET['orderID'];
    $_SESSION['orderID'] = $orderID; // save to session
} elseif (!empty($_SESSION['orderID'])) {
    $orderID = $_SESSION['orderID'];
} else {
    // No orderID in URL or session, create a new order
    $orderID = uniqid('O'); // unique orderID e.g. O643a7f5b12c3f
    $userID = 1; // hardcoded for now, replace with real logged in user ID if available
    $date = date('Y-m-d');
    $status = 'unpaid';
    $total = 0;

    // Insert new order into database
    $stmt = $connection->prepare("INSERT INTO `Order` (orderID, date, total, status, userID) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssisi', $orderID, $date, $total, $status, $userID);
    $stmt->execute();
    $stmt->close();

    $_SESSION['orderID'] = $orderID;
}

// Include all PHP files from the includes folder
foreach (glob("../../includes/*.php") as $file) {
    include $file;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu for Customers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .menu-image {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
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
</head>
<body>

<div class="container list mt-4">
    <h2 class="mb-4">Available Menu</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-primary text-center">
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

        <?php
        // Create connection
        $connection = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Read all rows from the menu table
        $sql = "SELECT * FROM menu";
        $result = $connection->query($sql);

        if (!$result) {
            die("Invalid query: " . $connection->error);
        }

        // Display menu items
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['menuID']) . "</td>";
            
            echo "<td class='text-center'>";
            if (!empty($row['image'])) {
                echo "<img src='../../Admin/assets/images/" . htmlspecialchars($row['image']) . "' alt='Menu Image' style='width: 100px; height: auto; display: block; margin: 0 auto;'>";
            } else {
                echo "No image";
            }
            echo "</td>";
            
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['price']) . "</td>";
            echo "<td><a class='btn btn-sm btn-custom' href='../Order/addToOrder.php?menuID={$row['menuID']}&orderID={$orderID}'>Add to Order</a></td>";
            echo "</tr>";
        }
        ?>

        </tbody>
    </table>
</div>

</body>
</html>
