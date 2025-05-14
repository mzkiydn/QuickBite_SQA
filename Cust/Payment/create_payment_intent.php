<?php
require '../../vendor/autoload.php'; // Include Stripe PHP library

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']); // Use the secret key from the .env file

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $orderId = $input['orderId'];
    $amount = $input['amount']; // Amount in cents
    $paymentMethod = $input['paymentMethod'];

    if (!$orderId || !$amount || !$paymentMethod) {
        throw new Exception('Invalid input.');
    }

    // Create a PaymentIntent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'myr', // Replace with your currency
        'payment_method_types' => ['card'],
    ]);

    echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}