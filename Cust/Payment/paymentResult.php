<?php
require '../../vendor/autoload.php'; // Include Stripe PHP library

header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quickbite";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed.']));
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['orderId'];
$paymentMethod = $input['paymentMethod'];
$amount = $input['amount'];
$status = $input['status'];

try {
    // Update order status
    $updateOrderQuery = "UPDATE `order` SET status = '$status' WHERE orderID = $orderId";
    $conn->query($updateOrderQuery);

    // Insert payment details
    $insertPaymentQuery = "INSERT INTO payment (orderID, method, amount, date) VALUES ($orderId, '$paymentMethod', $amount, NOW())";
    $conn->query($insertPaymentQuery);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();