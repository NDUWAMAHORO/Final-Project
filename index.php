<?php
session_start();
require_once 'connection.php';
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $courses_result = $conn->query("SELECT * FROM course WHERE course_name LIKE '%$search%' ORDER BY created_at DESC");
} else {
    $courses_result = $conn->query("SELECT * FROM course ORDER BY created_at DESC");
}

$enrolled_courses = array();
if (isset($_SESSION['learner_id'])) {
    $lid = intval($_SESSION['learner_id']);
    $eq  = $conn->query("SELECT course_id, status FROM enrollment WHERE learner_id = $lid");
    while ($er = $eq->fetch_assoc()) {
        $enrolled_courses[$er['course_id']] = $er['status'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>eSmart – Learn Digital Skills</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .top-nav { display:flex; gap:10px; align-items:center; }

    /* ALERT BAR */
    .alert-bar {
      margin-top: 70px;
      padding: 13px 30px;
      text-align: center;
      font-size: 0.88rem;
    }
    .alert-bar.info    { background:rgba(245,166,35,0.1);  border-bottom:1px solid rgba(245,166,35,0.25); color:var(--accent); }
    .alert-bar.success { background:rgba(80,200,120,0.1);  border-bottom:1px solid rgba(80,200,120,0.25); color:#50c878; }
    .alert-bar.error   { background:rgba(255,80,80,0.1);   border-bottom:1px solid rgba(255,80,80,0.25);  color:#ff8080; }
    .alert-bar a { color:inherit; text-decoration:underline; }
    .no-alert-spacer { margin-top:70px; }

    /* HERO */
    .hero-section {
      padding: 90px 30px 70px;
      text-align: center;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
    }
    .hero-section h1 {
      font-family: var(--font-head);
      font-size: clamp(2rem, 5vw, 3.2rem);
      font-weight: 800;
      margin-bottom: 14px;
      letter-spacing: -0.03em;
    }
    .hero-section h1 span { color: var(--accent); }
    .hero-section p { color: var(--muted); font-size: 1rem; margin-bottom: 28px; max-width: 480px; margin-left:auto; margin-right:auto; }
    .hero-btns { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }

    /* COURSES SECTION */
    .courses-section { padding: 60px 30px 80px; max-width: 1300px; margin: 0 auto; }
    .section-head { text-align:center; margin-bottom:40px; }
    .section-head h2 { font-family:var(--font-head); font-size:2rem; font-weight:800; }
    .section-head h2 span { color:var(--accent); }
    .section-head p { color:var(--muted); margin-top:8px; font-size:0.92rem; }

.courses-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

@media (max-width: 992px) {
  .courses-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 576px) {
  .courses-grid {
    grid-template-columns: 1fr;
  }
}
    /* COURSE CARD */
    .course-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
      display: flex;
      flex-direction: column;
    }
    .course-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 18px 45px rgba(0,0,0,0.45);
      border-color: rgba(245,166,35,0.4);
    }
    .course-thumb {
      width: 100%;
      height: 170px;
      object-fit: cover;
      display: block;
      background: var(--bg2);
    }
    .course-thumb-placeholder {
      width: 100%;
      height: 170px;
      background: var(--bg2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3.5rem;
      color: rgba(245,166,35,0.25);
    }
    .course-body { padding: 20px; flex:1; display:flex; flex-direction:column; }
    .course-body h3 { font-family:var(--font-head); font-size:1rem; font-weight:700; margin-bottom:8px; }
    .course-desc { color:var(--muted); font-size:0.82rem; line-height:1.55; margin-bottom:auto; }
    .course-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 16px;
      padding-top: 14px;
      border-top: 1px solid var(--border);
    }
    .course-price { font-family:var(--font-head); font-size:1.15rem; font-weight:800; color:var(--accent); }
    .course-credits { color:var(--muted); font-size:0.77rem; }

    .enroll-btn {
      display: block;
      margin-top: 14px;
      text-align: center;
      padding: 11px;
      border-radius: 30px;
      font-weight: 700;
      font-size: 0.87rem;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #0b0c0f;
      text-decoration: none;
      transition: opacity 0.2s, transform 0.2s;
    }
    .enroll-btn:hover { opacity:0.88; transform:translateY(-1px); }

    .status-badge {
      display: block;
      margin-top: 14px;
      text-align: center;
      padding: 10px;
      border-radius: 30px;
      font-weight: 600;
      font-size: 0.82rem;
    }
    .status-pending  { background:rgba(245,166,35,0.1);  color:#f5a623; border:1px solid rgba(245,166,35,0.3); }
    .status-approved { background:rgba(80,200,120,0.1);  color:#50c878; border:1px solid rgba(80,200,120,0.3); }
    .status-rejected { background:rgba(255,80,80,0.1);   color:#ff8080; border:1px solid rgba(255,80,80,0.3); }

    .no-courses { text-align:center; padding:80px 20px; color:var(--muted); }
    .no-courses i { font-size:3rem; color:rgba(245,166,35,0.18); display:block; margin-bottom:14px; }

    /* HOW IT WORKS */
    .how-section { padding:70px 30px; background:var(--bg2); border-top:1px solid var(--border); text-align:center; }
    .how-section h2 { font-family:var(--font-head); font-size:1.8rem; font-weight:800; margin-bottom:36px; }
    .how-section h2 span { color:var(--accent); }
    .steps-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; max-width:900px; margin:0 auto; }
    .step-card { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:24px 20px; }
    .step-num { font-family:var(--font-head); font-size:2rem; font-weight:800; color:var(--accent); margin-bottom:8px; }
    .step-card h3 { font-family:var(--font-head); font-size:0.95rem; font-weight:700; margin-bottom:8px; }
    .step-card p { color:var(--muted); font-size:0.82rem; line-height:1.55; }

    /* ABOUT */
    .about-section { padding:70px 30px; border-top:1px solid var(--border); }
    .about-section h2 { font-family:var(--font-head); font-size:1.8rem; font-weight:800; text-align:center; margin-bottom:36px; }
    .about-section h2 span { color:var(--accent); }
    .about-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; max-width:900px; margin:0 auto; }
    .about-card { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:28px; }
    .about-card h3 { font-family:var(--font-head); font-size:1rem; font-weight:700; color:var(--accent); margin-bottom:10px; }
    .about-card p { color:var(--muted); font-size:0.87rem; line-height:1.7; }

    @media(max-width:768px){
      .about-grid { grid-template-columns:1fr; }
      .courses-grid { grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); }
    }
    @media(max-width:480px){
      .courses-grid { grid-template-columns:1fr; }
    }
    .search-box {
  margin-top: 25px;
  display: flex;
  justify-content: center;
}

.search-box form {
  display: flex;
  width: 100%;
  max-width: 400px;
}

.search-box input {
  flex: 1;
  padding: 12px 15px;
  border-radius: 30px 0 0 30px;
  border: none;
  outline: none;
  font-size: 0.9rem;
}

.search-box button {
  padding: 12px 18px;
  border: none;
  background: #f5a623;
  color: black;
  border-radius: 0 30px 30px 0;
  cursor: pointer;
  font-weight: bold;
}

.search-box button:hover {
  opacity: 0.9;
}
  </style>
  <!-- ✅ CSS LINK -->
<link rel="stylesheet" href="../style.css">

<!-- Font Awesome (icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<!-- HEADER -->
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
  <nav class="top-nav">
    <?php if (isset($_SESSION['learner_id'])): ?>
      <span style="color:var(--muted);font-size:0.84rem;">
        <i class="fas fa-user-graduate" style="color:var(--accent);margin-right:5px;"></i>
        <?php echo htmlspecialchars($_SESSION['learner_name']); ?>
      </span>
      <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
    <?php else: ?>
      <a href="index.php" class="btn btn-outline">Home</a>
      <a href="register.php" class="btn btn-outline">Register</a>
      <a href="login.php" class="btn btn-primary">Login</a>
    <?php endif; ?>
    <a href="leave_feedback.php" class="btn btn-outline btn-sm">Feedback</a>
    <a href="contact.php" class="btn btn-outline">About Us</a>
  </nav>
</header>
<!-- ALERT BAR -->
<?php
$alerts = array(
  'login_required' => array('type'=>'info',    'msg'=>'Please <a href="login.php">login</a> or <a href="register.php">create an account</a> to enroll in a course.'),
  'enrolled'       => array('type'=>'success', 'msg'=>'Enrollment submitted! Waiting for admin approval.'),
  'already'        => array('type'=>'info',    'msg'=>' You have already applied for this course.'),
  'rejected'       => array('type'=>'error',   'msg'=>' Your enrollment was rejected. Contact support for help.'),
);
if (isset($_GET['msg']) && isset($alerts[$_GET['msg']])): 
  $a = $alerts[$_GET['msg']];
?>
  <div class="alert-bar <?php echo $a['type']; ?>"><?php echo $a['msg']; ?></div>
<?php else: ?>
  <div class="no-alert-spacer"></div>
<?php endif; ?>

<!-- HERO -->
<section class="hero-section">
  <h1 style="font-size: 3.5rem; font-weight: 800;">
  Learn with <span style="color:#f5a623;">eSmart</span>
</h1>
  <p>Master in-demand digital skills from expert teachers. Browse our courses and start today.</p>
  <div class="hero-btns">
    <?php if (!isset($_SESSION['learner_id'])): ?>
      <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Get Started Free</a>
      <a href="login.php" class="btn btn-outline">Login</a>
    <?php else: ?>
      <a href="#courses" class="btn btn-primary"><i class="fas fa-book-open"></i> Browse Courses</a>
    <?php endif; ?>
  </div>
  <div class="search-box">
  <form method="GET" action="index.php">
    <input type="text" name="search" placeholder="Search courses..." />
    <button type="submit"><i class="fas fa-search"></i></button>
  </form>
</div>
</section>

<!-- COURSES -->
<div class="courses-section" id="courses">
  <div class="section-head">
    <h2>Our <span>Courses</span></h2>
    <p>Choose from our expert-led courses and start learning at your own pace.</p>
  </div>

  <?php if ($courses_result && $courses_result->num_rows > 0): ?>
    <div class="courses-grid">
      <?php while ($course = $courses_result->fetch_assoc()):
        $cid = $course['course_id'];
      ?>
        <div class="course-card">

          <?php if (!empty($course['image']) && file_exists('uploads/courses/' . $course['image'])): ?>
            <img src="uploads/courses/<?php echo htmlspecialchars($course['image']); ?>"
                 alt="<?php echo htmlspecialchars($course['course_name']); ?>"
                 class="course-thumb">
          <?php else: ?>
            <div class="course-thumb-placeholder">
              <i class="fas fa-book-open"></i>
            </div>
          <?php endif; ?>

          <div class="course-body">
            <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
            <p class="course-desc">
              <?php echo !empty($course['description'])
                ? htmlspecialchars(mb_strimwidth($course['description'], 0, 100, '...'))
                : 'No description available.'; ?>
            </p>

            <div class="course-meta">
              <span class="course-price">$<?php echo number_format($course['price'], 2); ?></span>
              <span class="course-credits">
                <i class="fas fa-star" style="color:var(--accent);margin-right:3px;font-size:0.75rem;"></i>
                <?php echo $course['credits']; ?> credits
              </span>
            </div>

            <?php if (isset($_SESSION['learner_id'])): ?>
              <?php if (isset($enrolled_courses[$cid])): ?>
                <?php $st = $enrolled_courses[$cid]; ?>
                <?php $icons = array('pending'=>'','approved'=>'','rejected'=>''); ?>
                <?php $labels = array('pending'=>'Pending Approval','approved'=>'Enrolled Approved','rejected'=>'Enrollment Rejected'); ?>
                <div class="status-badge status-<?php echo $st; ?>">
                  <?php echo $icons[$st] . ' ' . $labels[$st]; ?>
                </div>
              <?php else: ?>
                <a href="enroll.php?course_id=<?php echo $cid; ?>" class="enroll-btn">
                  <i class="fas fa-plus-circle"></i> Enroll Now
                </a>
              <?php endif; ?>
            <?php else: ?>
              <a href="register.php" class="enroll-btn">
                <i class="fas fa-user-plus"></i> Register to Enroll
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

  <?php else: ?>
    <div class="no-courses">
      <i class="fas fa-book-open"></i>
      <p>No courses available yet. Check back soon!</p>
    </div>
  <?php endif; ?>
</div>

<!-- HOW IT WORKS -->
<section class="how-section">
  <h2>How It <span>Works</span></h2>
  <div class="steps-grid">
    <div class="step-card"><div class="step-num">01</div><h3>Browse Courses</h3><p>Explore our catalog of digital skills courses designed for all levels.</p></div>
    <div class="step-card"><div class="step-num">02</div><h3>Create Account</h3><p>Register as a learner it's free and takes less than a minute.</p></div>
    <div class="step-card"><div class="step-num">03</div><h3>Enroll</h3><p>Submit your enrollment and wait for admin approval.</p></div>
    <div class="step-card"><div class="step-num">04</div><h3>Start Learning</h3><p>Once approved, access your course content and grow your skills.</p></div>
  </div>
</section>

<!-- ABOUT -->
<section class="about-section">
  <h2>Our <span>Mission & Vision</span></h2>
  <div class="about-grid">
    <div class="about-card">
      <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
      <p>Empower students and young professionals with affordable, accessible digital skills education that prepares them for real-world opportunities.</p>
    </div>
    <div class="about-card">
      <h3><i class="fas fa-eye"></i> Our Vision</h3>
      <p>Become Africa's leading online learning platform helping millions of learners gain skills, build careers, and achieve financial independence.</p>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="logo">e<span>Smart</span></div>
      <p>Learn coding, databases, and more. Start your journey today!</p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="#courses">Courses</a></li>
        <li><a href="contact.php">About Us</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Learners</h4>
      <ul>
        <li><a href="register.php">Register</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </div>
  
  </div>
  <div class="footer-bottom">
    <p>© 2026 eSmart. All rights reserved.</p>
  </div>
</footer>

</body>
</html>