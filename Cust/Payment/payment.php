<?php
    // Include all PHP files from the includes folder
    foreach (glob("../../includes/*.php") as $file) {
        include $file;
    }

    // // Database connection (update with your database credentials)
    // $servername = "localhost";
    // $username = "root";
    // $password = "";
    // $dbname = "quickbite";

    // $conn = new mysqli($servername, $username, $password, $dbname);

    // // Check connection
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    // // Fetch order details (replace with actual order ID from session or request)
    // $orderId = 1234; // Example order ID
    // $orderQuery = "SELECT * FROM `order` WHERE orderID = $orderId";
    // $orderResult = $conn->query($orderQuery);

    // if ($orderResult->num_rows > 0) {
    //     $order = $orderResult->fetch_assoc();
    // } else {
    //     die("Order not found.");
    // }

    // // Fetch menu items for the order
    // $menuItemsQuery = "
    //     SELECT menu.name, menu.price 
    //     FROM order_items 
    //     JOIN menu ON order_items.menu_id = menu.id 
    //     WHERE order_items.order_id = $orderId";
    // $menuItemsResult = $conn->query($menuItemsQuery);

    // $menuItems = [];
    // $totalPrice = 0;

    // if ($menuItemsResult->num_rows > 0) {
    //     while ($row = $menuItemsResult->fetch_assoc()) {
    //         $menuItems[] = $row;
    //         $totalPrice += $row['price'];
    //     }
    // } else {
    //     die("No menu items found for this order.");
    // }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
        }
        
        .payment-methods select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .payment-methods button {
            background-color: #ff5722;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .payment-methods button:hover {
            background-color: #e64a19;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Payment</h1>
        <p><strong>Order ID:</strong> <?php echo $orderId; ?></p>
        <h2>Menu Items</h2>
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <li><?php echo htmlspecialchars($item['name']); ?> - $<?php echo number_format($item['price'], 2); ?></li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total Price:</strong> $<?php echo number_format($totalPrice, 2); ?></p>
        <button id="payBtn">Pay</button>
    </div>

    <!-- Payment Method Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Select Payment Method</h2>
            <div class="payment-methods">
                <select id="paymentMethod">
                    <option value="" disabled selected>Select a payment method</option>
                    <option value="Debit">Debit</option>
                    <option value="QR">QR</option>
                    <option value="Credit">Credit</option>
                    <option value="E-Wallet">E-Wallet</option>
                    <option value="Cash">Cash</option>
                </select>
                <button onclick="processPayment()">Confirm Payment</button>
            </div>
        </div>
    </div>

    <script>
        // Open modal
        const payBtn = document.getElementById('payBtn');
        const paymentModal = document.getElementById('paymentModal');
        const closeModal = document.getElementById('closeModal');

        payBtn.addEventListener('click', () => {
            paymentModal.style.display = 'block';
        });

        closeModal.addEventListener('click', () => {
            paymentModal.style.display = 'none';
        });

        window.onclick = (event) => {
            if (event.target === paymentModal) {
                paymentModal.style.display = 'none';
            }
        };

        // Process payment
        function processPayment() {
            const paymentMethod = document.getElementById('paymentMethod').value;
            if (!paymentMethod) {
                alert('Please select a payment method.');
                return;
            }
            alert('You selected ' + paymentMethod + ' as your payment method.');
            paymentModal.style.display = 'none';
        }
    </script>
</body>
</html>