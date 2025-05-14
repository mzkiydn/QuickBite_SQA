<?php
    // Include all PHP files from the includes folder
    foreach (glob("../../includes/*.php") as $file) {
        include $file;
    }

// Dummy data for testing purposes
$orderId = 1001;

// Dummy menu items (this would normally come from the database)
$menuItems = [
    ['id' => 1, 'name' => 'Burger', 'price' => 5.99],
    ['id' => 2, 'name' => 'Pizza', 'price' => 7.49],
    ['id' => 3, 'name' => 'Salad', 'price' => 4.99],
    ['id' => 4, 'name' => 'Pasta', 'price' => 6.79]
];

// Initial order items (hardcoded for the test)
$orderItems = [
    ['menu_id' => 1, 'quantity' => 2],  // 2 Burgers
    ['menu_id' => 2, 'quantity' => 1],  // 1 Pizza
];

// Calculate the total price based on the dummy data
$totalPrice = 0;
$orderedItems = [];

foreach ($orderItems as $orderItem) {
    $menuItem = $menuItems[$orderItem['menu_id'] - 1]; // Menu IDs are 1-based
    $orderedItems[] = [
        'name' => $menuItem['name'],
        'price' => $menuItem['price'],
        'quantity' => $orderItem['quantity']
    ];
    $totalPrice += $menuItem['price'] * $orderItem['quantity'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Order</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        
        .payment-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            font-family: Arial, sans-serif;
        }

        h1, h2 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .total {
            font-weight: bold;
            margin-top: 20px;
        }

        .btn {
            background-color: #ff5722;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #e64a19;
        }

        .empty {
            color: #888;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-item button {
            background-color: #ff5722;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .order-item button:hover {
            background-color: #e64a19;
        }

        .quantity-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .quantity-controls button {
            padding: 5px 10px;
        }

        /* Styling for the menu section */
        .menu-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Your Order</h1>
        <p><strong>Order ID:</strong> <?php echo $orderId; ?></p>

        <!-- Display the Menu Items -->
        <h2>Menu Items</h2>
        <ul id="menu-items-list">
            <?php foreach ($menuItems as $menuItem): ?>
                <li class="menu-item">
                    <span><?php echo htmlspecialchars($menuItem['name']); ?> - RM<?php echo number_format($menuItem['price'], 2); ?></span>
                    <button onclick="addItem(<?php echo $menuItem['id']; ?>)">Add to Order</button>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Display the Order Items -->
        <h2>Your Order</h2>
        <ul id="order-items-list">
            <?php foreach ($orderedItems as $index => $item): ?>
                <li class="order-item" id="item-<?php echo $index; ?>">
                    <span><?php echo htmlspecialchars($item['name']); ?> - 
                        <?php echo $item['quantity']; ?> x RM<?php echo number_format($item['price'], 2); ?> = 
                        RM<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </span>
                    <div class="quantity-controls">
                        <button onclick="updateQuantity(<?php echo $index; ?>, 'decrease')">-</button>
                        <span id="quantity-<?php echo $index; ?>"><?php echo $item['quantity']; ?></span>
                        <button onclick="updateQuantity(<?php echo $index; ?>, 'increase')">+</button>
                    </div>
                    <button onclick="removeItem(<?php echo $index; ?>)">Remove</button>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="total">Total: RM<span id="total-price"><?php echo number_format($totalPrice, 2); ?></span></p>
        <button class="btn" onclick="addItem()">Add Item</button>
        <a href="Cust/Payment/payment.php?orderId=<?php echo $orderId; ?>">
            <button class="btn">Proceed to Payment</button>
        </a>
    </div>

    <script>
        // Initial dummy data to simulate the order items
        let orderItems = <?php echo json_encode($orderedItems); ?>;
        let menuItems = <?php echo json_encode($menuItems); ?>;

        // Function to update the quantity of an item in the order
        function updateQuantity(index, action) {
            if (action === 'increase') {
                orderItems[index].quantity++;
            } else if (action === 'decrease' && orderItems[index].quantity > 1) {
                orderItems[index].quantity--;
            }
            renderOrder();
        }

        // Function to remove an item from the order
        function removeItem(index) {
            orderItems.splice(index, 1);
            renderOrder();
        }

        // Function to add an item to the order (now accepts the menu item id)
        function addItem(menuId) {
            // Find the selected menu item
            const menuItem = menuItems.find(item => item.id === menuId);
            
            // Check if the item is already in the order
            const existingItemIndex = orderItems.findIndex(item => item.menu_id === menuItem.id);
            
            if (existingItemIndex !== -1) {
                // If the item is already in the order, increase its quantity
                orderItems[existingItemIndex].quantity++;
            } else {
                // If the item is not in the order, add it with a quantity of 1
                orderItems.push({ menu_id: menuItem.id, quantity: 1 });
            }
            
            renderOrder();
        }

        // Function to render the updated order list and total price
        function renderOrder() {
            let totalPrice = 0;
            const orderItemsList = document.getElementById('order-items-list');
            orderItemsList.innerHTML = ''; // Clear the current order items list

            orderItems.forEach((item, index) => {
                const menuItem = menuItems[item.menu_id - 1];
                const itemTotalPrice = menuItem.price * item.quantity;
                totalPrice += itemTotalPrice;

                // Create new list item
                const li = document.createElement('li');
                li.classList.add('order-item');
                li.id = 'item-' + index;

                li.innerHTML = `
                    <span>${menuItem.name} - ${item.quantity} x $${menuItem.price.toFixed(2)} = $${itemTotalPrice.toFixed(2)}</span>
                    <div class="quantity-controls">
                        <button onclick="updateQuantity(${index}, 'decrease')">-</button>
                        <span id="quantity-${index}">${item.quantity}</span>
                        <button onclick="updateQuantity(${index}, 'increase')">+</button>
                    </div>
                    <button onclick="removeItem(${index})">Remove</button>
                `;

                orderItemsList.appendChild(li);
            });

            // Update total price
            document.getElementById('total-price').textContent = totalPrice.toFixed(2);
        }

        
