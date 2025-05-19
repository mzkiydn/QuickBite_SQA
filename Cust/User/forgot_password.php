<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $newPassword = $_POST['new_password'];

    $conn = new mysqli("localhost", "root", "", "quickbite");

    if ($conn->connect_error) {
        die("Connection failed");
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM User WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Update password
        $update = $conn->prepare("UPDATE User SET password=? WHERE username=?");
        $update->bind_param("ss", $newPassword, $username);
        $update->execute();

        $_SESSION['success'] = "Password reset successfully. Please login.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body { background: #6db3b3; font-family: Arial, sans-serif; }
        .reset-container {
            width: 500px;
            margin: 200px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; }
        input {
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .error, .success {
            text-align: center;
            margin-bottom: 15px;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Reset Password</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error">'.$_SESSION['error'].'</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="forgot_password.php" method="POST">
            <input type="text" name="username" placeholder="Enter your username" required>
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>