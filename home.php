<?php
session_start();
include 'db.php';

$featured_result = mysqli_query($connect, "SELECT * FROM menu_items WHERE is_featured=1 ORDER BY id ASC LIMIT 3");
$featured_dishes = [];
if ($featured_result) {
    while ($row = mysqli_fetch_assoc($featured_result)) {
        $featured_dishes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f8f0e3;
}

.navbar {
    background-color: darkred;
    padding: 15px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
}
.navbar .logo {
    color: white;
    font-size: 24px;
    font-weight: bold;
    letter-spacing: 1px;
    cursor: pointer;
    transition: transform 0.3s ease;
}
.navbar .logo:hover {
    transform: scale(1.1);
    color: #ffcccb;
}
.navbar ul {
    list-style: none;
    display: flex;
}
.navbar ul li {
    margin-left: 30px;
}
.navbar ul li a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    position: relative;
}
.navbar ul li a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -5px;
    width: 0;
    height: 2px;
    background: blue;
    transition: width 0.3s ease;
}
.navbar ul li a:hover::after {
    width: 100%; 
}

.hero {
    background: url('IMAGES/Home Page.jpg') no-repeat center center;
    background-size: cover;
    height: 500px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
}
.hero h1 {
    font-size: 3rem;
    margin-bottom: 20px;
}
.hero p {
    font-size: 1.5rem;
    margin-bottom: 30px;
}
.hero .btn {
    background-color: crimson;
    padding: 15px 30px;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 8px;
}
.hero .btn:hover {
    background-color: darkred;
}

.featured {
    padding: 60px 50px;
    text-align: center;
}
.featured h2 {
    font-size: 2.5rem;
    margin-bottom: 50px;
    color: darkred;
}
.dishes {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.dish-card {
    background-color: white;
    width: 30%;
    margin-bottom: 30px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    animation: cardFadeUp 1.2s ease;
}

@keyframes cardFadeUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dish-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.25);
}

.dish-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.dish-card h3 {
    margin: 15px 0 10px 0;
    color: darkred;
}
.dish-card p {
    padding: 0 15px 15px 15px;
}
.dish-card a {
    display: inline-block;
    margin-bottom: 15px;
    background-color: crimson;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
}
.dish-card a:hover {
    background-color: darkred;
}

.footer {
    background-color: darkred;
    color: white;
    text-align: center;
    padding: 20px;
}

@media (max-width: 992px) {
    .dishes {
        flex-direction: column;
        align-items: center;
    }
    .dish-card {
        width: 80%;
    }
}
</style>
</head>

<body>
<?php include 'includes/order_toast.php'; ?>

<div class="navbar">
    <div class="logo">🍴 Online Food Ordering</div>
    <ul>
        <li><a href="home.php">HOME</a></li>
        <li><a href="menu.php">MENU</a></li>
        <li><a href="about.php">ABOUT</a></li>
        <li><a href="review.php">REVIEW</a></li>
        <li><a href="contact.php">CONTACT</a></li>

        <?php if(isset($_SESSION['user_id'])) { ?>
            <li><a href="my_orders.php">MY ORDERS</a></li>
            <li><a href="logout.php">LOGOUT</a></li>
        <?php } else { ?>
            <li><a href="login.php">LOGIN</a></li>
        <?php } ?>
    </ul>
</div>


<div class="hero">
    <div>
        <h1>Delicious Food Delivered To Your Doorstep</h1>
        <p>Fresh Meals, Fast Delivery, Your Favorite Dishes at Home!</p>
        <a href="orders.php" class="btn">Order Now</a>
    </div>
</div>

<div class="featured">
    <h2>Our Popular Dishes</h2>
    <div class="dishes">

        <?php foreach($featured_dishes as $dish): ?>
        <div class="dish-card">
            <img src="<?= htmlspecialchars($dish['image']) ?>">
            <h3><?= htmlspecialchars($dish['name']) ?></h3>
            <p><?= htmlspecialchars($dish['description']) ?></p>
            <a href="orders.php">Order Now</a>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<div class="footer">
    <p>&copy; 2025 Food Delight. All Rights Reserved. | Designed By <b>Mohamed Farhan</b></p>
    <p style="margin-top:8px; font-size:0.8rem; opacity:0.7;"><a href="login.php?type=admin" style="color:#ffcccb;">Admin Login</a></p>
</div>

</body>
</html>
