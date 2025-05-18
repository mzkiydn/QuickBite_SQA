<?php
    // Include all PHP files from the includes folder
    foreach (glob("../../includes/*.php") as $file) {
        include $file;
    }

// Dummy data for testing purposes
$orderId = 1234;

// Dummy menu items
$menuItems = [
    ['id' => 1, 'name' => 'Burger', 'price' => 5.99],
    ['id' => 2, 'name' => 'Pizza', 'price' => 7.49],
    ['id' => 3, 'name' => 'Salad', 'price' => 4.99],
    ['id' => 4, 'name' => 'Pasta', 'price' => 6.79]
];

// Initial order items
$orderItems = [
    ['menu_id' => 1, 'quantity' => 2], // 2 Burgers
    ['menu_id' => 2, 'quantity' => 1], // 1 Pizza
];

// Prepare items for JavaScript
$orderedItems = [];
foreach ($orderItems as $item) {
    $menu = $menuItems[$item['menu_id'] - 1];
    $orderedItems[] = [
        'menu_id' => $item['menu_id'],
        'quantity' => $item['quantity']
    ];
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
                <button data-id="<?php echo $menuItem['id']; ?>" class="add-item-btn">Add to Order</button>
            </li>
        <?php endforeach; ?>
    </ul>
<!-- Display the Order Items -->
    <h2>Your Order</h2>
    <ul id="order-items-list"></ul>

    <p class="total">Total: RM<span id="total-price">0.00</span></p>
    <a href="../menu.php?orderId=<?php echo $orderId; ?>">
    <button class="btn">Add Item</button>
    </a>
    <a href="../Payment/payment.php?orderId=<?php echo $orderId; ?>">
        <button class="btn">Proceed to Payment</button>
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let orderItems = <?php echo json_encode($orderedItems); ?>;
    let menuItems = <?php echo json_encode($menuItems); ?>;

    function renderOrder() {
        const orderItemsList = document.getElementById('order-items-list');
        orderItemsList.innerHTML = '';
        let totalPrice = 0;

        orderItems.forEach((item, index) => {
            const menuItem = menuItems.find(m => m.id === item.menu_id);
            const itemTotal = menuItem.price * item.quantity;
            totalPrice += itemTotal;

            const li = document.createElement('li');
            li.className = 'order-item';
            li.innerHTML = `
                <span>${menuItem.name} - ${item.quantity} x RM${menuItem.price.toFixed(2)} = RM${itemTotal.toFixed(2)}</span>
                <div class="quantity-controls">
                    <button class="decrease">-</button>
                    <span>${item.quantity}</span>
                    <button class="increase">+</button>
                </div>
                <button class="remove">Remove</button>
            `;

            // Add functionality
            li.querySelector('.increase').addEventListener('click', () => {
                item.quantity++;
                renderOrder();
            });

            li.querySelector('.decrease').addEventListener('click', () => {
                if (item.quantity > 1) {
                    item.quantity--;
                    renderOrder();
                }
            });

            li.querySelector('.remove').addEventListener('click', () => {
                orderItems.splice(index, 1);
                renderOrder();
            });

            orderItemsList.appendChild(li);
        });

        document.getElementById('total-price').textContent = totalPrice.toFixed(2);
    }

    // Add event listeners to "Add to Order" buttons
    document.querySelectorAll('.add-item-btn').forEach(button => {
        button.addEventListener('click', () => {
            const menuId = parseInt(button.getAttribute('data-id'));
            const existing = orderItems.find(item => item.menu_id === menuId);

            if (existing) {
                existing.quantity++;
            } else {
                orderItems.push({ menu_id: menuId, quantity: 1 });
            }

            renderOrder();
        });
    });

    // Initial render
    renderOrder();
});
</script>

</body>
</html>
