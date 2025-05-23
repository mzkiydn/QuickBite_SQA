<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "quickbite";
$orderID = $_GET['orderID'] ?? null;

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
    
</head>
<body>
     
    <div class="container list">
        <h2>Available Menu</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>menuID</th>
                    <th>Menu</th>
                    <th>Description</th>
                    <th>Price (RM)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "quickbite";

                // Create connection
                $connection = new mysqli($servername, $username, $password, $database);

                // read all row from database table
                $sql = "SELECT * FROM menu";
                $result = $connection->query($sql);

                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                // Display data rows
                while($row = $result->fetch_assoc()){
                    echo "
                    <tr>
                        <td>{$row['menuID']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['description']}</td>
                        <td>{$row['price']}</td>
                        <td>
                            <a class='btn btn-success btn-sm' href='addToOrder.php?id={$row['menuID']}'>Add to Order</a>
                        </td>
                    </tr>
                    ";
                }
                ?>

            </tbody>
        </table>
    </div>
</body>
</html>
