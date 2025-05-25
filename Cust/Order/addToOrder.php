<?php
    // Include all PHP files from the includes folder
    foreach (glob("../../includes/*.php") as $file) {
        include $file;
    }

// Get menuID and orderID from URL
$menuID = $_GET['menuID'] ?? null;
$orderID = $_GET['orderID'] ?? null;

if (!$menuID || !$orderID) {
    die("Missing menuID or orderID.");
}

// Check if item already exists
$checkSql = "SELECT * FROM MenuList WHERE menuID = ? AND orderID = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("ii", $menuID, $orderID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $updateSql = "UPDATE MenuList SET quantity = quantity + 1 WHERE menuID = ? AND orderID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ii", $menuID, $orderID);
    $updateStmt->execute();
} else {
    $insertSql = "INSERT INTO MenuList (menuID, orderID, quantity, remarks) VALUES (?, ?, 1, '')";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ii", $menuID, $orderID);
    $insertStmt->execute();
}
     if (!$insertStmt->execute()) {
         die("Insert failed: " . $insertStmt->error);
     }
     

header("Location: ../Order/order1.php?orderID=" . $orderID);
exit();
?>
