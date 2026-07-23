<?php
session_start();
include 'db.php';

$result = mysqli_query($connect, "SELECT * FROM menu_items ORDER BY id ASC");
$dishes = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $dishes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Food Ordering Menu</title>
<link rel="Stylesheet" href="menu.css">
</head>
<body>
<?php include 'includes/order_toast.php'; ?>

<nav>
  <div class="logo">🍴 Online Food Ordering</div>
  <div class="links">
    <a href="home.php">HOME</a>
    <a href="menu.php">MENU</a>
    <a href="about.php">ABOUT</a>
    <a href="review.php">REVIEW</a>
    <a href="contact.php">CONTACT</a>

    <?php if(isset($_SESSION['user_id'])){ ?>
      <a href="my_orders.php">MY ORDERS</a>
      <a href="logout.php">LOGOUT</a>
    <?php }else{ ?>
      <a href="login.php">LOGIN</a>
    <?php } ?>
  </div>
</nav>

<h2>Our Popular Dishes</h2>

<section class="menu">

<?php foreach($dishes as $dish): ?>
<div class="card">
  <img src="<?= htmlspecialchars($dish['image']) ?>">
  <div class="card-body">
    <h5 class="card-title"><?= htmlspecialchars($dish['name']) ?></h5>
    <p class="price"><?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?><?= number_format($dish['price'], 0) ?></p>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="orders.php"><button class="btn">Order Now</button></a>
    <?php else: ?>
      <a href="login.php?redirect=orders.php"><button class="btn">Login to Order</button></a>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>

</section>

<footer>
© 2025 Food Delight. All Rights Reserved. | Designed By <b>Mohamed Farhan</b>
</footer>

</body>
</html>
