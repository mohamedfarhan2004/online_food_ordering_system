<?php
session_start();
include 'db.php';
include 'includes/otp.php';

$msg = "";
$email = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])){
    $email = mysqli_real_escape_string($connect, $_POST['email'] ?? '');

    if($email == ""){
        $msg = "Please enter your email!";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($connect, $sql);

        if($result && mysqli_num_rows($result) > 0){
            // Email verified - send an OTP code before allowing a reset
            $code = create_otp($connect, $email, 'reset');
            $sent = send_otp_email($email, $code, 'reset');

            $_SESSION['pending_reset_email'] = $email;
            if (!$sent) {
                $_SESSION['dev_otp_code'] = $code;
            }
            header("Location: verify_otp.php?purpose=reset");
            exit;
        } else {
            $msg = "Email not found in our records!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<link rel="stylesheet" href="forgot.css">
</head>
<body>

<?php include 'includes/site_nav.php'; ?>

<div class="page-content">
<div class="forgot-container">
    <h2>Forgot Password</h2>

    <?php if($msg != ""): ?>
        <p class="error"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <form method="POST" action="forgot.php">
        <label for="email">Enter Your Registered Email</label>
        <input type="email" id="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>" required>

        <button type="submit" name="reset" class="btn">Send Verification Code</button>
    </form>

    <p>Remembered Your Password? <a href="login.php">Login here</a></p>
    <p>Don't have an Account? <a href="signup.php">Sign Up</a></p>
</div>
</div>

</body>
</html>
