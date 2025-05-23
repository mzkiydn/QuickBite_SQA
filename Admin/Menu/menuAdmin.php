<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "quickbite";

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
    <script>
        function toggleNav() {
            const sideNav = document.getElementById('side-nav');
            sideNav.style.width = sideNav.style.width === '250px' ? '0' : '250px';
        }
    </script>
    <header>
        <div class="header-container">
            <div class="header-left">
                <span class="menu-icon" onclick="toggleNav()">&#9776;</span> <!-- Menu Icon -->
                <h1>QuickBite</h1>
            </div>
            <div class="header-right">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="user-info">
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</span>
                        <a href="">Profile</a>
                        <a href="">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="guest-info">
                        <a href="">Login</a>
                        <a href="">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <style>
        header {
    background-color: #6db3b3; /* Soft teal */
    color: #fff;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.header-container {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.header-left h1 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
}

.menu-icon {
    font-size: 24px;
    cursor: pointer;
    margin-right: 10px;
    color: #fff;
}

.header-right {
    display: flex;
    align-items: center;
}

.header-right .user-info,
.header-right .guest-info {
    display: flex;
    gap: 15px;
}

.header-right a {
    color: #fff;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.header-right a:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Side Navigation Bar */
    .side-nav {
        height: 100%;
        width: 0;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #111;
        overflow-x: hidden;
        transition: 0.5s;
        padding-top: 60px;
    }

    .side-nav a {
        padding: 10px 15px;
        text-decoration: none;
        font-size: 18px;
        color: #fff;
        display: block;
        transition: 0.3s;
    }

    .side-nav a:hover {
        background-color: #575757;
    }

    .side-nav .close-btn {
        position: absolute;
        top: 0;
        right: 15px;
        font-size: 36px;
        color: #fff;
    }

     /* Menu Icon */
        .menu-icon {
            font-size: 24px;
            cursor: pointer;
            margin-right: 10px;
        }

        .header-left {
            display: flex;
            align-items: center;
        }
    </style>
<body>
    <div id="side-nav" class="side-nav">
        <a href="javascript:void(0)" class="close-btn" onclick="toggleNav()">&times;</a>
        <a href="index.php">User</a>
        <a href="order.php">Order</a>
        <a href="../Menu/menuAdmin.php">Menu</a>
        <a href="payment.php">Payment</a>
    </div>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>List Of Menu</h2>
            <a class="btn btn-primary" href="../Menu/createMenu.php" role="button">New Menu</a>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Menu ID</th>
                    <th>Menu</th>
                    <th>Description</th>
                    <th>Price (RM)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['menuID']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['price']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="editMenu.php?id=<?= $row['menuID'] ?>">Edit</a>
                            <a class="btn btn-sm btn-danger" href="../Menu/deleteMenu.php?id=<?= $row['menuID'] ?>" onclick="return confirm('Are you sure you want to delete this menu item?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
