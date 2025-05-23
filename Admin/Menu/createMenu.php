<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "quickbite";

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
</head>
<div id="side-nav" class="side-nav">
        <a href="javascript:void(0)" class="close-btn" onclick="toggleNav()">&times;</a>
        <a href="index.php">User</a>
        <a href="order.php">Order</a>
        <a href="../Menu/menuAdmin.php">Menu</a>
        <a href="payment.php">Payment</a>
    </div>
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