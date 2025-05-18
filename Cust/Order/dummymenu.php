<?php
// Dummy menu data (could come from DB)
$menuItems = [
    ['id' => 1, 'name' => 'Burger', 'price' => 5.99],
    ['id' => 2, 'name' => 'Pizza', 'price' => 7.49],
    ['id' => 3, 'name' => 'Salad', 'price' => 4.99],
    ['id' => 4, 'name' => 'Pasta', 'price' => 6.79]
];

// Get order ID from URL
$orderId = $_GET['orderId'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 40px;
        }

        .menu-container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .menu-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .add-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-btn:hover {
            background-color: #388e3c;
        }

        .manage-order-link {
            margin-top: 30px;
            display: block;
            text-align: center;
            background-color: #2196F3;
            color: white;
            padding: 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .manage-order-link:hover {
            background-color: #1976d2;
        }
    </style>
</head>
<body>

<div class="menu-container">
    <h1>Our Menu</h1>

    <?php foreach ($menuItems as $item): ?>
        <div class="menu-item">
            <span><?= htmlspecialchars($item['name']) ?> - RM<?= number_format($item['price'], 2) ?></span>
            <button class="add-btn" onclick="addToOrder(<?= $item['id'] ?>)">Add to Order</button>
        </div>
    <?php endforeach; ?>

    <?php if ($orderId): ?>
        <a class="manage-order-link" href="manage-order.php?orderId=<?= $orderId ?>">View My Order</a>
    <?php endif; ?>
</div>

<script>
    const orderId = "<?= $orderId ?>";
    const menuItems = <?= json_encode($menuItems) ?>;

    function addToOrder(menuId) {
        // Get current order from localStorage
        let orderKey = `order_${orderId}`;
        let currentOrder = JSON.parse(localStorage.getItem(orderKey)) || [];

        // Check if item is already in order
        const existingItem = currentOrder.find(item => item.menu_id === menuId);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            currentOrder.push({ menu_id: menuId, quantity: 1 });
        }

        // Save updated order
        localStorage.setItem(orderKey, JSON.stringify(currentOrder));
        alert("Item added to order!");
    }
</script>

</body>
</html>
