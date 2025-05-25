<?php
// Include your DB connection
foreach (glob("../../includes/*.php") as $file) {
    include $file;
}

$orderID = $_POST['orderID'] ?? 0;
$menuID = $_POST['menuID'] ?? 0;
$action = $_POST['action'] ?? '';

if (!$orderID || !$menuID || !$action) {
    http_response_code(400);
    echo "Missing parameters.";
    exit;
}

switch ($action) {
    case 'increase':
        $sql = "UPDATE MenuList SET quantity = quantity + 1 WHERE orderID = ? AND menuID = ?";
        break;
    case 'decrease':
        $sql = "UPDATE MenuList SET quantity = quantity - 1 WHERE orderID = ? AND menuID = ? AND quantity > 1";
        break;
    case 'remove':
        $sql = "DELETE FROM MenuList WHERE orderID = ? AND menuID = ?";
        break;
    default:
        http_response_code(400);
        echo "Invalid action.";
        exit;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $orderID, $menuID);
if ($stmt->execute()) {
    echo "Success";
} else {
    http_response_code(500);
    echo "Database error";
}
?>
