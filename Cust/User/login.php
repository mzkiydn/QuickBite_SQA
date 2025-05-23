<?php
session_start(); // Start the session

// Include connection file
foreach (glob("../../includes/conn.php") as $file) {
    include $file;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Database connection
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "quickbite";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed");
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM User WHERE username=? AND password=? AND role=?");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Login success
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        if ($role === "admin") {
            header("Location: ../../Admin/Menu/menuAdmin.php");
        } elseif ($role === "customer") {
            header("Location: ../Menu/menuCust.php");
        }
        exit();
    } else {
        // Login failed
        echo "<script>alert('Username or password is incorrect'); window.location.href='login.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body 
        {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #6db3b3;
        }

        .login-container 
        {
            width: 500px;
            margin: 200px auto;
            padding: 30px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to QuickBite</h2>
        <?php
        // Display error message if it exists
        if (isset($_SESSION['error'])) {
            echo '<div class="error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Clear the error message after displaying
        }
        ?>
        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="customer">Customer</option>
            </select>

            <button type="submit">Login</button>

            <p style="text-align:center; margin-top:10px;">
            <a href="forgot_password.php">Forgot Password?</a>
            </p>
            <p style="text-align:center; margin-top:5px;">
            Don't have an account? <a href="register.php">Register</a>
            </p>
        </form>
    </div>
</body>
</html>