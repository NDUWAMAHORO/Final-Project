<?php
session_start();
require_once 'connection.php';
$error=''; $success='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $fn=trim($_POST['full_name']); $em=trim($_POST['email']); $msg=trim($_POST['message']);
    if(empty($fn)||empty($em)||empty($msg)){$error='All fields are required.';}
    elseif(!filter_var($em,FILTER_VALIDATE_EMAIL)){$error='Please enter a valid email.';}
    else{
        $s=$conn->prepare("INSERT INTO contact_messages (full_name,email,message) VALUES(?,?,?)");
        $s->bind_param("sss",$fn,$em,$msg);
        $s->execute()?$success='Message sent! We will get back to you soon.':$error='Failed to send. Please try again.';
        $s->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Contact Us – eSmart</title>
  <link rel="stylesheet" href="style.css">

  <style>
    .contact-wrap{max-width:960px;margin:100px auto 60px;padding:0 24px;}
    .contact-title{font-family:var(--font-head);font-size:2.2rem;font-weight:800;margin-bottom:6px;}
    .contact-title span{color:var(--accent);}
    .contact-sub{color:var(--muted);margin-bottom:40px;font-size:0.92rem;}
    .contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:28px;}

    .info-box,.form-box,.about-box{
      background:var(--card);
      border:1px solid var(--border);
      border-radius:var(--radius);
      padding:28px;
    }

    .info-item{margin-bottom:22px;}
    .info-item strong{
      display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;
      letter-spacing:0.06em;color:var(--accent);margin-bottom:5px;
    }
    .info-item span{font-size:0.88rem;color:var(--text);}

    .form-group{margin-bottom:14px;}
    .form-group input,.form-group textarea{
      width:100%;background:var(--bg);border:1px solid var(--border);
      border-radius:8px;padding:11px 13px;font-size:0.87rem;color:var(--text);
      font-family:inherit;
    }
    .form-group input:focus,.form-group textarea:focus{
      border-color:var(--accent);
      outline:none;
      box-shadow:0 0 0 3px rgba(245,166,35,0.1);
    }
    .form-group textarea{resize:vertical;min-height:120px;}

    .alert{
      padding:12px 16px;border-radius:9px;margin-bottom:16px;
      font-size:0.85rem;display:flex;align-items:center;gap:9px;
    }
    .alert-success{background:rgba(80,200,120,0.1);border:1px solid rgba(80,200,120,0.28);color:#50c878;}
    .alert-error{background:rgba(255,80,80,0.1);border:1px solid rgba(255,80,80,0.28);color:#ff8080;}

    /* ABOUT SECTION */
    .about-box{
      margin-top:30px;
    }
    .about-box h2{
      font-family:var(--font-head);
      font-size:1.3rem;
      margin-bottom:10px;
      color:var(--accent);
    }
    .about-box p{
      color:var(--muted);
      font-size:0.9rem;
      line-height:1.6;
      margin-bottom:10px;
    }

    @media(max-width:700px){
      .contact-grid{grid-template-columns:1fr;}
    }
    .map-container {
    aspect-ratio: 16 / 9; /* Defines the 16:9 shape directly */
}
.map-container iframe {
    width: 100%;
    height: 100%;
}
  </style>
  <!-- ✅ CSS LINK -->
<link rel="stylesheet" href="../style.css">

<!-- Font Awesome (icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>
<body>

<header class="header">
  <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
    <svg width="32" height="32" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;">
      <rect width="36" height="36" rx="8" fill="#1f1f1f" stroke="#f59e0b" stroke-width="1.5"/>
      <rect x="8" y="10" width="13" height="4" rx="2" fill="#f59e0b"/>
      <rect x="8" y="16" width="9"  height="4" rx="2" fill="#f59e0b" opacity="0.55"/>
      <rect x="8" y="22" width="13" height="4" rx="2" fill="#f59e0b"/>
      <circle cx="25" cy="18" r="4" fill="#f59e0b" opacity="0.9"/>
    </svg>
    <span>e<span style="color: var(--accent);">Smart</span></span>
  </a>
  <nav style="display: flex; gap: 10px; align-items: center;">
    <?php if(isset($_SESSION['learner_id'])): ?>
      <span style="color: var(--muted); font-size: 0.83rem;">
        <?php echo htmlspecialchars($_SESSION['learner_name']); ?>
      </span>
      <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
    <?php else:?>
      <a href="login.php" class="btn btn-outline btn-sm">Login</a>
      <a href="register.php" class="btn btn-primary btn-sm">Register</a>
    <?php endif;?>
  </nav>
</header>

<div class="contact-wrap">
  <h1 class="contact-title">Contact <span>Us</span></h1>
  <p class="contact-sub">Have questions or need help? We're always here.</p>

  <div class="contact-grid">
    
    <!-- LEFT INFO -->
    <div class="info-box">
      <div class="info-item"><strong>Email</strong><span>info@esmart.rw</span></div>
      <div class="info-item"><strong>Phone</strong><span>+250 785 651 467</span></div>
      <div class="info-item"><strong>Location</strong><span>Kinyinya, Kigali, Rwanda</span></div>
      <div class="info-item"><strong>Working Hours</strong><span>Mon – Fri: 8AM – 6PM</span></div>
    </div>

    <!-- RIGHT FORM -->
    <div class="form-box">
      <?php if($error):?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error);?></div>
      <?php endif;?>

      <?php if($success):?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success);?></div>
      <?php endif;?>

      <form method="POST">
        <div class="form-group">
          <input type="text" name="full_name" placeholder="Your Full Name" required>
        </div>
        <div class="form-group">
          <input type="email" name="email" placeholder="Your Email Address" required>
        </div>
        <div class="form-group">
          <textarea name="message" placeholder="Your Message..." required></textarea>
        </div>
        <button class="btn btn-primary" style="width:100%;">
          Send Message
        </button>
      </form>
    </div>
  </div>

  <!-- ✅ ABOUT SECTION ADDED HERE -->
  <div class="about-box">
    <h2>About eSmart</h2>
    <p>
      eSmart is a modern online learning platform designed to help students,
      teachers, and professionals gain practical digital skills.
      We provide structured courses in programming, databases, web development,
      and ICT fundamentals.
    </p>
    <p>
      Our mission is to make quality education accessible, simple, and affordable
      for everyone in Rwanda and beyond. We connect learners with experienced
      instructors and real-world learning materials.
    </p>
    <p>
      At eSmart, we believe in learning by doing building skills that prepare you
      for real careers in technology and business.
    </p>
  </div>

</div>

  <!-- MAP SECTION (Google Maps) -->
  <div class="map-box">
    <h2><i class="fas fa-map-marker-alt"></i> Our Location</h2>
    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2860.8907835586215!2d30.106884873115902!3d-1.913420536629249!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca191429ec57d%3A0xf4e32309294a5e7!2sKinyinya%20Taxi%20Parking!5e1!3m2!1sen!2srw!4v1776631955749!5m2!1sen!2srw" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>

<footer>
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="logo">e<span>Smart</span></div>
      <p>Learn coding, databases, and more online.</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2026 eSmart. All rights reserved.</p>
  </div>
</footer>

</body>
</html>