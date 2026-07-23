<?php
session_start();
include 'db.php';

$success = false;
$error = "";
$name = "";
$email = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name == "" || $email == "" || $message == "") {
        $error = "Please fill in all fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } elseif (strlen($message) > 1000) {
        $error = "Message too long! Maximum 1000 characters allowed.";
    } else {
        // Sanitize for SQL
        $name = mysqli_real_escape_string($connect, $name);
        $email = mysqli_real_escape_string($connect, $email);
        $message = mysqli_real_escape_string($connect, $message);

        $sql = "INSERT INTO contact_messages (name,email,message) VALUES ('$name','$email','$message')";

        if (mysqli_query($connect, $sql)) {
            $success = true;
        } else {
            $error = "Database error! Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Contact Us | Food Ordering System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
  body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:url('IMAGES/Contact Page.jpg');
    font-family:Arial, sans-serif;
}

.box{
    background:wheat;
    width:380px;
    padding:30px;
    border-radius:14px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.25);
    animation: pop 0.6s ease;
}

@keyframes pop{
    from{
        transform:scale(0.7);
        opacity:0;
    }
    to{
        transform:scale(1);
        opacity:1;
    }
}

/* SUCCESS */
.success-icon{
    font-size:55px;
    animation: bounce 1s ease infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.success h2{
    color:#28a745;
}

.success a{
    display:inline-block;
    margin-top:15px;
    padding:10px 25px;
    background:#28a745;
    color:#fff;
    text-decoration:none;
    border-radius:25px;
}

/* FORM */
input,textarea{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:1rem;
}
button{
    width:100%;
    padding:12px;
    border:none;
    background:#28a745;
    color:#fff;
    font-size:16px;
    border-radius:25px;
    cursor:pointer;
    transition:0.3s;
}
button:hover{
    background:#218838;
}

.error{
    background:#f8d7da;
    color:#842029;
    padding:10px;
    border-radius:6px;
    margin-bottom:10px;
    font-weight:bold;
}

.back-link{
    display:inline-block;
    margin-bottom:14px;
    color:#444;
    text-decoration:none;
    font-size:0.9rem;
    font-weight:bold;
}
.back-link:hover{
    color:#28a745;
}
</style>

</head>

<body>

<?php if($success): ?>

<!-- ✅ SUCCESS PAGE -->
<div class="box success">
    <a href="javascript:history.back()" class="back-link">&larr; Back</a>
    <div class="success-icon">✅</div>
    <h2>Message Sent!</h2>
    <p>
        Thank you <b><?= htmlspecialchars($name) ?></b><br>
        We received your message.<br>
        We will contact you soon 😊
    </p>
    <a href="contact.php">Send Another</a>
</div>

<?php else: ?>

<!-- 📨 FORM PAGE -->
<div class="box">
    <a href="javascript:history.back()" class="back-link">&larr; Back</a>
    <h2>Contact Us</h2>

    <?php if($error!=""): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="name" placeholder="Your Name" value="<?= htmlspecialchars($name) ?>" required>
        <input type="email" name="email" placeholder="Your Email" value="<?= htmlspecialchars($email) ?>" required>
        <textarea name="message" placeholder="Your Message" rows="5" required><?= htmlspecialchars($message) ?></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<?php endif; ?>

</body>
</html>
