<?php
require_once('config.php');

$payment_id = $_POST['razorpay_payment_id'];
$order_id   = $_POST['razorpay_order_id'] ?? ''; // Some versions pass order_id back, some don't
$signature  = $_POST['razorpay_signature'];

// If order_id isn't in POST, you might need to check if it's stored in a session or similar.
// In this implementation, we expect either order_id or simple successful ID.

$success = false;
$error = "Payment Verification Failed";

// Verify Signature
$generated_signature = hash_hmac('sha256', $_POST['razorpay_order_id'] . "|" . $payment_id, RAZORPAY_KEY_SECRET);

if ($generated_signature == $signature) {
    $success = true;
} else {
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status - <?php echo SITE_NAME; ?></title>
    <!-- Use existing CSS if possible -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .status-container {
            padding: 100px 20px;
            text-align: center;
        }

        .status-icon {
            font-size: 40px;
            margin-bottom: 20px;
        }

        .success-icon {
            color: #28a745;
        }

        .failed-icon {
            color: #dc3545;
        }

        .status-card {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .status-card h2 {
            font-size: 40px;
        }
    </style>
</head>

<body>

    <div class="status-container">
        <div class="status-card">
            <?php if ($success): ?>
                <div class="status-icon success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Payment Successful!</h2>
                <p>Thank you for your generous donation. Your support means everything to us.</p>
                <div class="details mt-4 text-left">
                    <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($payment_id); ?></p>
                </div>
                <a href="index.html" class="theme-btn mt-4"><span>Back to Home</span></a>
            <?php else: ?>
                <div class="status-icon failed-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2>Payment Failed! ❌</h2>
                <p><?php echo $error; ?></p>
                <a href="donation.html" class="theme-btn mt-4"><span>Try Again</span></a>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>