<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us | Food Ordering System</title>
  <link rel="Stylesheet" href="about.css">
</head>
<body>
  <!-- Dynamic Header Start -->
  <nav>
    <div class="logo">🍴 Online Food Ordering</div>
    <ul>
      <li><a href="home.php">HOME</a></li>
      <li><a href="menu.php">MENU</a></li>
      <li><a href="about.php">ABOUT</a></li>
      <li><a href="review.php">REVIEW</a></li>
      <li><a href="contact.php">CONTACT</a></li>

      <?php if(isset($_SESSION['user_id'])): ?>
        <li><a href="logout.php">LOGOUT</a></li>
      <?php else: ?>
        <li><a href="login.php">LOGIN</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <!-- Dynamic Header End -->

  <section class="about">
    <div class="about-text">
      <h2>Online Food Ordering</h2>
      <p>
        Welcome to Food Order! We are passionate about delivering delicious meals made with love and the finest ingredients. Whether you’re celebrating a special moment or simply craving good food, we’ve got something for everyone.
      </p>
      <p>
        Our journey started with a mission — to bring the taste of happiness to every customer’s table. With our talented chefs and commitment to quality, we make every bite memorable.
      </p>
    </div>
    <img src="IMAGES/AboutPage.jpg" alt="Delicious Food">
  </section>

  <section class="team">
    <h2>Meet Our Team</h2>
    <p>Our Skilled Chefs and Food Enthusiasts Work Hard to Serve You the Best Meals Every Day!</p>
    <div class="team-members">
      <div class="member">
        <img src="IMAGES/Chef1.jpg" alt="Chef 1" />
        <h4>Arun Kumar</h4>
        <p>Head Chef</p>
      </div>
      
      <div class="member">
        <img src="IMAGES/Chef2.jpg" alt="Chef 2" />
        <h4>Priya Sharma</h4>
        <p>Pastry Expert</p>
      </div>
      <div class="member">
        <img src="IMAGES/Chef 3.jpg" alt="Chef 3" />
        <h4>Nisar Kareem</h4>
        <p>Food Designer</p>
      </div>
    </div>
  </section>

  <footer>
    <p>© 2025 Food Delight. All Rights Reserved. | Designed By <b>Mohamed Farhan</b></p>
  </footer>
</body>
</html>
