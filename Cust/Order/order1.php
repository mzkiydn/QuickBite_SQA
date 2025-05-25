<?php

    // Include all PHP files from the includes folder
    foreach (glob("../../includes/*.php") as $file) {
        include $file;
    }


//fetch data from db
$orderID = $_GET['orderID'] ?? 0;


// Get order items by joining MenuList + Menu
$sql = "SELECT ml.menuID, m.name, m.price, ml.quantity 
        FROM MenuList ml 
        JOIN Menu m ON ml.menuID = m.menuID 
        WHERE ml.orderID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();

$orderItems = [];
while ($row = $result->fetch_assoc()) {
    $orderItems[] = $row;
}

// Fetch all menu items
$menuItems = [];
$menuQuery = $conn->query("SELECT menuID, name, price FROM Menu");
while ($row = $menuQuery->fetch_assoc()) {
    $menuItems[] = [
        'id' => $row['menuID'],  // Use 'id' to match JS code
        'name' => $row['name'],
        'price' => (float)$row['price']
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
    <p><strong>Order ID:</strong> <?php echo $orderID; ?></p>

<!-- Display the Order Items -->
    <h2>Your Order</h2>
    <ul id="order-items-list"></ul>

    <p class="total">Total: RM<span id="total-price">0.00</span></p>
    <a href="../menu/menu.php?orderID=<?php echo $orderID; ?>">
    <button class="btn">Add Item</button>
    </a>
    <a href="../Payment/payment.php?orderID=<?php echo $orderID; ?>">
        <button class="btn">Proceed to Payment</button>
    </a>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    let orderItems = <?php echo json_encode($orderItems); ?>;
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
