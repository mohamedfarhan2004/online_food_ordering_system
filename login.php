<?php
session_start();
include 'db.php';
include 'includes/otp.php';

$msg_user = "";
$msg_admin = "";
$active_tab = (isset($_GET['type']) && $_GET['type'] === 'admin') ? 'admin' : 'user';

// Only allow redirecting back to a known local page after login - never to
// an external URL - to avoid open-redirect issues.
function safe_redirect_target($target) {
    $allowed = ['orders.php', 'menu.php', 'my_orders.php', 'home.php'];
    return in_array($target, $allowed, true) ? $target : 'home.php';
}
$redirect_target = safe_redirect_target($_GET['redirect'] ?? $_POST['redirect'] ?? '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_type = $_POST['login_type'] ?? 'user';

    if ($login_type === 'admin') {
        $active_tab = 'admin';
        $admin_username = trim($_POST['admin_username'] ?? '');
        $admin_password = $_POST['admin_password'] ?? '';

        if ($admin_username === ADMIN_USERNAME && $admin_password === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin_username;
            header("Location: admin/dashboard.php");
            exit;
        } else {
            $msg_admin = "Invalid admin username or password.";
        }

    } else {
        $active_tab = 'user';
        $email = mysqli_real_escape_string($connect, $_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email == "" || $password == "") {
            $msg_user = "All fields are required!";
        } else {
            $sql = "SELECT * FROM users WHERE email='$email'";
            $result = mysqli_query($connect, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    if ((int)$row['is_verified'] !== 1) {
                        // Not verified yet - send a fresh OTP and redirect to verify it
                        $code = create_otp($connect, $row['email'], 'signup');
                        $sent = send_otp_email($row['email'], $code, 'signup');
                        $_SESSION['pending_verify_email'] = $row['email'];
                        if (!$sent) { $_SESSION['dev_otp_code'] = $code; }
                        header("Location: verify_otp.php?purpose=signup");
                        exit;
                    }
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['fullname'];
                    header("Location: " . $redirect_target);
                    exit;
                } else {
                    $msg_user = "Invalid password!";
                }
            } else {
                $msg_user = "User not found!";
            }
        }
    }
}

$reset_success = (isset($_GET['reset']) && $_GET['reset'] == "success");
$just_verified = (isset($_GET['verified']) && $_GET['verified'] == "1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Food Delight</title>
<link rel="Stylesheet" href="login.css">
<style>
.container {
 background: url("IMAGES/Login.jpg") no-repeat center center / cover !important;
}
.login-type-toggle {
  display: flex;
  margin-bottom: 18px;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.4);
}
.login-type-toggle button {
  flex: 1;
  padding: 12px 10px;
  border: none;
  background: rgba(255,255,255,0.15);
  color: #fff;
  font-weight: bold;
  font-size: 0.95rem;
  cursor: pointer;
  transition: background 0.2s ease;
}
.login-type-toggle button.active {
  background: crimson;
}
.login-form form.hidden-form {
  display: none;
}
</style>
</head>
<body>

<?php include 'includes/site_nav.php'; ?>

<div class="container">
  <div class="login-form" style="background:rgba(0,0,0,0.35); padding:35px; border-radius:12px; width:340px;">
    <h2 style="text-align:center; color:#fff; margin-bottom:15px;">Login</h2>

    <div class="login-type-toggle">
      <button type="button" id="userTabBtn" class="<?= $active_tab==='user' ? 'active' : '' ?>" onclick="showTab('user')">User Login</button>
      <button type="button" id="adminTabBtn" class="<?= $active_tab==='admin' ? 'active' : '' ?>" onclick="showTab('admin')">Admin Login</button>
    </div>

    <?php if($reset_success): ?>
      <p style="color: lightgreen; text-align:center;">Password Updated Successfully! Please Login.</p>
    <?php endif; ?>

    <?php if($just_verified): ?>
      <p style="color: lightgreen; text-align:center;">Email verified successfully! Please Login.</p>
    <?php endif; ?>

    <!-- USER LOGIN FORM -->
    <form action="" method="POST" id="userForm" class="<?= $active_tab==='user' ? '' : 'hidden-form' ?>">
      <input type="hidden" name="login_type" value="user">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect_target) ?>">

      <?php if($msg_user != ""): ?>
        <div class="error"><?php echo htmlspecialchars($msg_user); ?></div>
      <?php endif; ?>

      <input type="email" name="email" placeholder="Enter Email" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <button type="submit" class="login">Login as User</button>

      <div class="forget">
        <p>Forgot Password? <a href="forgot.php">Click Here</a></p>
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
      </div>
    </form>

    <!-- ADMIN LOGIN FORM -->
    <form action="" method="POST" id="adminForm" class="<?= $active_tab==='admin' ? '' : 'hidden-form' ?>">
      <input type="hidden" name="login_type" value="admin">

      <?php if($msg_admin != ""): ?>
        <div class="error"><?php echo htmlspecialchars($msg_admin); ?></div>
      <?php endif; ?>

      <input type="text" name="admin_username" placeholder="Admin Username" required>
      <input type="password" name="admin_password" placeholder="Admin Password" required>
      <button type="submit" class="login">Login as Admin</button>
    </form>

  </div>
</div>

<script>
function showTab(tab) {
  document.getElementById('userForm').classList.toggle('hidden-form', tab !== 'user');
  document.getElementById('adminForm').classList.toggle('hidden-form', tab !== 'admin');
  document.getElementById('userTabBtn').classList.toggle('active', tab === 'user');
  document.getElementById('adminTabBtn').classList.toggle('active', tab === 'admin');
}
</script>

</body>
</html>
