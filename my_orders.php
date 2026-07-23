<?php
session_start();
include 'db.php';
include 'includes/order_tracker.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$msg = $_SESSION['order_msg'] ?? '';
unset($_SESSION['order_msg']);

$result = mysqli_query($connect, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id DESC");
$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders</title>
<link rel="stylesheet" href="forgot.css">
<style>
.orders-wrap { max-width: 820px; margin: 30px auto 60px; padding: 0 20px; }
.page-title { color:#fff; text-align:center; margin: 20px 0 30px; font-size:1.8rem; text-shadow: 0 2px 6px rgba(0,0,0,0.4); }
.msg-banner { max-width:820px; margin: 0 auto 15px; padding:12px 20px; border-radius:8px; text-align:center; font-weight:600; }
.msg-banner.success { background:#e8f5e9; color:#256029; }
.msg-banner.error { background:#fdecea; color:#a1260c; }

.order-card {
  background:#fff; border-radius:14px; padding:24px 28px; margin-bottom:22px;
  box-shadow:0 10px 30px rgba(0,0,0,0.15);
  animation: slideIn 0.5s ease forwards; opacity:0; transform: translateY(15px);
}
@keyframes slideIn { to { opacity:1; transform:translateY(0); } }

.order-card-head { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px; margin-bottom:16px; }
.order-card-head h3 { color:#c0392b; margin:0; }
.order-card-head .order-date { color:#888; font-size:0.85rem; }
.order-details p { margin: 4px 0; color:#444; font-size:0.92rem; }
.order-badges { display:flex; gap:8px; flex-wrap:wrap; margin: 10px 0; }
.pill { padding:4px 12px; border-radius:20px; font-size:0.78rem; font-weight:bold; color:#fff; }
.pill.green { background:#2e7d32; }
.pill.orange { background:#e65100; }
.pill.blue { background:#1565c0; }
.pill.gray { background:#616161; }
.pill.red { background:#c62828; }

/* --- Animated tracker --- */
.order-tracker { margin: 22px 0 8px; position:relative; }
.tracker-line-bg { position:absolute; top:19px; left:10%; right:10%; height:4px; background:#eee; border-radius:2px; z-index:0; }
.tracker-line-fill {
  position:absolute; top:0; left:0; height:100%; background: linear-gradient(90deg,#ff7043,#c0392b);
  border-radius:2px; width:0; animation: fillLine 1.2s ease forwards 0.2s;
}
@keyframes fillLine { to { width: var(--fill-to); } }
.tracker-steps { display:flex; justify-content:space-between; position:relative; z-index:1; }
.tracker-step { display:flex; flex-direction:column; align-items:center; width:25%; }
.tracker-dot {
  width:40px; height:40px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center;
  font-size:1.1rem; border:3px solid #eee; transition: all 0.4s ease;
}
.tracker-step.done .tracker-dot { background:#ffe0d6; border-color:#ff7043; }
.tracker-step.active .tracker-dot { background:#fff; border-color:#c0392b; animation: pulse 1.4s infinite; }
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(192,57,43,0.4); }
  70% { box-shadow: 0 0 0 10px rgba(192,57,43,0); }
  100% { box-shadow: 0 0 0 0 rgba(192,57,43,0); }
}
.tracker-label { font-size:0.75rem; margin-top:6px; color:#666; text-align:center; }
.tracker-step.done .tracker-label, .tracker-step.active .tracker-label { color:#c0392b; font-weight:600; }
.tracker-cancelled { background:#fdecea; color:#a1260c; padding:14px; border-radius:8px; text-align:center; font-weight:600; margin: 16px 0 8px; }

.order-actions { margin-top:18px; text-align:right; }
.btn-cancel {
  background:#fff; color:#c62828; border:2px solid #c62828; padding:8px 18px; border-radius:6px;
  cursor:pointer; font-weight:600; transition: all 0.25s ease;
}
.btn-cancel:hover { background:#c62828; color:#fff; }

.empty-state { text-align:center; color:#fff; background:rgba(0,0,0,0.25); padding:40px; border-radius:14px; }
.empty-state a { color:#ffd7cc; font-weight:bold; }
</style>
</head>
<body>

<?php include 'includes/site_nav.php'; ?>

<h1 class="page-title">📦 My Orders</h1>

<?php if ($msg): ?>
  <div class="msg-banner success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="orders-wrap">

<?php if (empty($orders)): ?>
  <div class="empty-state">
    <p>You haven't placed any orders yet.</p>
    <a href="menu.php">Browse the Menu &rarr;</a>
  </div>
<?php endif; ?>

<?php foreach ($orders as $i => $o):
    $can_cancel = !in_array($o['order_status'], ['Delivered', 'Cancelled'], true);

    $pay_badge = 'gray';
    if ($o['payment_status'] === 'Paid') $pay_badge = 'green';
    elseif ($o['payment_status'] === 'Pending Verification') $pay_badge = 'orange';
    elseif (strpos($o['payment_status'], 'Pending') === 0) $pay_badge = 'blue';

    $status_badge = 'blue';
    if ($o['order_status'] === 'Delivered') $status_badge = 'green';
    elseif ($o['order_status'] === 'Cancelled') $status_badge = 'red';
    elseif ($o['order_status'] === 'Out for Delivery') $status_badge = 'orange';
?>
  <div class="order-card" style="animation-delay: <?= $i * 0.1 ?>s;">
    <div class="order-card-head">
      <div>
        <h3><?= htmlspecialchars($o['dish_name']) ?> &times; <?= (int)$o['quantity'] ?></h3>
      </div>
      <div class="order-date">Order #<?= $o['id'] ?> &middot; <?= htmlspecialchars(date('d M Y, h:i A', strtotime($o['created_at']))) ?></div>
    </div>

    <div class="order-badges">
      <span class="pill <?= $status_badge ?>"><?= htmlspecialchars($o['order_status']) ?></span>
      <span class="pill <?= $pay_badge ?>"><?= htmlspecialchars($o['payment_method']) ?> &middot; <?= htmlspecialchars($o['payment_status']) ?></span>
    </div>

    <div class="order-details">
      <p><b>Total:</b> <?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($o['total'], 2) ?></p>
      <p><b>Deliver to:</b> <?= htmlspecialchars($o['customer_name']) ?>, <?= htmlspecialchars($o['phone']) ?></p>
      <p><b>Address:</b> <?= htmlspecialchars($o['address']) ?></p>
    </div>

    <?php render_order_tracker($o['order_status']); ?>

    <?php if ($can_cancel): ?>
      <div class="order-actions">
        <form method="POST" action="cancel_order.php" onsubmit="return confirm('Cancel this order?');">
          <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
          <button type="submit" class="btn-cancel">Cancel Order</button>
        </form>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

</div>

</body>
</html>
