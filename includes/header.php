<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickBite</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script>
        function toggleNav() {
            const sideNav = document.getElementById('side-nav');
            sideNav.style.width = sideNav.style.width === '250px' ? '0' : '250px';
        }
    </script>
</head>
<body>
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
                        <a href="../../Cust/User/login.php">Login</a>
                        <a href="../../Cust/User/register.php">Register</a>
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

    <!-- Include the side navigation bar -->
    <?php include 'navbar.php'; ?>

    <hr>
    <style>
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
</body>
</html>