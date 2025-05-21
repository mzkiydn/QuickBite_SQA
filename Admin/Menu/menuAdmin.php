<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container list">
        <h2>List Of Menu</h2>
        <a class="btn btn-primary" href="../Menu/createMenu.php" role="button">New Menu</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>menuID</th>
                    <th>menu</th>
                    <th>description</th>
                    <th>price(RM)</th>
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

                //read data of each row
                while($row = $result->fetch_assoc()){
                    echo "
                    <tr>
                        <td>$row[menuID]</td>
                        <td>$row[name]</td>
                        <td>$row[description]</td>
                        <td>$row[price]</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='../Menu/editMenu.php?id=$row[menuID]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='../Menu/deleteMenu.php?id=$row[menuID]'>Delete</a>
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