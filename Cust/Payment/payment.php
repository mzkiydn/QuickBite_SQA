<?php
require '../../vendor/autoload.php'; // Include Stripe PHP library

// Stripe API keys (replace with your actual keys)
\Stripe\Stripe::setApiKey('secret key'); // Secret Key

// Include all PHP files from the includes folder
foreach (glob("../../includes/*.php") as $file) {
    include $file;
}

// Database connection (update with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quickbite";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get orderId from query string
if (!isset($_GET['orderId']) || empty($_GET['orderId'])) {
    die("Order ID is required.");
}

$orderId = intval($_GET['orderId']); // Sanitize input

// Fetch order details
$orderQuery = "SELECT * FROM `order` WHERE orderID = $orderId";
$orderResult = $conn->query($orderQuery);

if ($orderResult->num_rows > 0) {
    $order = $orderResult->fetch_assoc();
} else {
    die("Order not found.");
}

// Fetch menu items for the order
$menuItemsQuery = "
    SELECT menu.name, menu.price, menulist.quantity 
    FROM menulist 
    JOIN menu ON menulist.menuID = menu.menuID 
    WHERE menulist.orderID = $orderId";
$menuItemsResult = $conn->query($menuItemsQuery);

$menuItems = [];
$totalPrice = 0;

if ($menuItemsResult->num_rows > 0) {
    while ($row = $menuItemsResult->fetch_assoc()) {
        $menuItems[] = $row;
        $totalPrice += $row['price'] * $row['quantity'];
    }
} else {
    die("No menu items found for this order.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script src="https://js.stripe.com/v3/"></script> <!-- Stripe.js -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stripe = Stripe('pk_test_51RIw55R0B0Zzi8hZV4ag1Iwk1wKcnVpD4acBDsITfyGgyznwLoeqEvBedMVqWM0sEbGDchiPx1xLyfzLICYxQrfJ00vmhMzCOy'); // Replace with your Publishable Key
            const payBtn = document.getElementById('payBtn');
            const paymentModal = document.getElementById('paymentModal');
            const closeModal = document.getElementById('closeModal');
            const confirmPayment = document.getElementById('confirmPayment');
            const paymentMethodSelect = document.getElementById('paymentMethod');
            const cardElementContainer = document.getElementById('card-element-container');
            const cardElement = stripe.elements().create('card');
            cardElement.mount('#card-element');

            // Open modal when "Pay" button is clicked
            payBtn.addEventListener('click', () => {
                paymentModal.style.display = 'block';
            });

            // Close modal when "X" button is clicked
            closeModal.addEventListener('click', () => {
                paymentModal.style.display = 'none';
            });

            // Close modal when clicking outside the modal
            window.onclick = (event) => {
                if (event.target === paymentModal) {
                    paymentModal.style.display = 'none';
                }
            };

            // Show or hide card input field based on payment method
            paymentMethodSelect.addEventListener('change', () => {
                if (paymentMethodSelect.value === 'Card') {
                    cardElementContainer.style.display = 'block';
                } else {
                    cardElementContainer.style.display = 'none';
                }
            });

            // Process payment using Stripe
            confirmPayment.addEventListener('click', async () => {
                const paymentMethod = paymentMethodSelect.value;
                if (!paymentMethod) {
                    alert('Please select a payment method.');
                    return;
                }

                // Call backend to create a Stripe Payment Intent
                const response = await fetch('create_payment_intent.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        orderId: <?php echo $orderId; ?>,
                        amount: <?php echo $totalPrice * 100; ?>, // Amount in cents
                        paymentMethod: paymentMethod
                    })
                });

                const data = await response.json();
                if (data.error) {
                    alert(data.error);
                    return;
                }

                const { clientSecret } = data;

                // Confirm the payment with the card details
                const result = await stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: 'Customer Name', // Replace with actual customer name
                        },
                    },
                });

                if (result.error) {
                    alert(result.error.message);
                } else if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
                    alert('Payment successful!');
                    paymentModal.style.display = 'none';

                    // Update order status and save payment details
                    await fetch('paymentResult.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            orderId: <?php echo $orderId; ?>,
                            paymentMethod: paymentMethod,
                            amount: <?php echo $totalPrice; ?>,
                            status: 'Paid'
                        })
                    });

                    // Redirect to order confirmation page or display success message
                    alert('Order status updated successfully!');
                }
            });
        });
    </script>
</head>
<body>
    <div class="payment-container">
        <h1>Payment</h1>
        <p><strong>Order ID:</strong> <?php echo $orderId; ?></p>
        <h2>Menu Items</h2>
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <li><?php echo htmlspecialchars($item['name']); ?> - RM <?php echo number_format($item['price'], 2); ?></li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total Price:</strong> RM <?php echo number_format($totalPrice, 2); ?></p>
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
                    <option value="Card">Card</option>
                </select>
                <div id="card-element-container" style="display: none; margin: 20px 0;">
                    <label for="card-element">Enter Card Details:</label>
                    <div id="card-element" style="border: 1px solid #ccc; padding: 10px; border-radius: 5px;"></div>
                </div>
                <button id="confirmPayment">Confirm Payment</button>
            </div>
        </div>
    </div>
</body>
</html>