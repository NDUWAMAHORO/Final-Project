<?php
session_start();
require_once '../connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {

        $stmt = $conn->prepare("SELECT id, full_name, password FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {

            $stmt->bind_result($id, $name, $db_password);
            $stmt->fetch();

            // FIXED: replaced password_verify() with md5()
            if ($db_password === md5($password)) {

                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_name'] = $name;

                header("Location:dashboard.php");
                exit;

            } else {
                $error = 'Invalid email or password.';
            }

        } else {
            $error = 'Invalid email or password.';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login – eSmart</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .auth-page { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:90px 20px; }
    .auth-card { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:38px 40px; width:100%; max-width:420px; }
    .auth-title { font-family:var(--font-head); font-size:1.6rem; font-weight:800; margin-bottom:20px; }
    .form-group { margin-bottom:16px; }
    .form-group input {
      width:100%; padding:12px; border-radius:10px;
      border:1px solid var(--border); background:var(--bg); color:var(--text);
    }
    .btn {
      width:100%; padding:12px; border:none; border-radius:30px;
      cursor:pointer; font-weight:700;
    }
    .btn-primary { background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#000; }
    .error { background:rgba(255,80,80,0.1); padding:10px; margin-bottom:10px; border-radius:8px; color:#ff8080; }
    .auth-switch { margin-top:15px; text-align:center; font-size:0.85rem; }
    .auth-switch a { color:var(--accent); }
  </style>
</head>
<body>

<header class="header">
  <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 8px;">
    <!-- SVG logo icon -->
    <svg width="32" height="32" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;">
      <rect width="36" height="36" rx="8" fill="#1f1f1f" stroke="#f59e0b" stroke-width="1.5"/>
      <rect x="8" y="10" width="13" height="4" rx="2" fill="#f59e0b"/>
      <rect x="8" y="16" width="9"  height="4" rx="2" fill="#f59e0b" opacity="0.55"/>
      <rect x="8" y="22" width="13" height="4" rx="2" fill="#f59e0b"/>
      <circle cx="25" cy="18" r="4" fill="#f59e0b" opacity="0.9"/>
    </svg>
    <span>e<span style="color: var(--accent);">Smart</span></span>
  </a>
</header>

<div class="auth-page">
  <div class="auth-card">

    <div class="auth-title">Admin Login</div>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <input type="email" name="email" placeholder="Email" required>
      </div>

      <div class="form-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <div class="auth-switch">
      Don't have an account? <a href="register.php">Register</a>
    </div>

  </div>
</div>

</body>
</html>