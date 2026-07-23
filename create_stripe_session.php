<?php
session_start();
include 'db.php';

$order = $_SESSION['pending_order'] ?? null;
if (!$order) {
    header("Location: menu.php");
    exit;
}

$total = $order['price'] * $order['quantity'];
$unit_amount = (int) round($total * 100); // smallest currency unit (e.g. cents)

function stripe_is_configured() {
    return STRIPE_SECRET_KEY !== 'sk_test_XXXXXXXXXXXXXXXXXXXXXXXX' && strpos(STRIPE_SECRET_KEY, 'sk_') === 0;
}

function show_stripe_not_configured_page($order, $total) {
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="UTF-8"><title>Card Payment Unavailable</title>
    <style>
      body{font-family:Arial,sans-serif;background:#f8f0e3;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
      .box{background:#fff;padding:40px;border-radius:12px;max-width:520px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.15);}
      .box h2{color:#c0392b;}
      code{background:#eee;padding:2px 6px;border-radius:4px;}
      a.btn{display:inline-block;margin-top:20px;background:#c0392b;color:#fff;padding:10px 22px;border-radius:6px;text-decoration:none;}
    </style></head>
    <body>
      <div class="box">
        <h2>💳 Card Payments Not Set Up Yet</h2>
        <p>The site owner hasn't added a real Stripe secret key yet. Add your key to
        <code>STRIPE_SECRET_KEY</code> in <code>config.php</code> to enable real card payments
        (free test keys at <a href="https://dashboard.stripe.com/test/apikeys" target="_blank">dashboard.stripe.com</a>).</p>
        <p>In the meantime you can complete this order using Cash on Delivery or UPI.</p>
        <a class="btn" href="payment.php">&larr; Back to Payment Options</a>
      </div>
    </body></html>
    <?php
}

if (!stripe_is_configured()) {
    show_stripe_not_configured_page($order, $total);
    exit;
}

$success_url = rtrim(SITE_URL, '/') . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}';
$cancel_url  = rtrim(SITE_URL, '/') . '/payment_cancel.php';

$params = [
    'mode' => 'payment',
    'success_url' => $success_url,
    'cancel_url' => $cancel_url,
    'line_items' => [
        [
            'quantity' => $order['quantity'],
            'price_data' => [
                'currency' => CURRENCY_CODE,
                'unit_amount' => (int) round($order['price'] * 100),
                'product_data' => [
                    'name' => $order['dish_name'],
                ],
            ],
        ],
    ],
];

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($params),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . STRIPE_SECRET_KEY,
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
]);
$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

$data = $response ? json_decode($response, true) : null;

if ($curl_error || !$data || isset($data['error']) || empty($data['url'])) {
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="UTF-8"><title>Payment Error</title>
    <style>body{font-family:Arial,sans-serif;background:#f8f0e3;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
    .box{background:#fff;padding:40px;border-radius:12px;max-width:520px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.15);}
    .box h2{color:#c0392b;} a.btn{display:inline-block;margin-top:20px;background:#c0392b;color:#fff;padding:10px 22px;border-radius:6px;text-decoration:none;}</style>
    </head><body><div class="box">
        <h2>Couldn't start card payment</h2>
        <p><?= htmlspecialchars($data['error']['message'] ?? $curl_error ?? 'Unknown error. Please check your Stripe keys / network access.') ?></p>
        <a class="btn" href="payment.php">&larr; Back to Payment Options</a>
    </div></body></html>
    <?php
    exit;
}

// Remember the pending order under the Stripe session id so payment_success.php
// can confirm payment and finalize it even if the session cookie changed browser tab.
$_SESSION['stripe_pending_' . $data['id']] = $order;

header("Location: " . $data['url']);
exit;
