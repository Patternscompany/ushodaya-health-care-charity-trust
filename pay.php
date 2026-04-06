<?php
require_once('config.php');

use Razorpay\Api\Api;

// If the Razorpay PHP library exists, use it. Otherwise, use a simple fallback.
if (file_exists('razorpay-php/Razorpay.php')) {
    require_once('razorpay-php/Razorpay.php');
    $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
    
    // Create Order via Library
    $amount = $_POST['amount'] * 100; // convert to paise
    $orderData = [
        'receipt'         => 'order_rcptid_' . rand(),
        'amount'          => $amount,
        'currency'        => CURRENCY
    ];
    $order = $api->order->create($orderData);
    $orderId = $order['id'];
} else {
    // Fallback: Create Order using native PHP cURL
    $amount = $_POST['amount'] * 100;
    $orderData = json_encode([
        'amount'   => $amount,
        'currency' => CURRENCY,
        'receipt'  => 'order_rcptid_' . rand()
    ]);

    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ":" . RAZORPAY_KEY_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $orderData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    if (isset($data['id'])) {
        $orderId = $data['id'];
    } else {
        die("Error creating Razorpay Order: " . ($data['error']['description'] ?? 'Unknown error'));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment...</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>
    <form name="razorpayform" action="verify.php" method="POST">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
        <input type="hidden" name="razorpay_order_id"    id="razorpay_order_id" >
    </form>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
    var options = {
        "key": "<?php echo RAZORPAY_KEY_ID; ?>",
        "amount": "<?php echo $amount; ?>",
        "currency": "<?php echo CURRENCY; ?>",
        "name": "<?php echo SITE_NAME; ?>",
        "description": "Support our cause",
        "image": "assets/img/logo/ushodaya.png",
        "order_id": "<?php echo $orderId; ?>",
        "handler": function (response){
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.razorpayform.submit();
        },
        "prefill": {
            "name": "<?php echo $_POST['name']; ?>",
            "email": "<?php echo $_POST['email']; ?>",
            "contact": "<?php echo $_POST['phone']; ?>"
        },
        "theme": {
            "color": "#00add9"
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.on('payment.failed', function (response){
        alert("Payment Failed! " + response.error.description);
        window.location.href = "donation.html";
    });
    rzp1.open();
    </script>
</body>
</html>
