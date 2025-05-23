<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and validate inputs
    $username = trim($_POST['username']);
    $password_raw = trim($_POST['password']);
    $role = "customer";

    // Validate username and password length (max 15 chars)
    if (strlen($username) > 15) {
        $_SESSION['error'] = "Username must be 15 characters or less.";
    } elseif (strlen($password_raw) > 15) {
        $_SESSION['error'] = "Password must be 15 characters or less.";
    } else {
        // Hash password securely
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // Database connection
        $conn = new mysqli("localhost", "root", "", "quickbite");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if username already exists (case-insensitive)
        $checkStmt = $conn->prepare("SELECT username FROM User WHERE LOWER(username) = LOWER(?)");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $_SESSION['error'] = "Username already exists. Please choose a different one.";
        } else {
            // Insert new user with role = 'customer'
            $stmt = $conn->prepare("INSERT INTO User (username, password, role) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $username, $password, $role);

                if ($stmt->execute()) {
                    // Registration success, redirect to dashboard
                    header("Location: login.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Registration failed. Please try again.";
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Database error. Please contact admin.";
            }
        }

        $checkStmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - QuickBite</title>
    <link rel="stylesheet" href="assets/css/styles.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #6db3b3;
        }
        .register-container {
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
        input[type="password"] {
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
    <div class="register-container">
        <h2>Register for QuickBite</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="register.php" method="POST" autocomplete="off">
            <label for="username">Username (max 15 chars)</label>
            <input type="text" name="username" id="username" maxlength="15" required />

            <label for="password">Password (max 15 chars)</label>
            <input type="password" name="password" id="password" maxlength="15" required />

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>