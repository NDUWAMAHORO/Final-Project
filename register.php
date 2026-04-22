<?php
require_once '../connection.php';

$error=''; $success='';

if ($_SERVER['REQUEST_METHOD']==='POST'){
    $fn=trim($_POST['full_name']);
    $em=trim($_POST['email']);
    $pw=$_POST['password'];

    if(empty($fn)||empty($em)||empty($pw)){
        $error='All fields required.';
    } else {

        $c=$conn->prepare("SELECT id FROM admin WHERE email=?");
        $c->bind_param("s",$em);
        $c->execute();
        $c->store_result();

        if($c->num_rows>0){
            $error='Email already registered.';
        } else {

            // FIXED: replaced password_hash() for old PHP support
            $h = md5($pw);

            $s=$conn->prepare("INSERT INTO admin (full_name,email,password) VALUES(?,?,?)");
            $s->bind_param("sss",$fn,$em,$h);

            if($s->execute()){
                $success='Account created! You can now login.';
            } else {
                $error='Registration failed.';
            }

            $s->close();
        }

        $c->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Admin Register – eSmart</title>
  <link rel="stylesheet" href="../style.css">

  <style>
    .auth-page{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:90px 20px 40px;}
    .auth-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:38px 40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.4);}
    .auth-brand{font-family:var(--font-head);font-size:1.1rem;font-weight:800;margin-bottom:8px;} .auth-brand span{color:var(--accent);}
    .auth-title{font-family:var(--font-head);font-size:1.6rem;font-weight:800;letter-spacing:-0.03em;margin-bottom:4px;}
    .auth-sub{color:var(--muted);font-size:0.83rem;margin-bottom:26px;}
    .form-group{margin-bottom:16px;} .form-group label{display:block;font-size:0.72rem;font-weight:700;color:var(--muted);margin-bottom:6px;letter-spacing:0.05em;text-transform:uppercase;}
    .form-group input{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:11px 14px;font-size:0.88rem;color:var(--text);font-family:inherit;}
    .form-group input:focus{border-color:var(--accent);outline:none;box-shadow:0 0 0 3px rgba(245,166,35,0.1);}
    .alert{padding:12px 16px;border-radius:9px;margin-bottom:18px;font-size:0.85rem;display:flex;align-items:center;gap:9px;}
    .alert-error{background:rgba(255,80,80,0.1);border:1px solid rgba(255,80,80,0.28);color:#ff8080;}
    .alert-success{background:rgba(80,200,120,0.1);border:1px solid rgba(80,200,120,0.28);color:#50c878;}
    .auth-switch{text-align:center;margin-top:18px;font-size:0.83rem;color:var(--muted);} .auth-switch a{color:var(--accent);}
  </style>
</head><body>

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
  <a href="../index.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
</header>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-brand">e<span>Smart</span></div>
    <div class="auth-title">Admin Register</div>
    <div class="auth-sub">Create an administrator account.</div>

    <?php if($error):?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error);?>
      </div>
    <?php endif;?>

    <?php if($success):?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($success);?>
      </div>
    <?php endif;?>

    <form method="POST" action="register.php">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" required
               value="<?php echo isset($_POST['full_name'])?htmlspecialchars($_POST['full_name']):'';?>">
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required
               value="<?php echo isset($_POST['email'])?htmlspecialchars($_POST['email']):'';?>">
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
        <i class="fas fa-user-shield"></i> Create Account
      </button>
    </form>

    <div class="auth-switch">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>
</div>

</body>
</html>