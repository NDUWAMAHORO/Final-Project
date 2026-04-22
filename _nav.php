<?php
// Used by all admin pages - outputs full HTML head + sidebar + main open tag
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title).' – eSmart Admin' : 'eSmart Admin'; ?></title>
  <link rel="stylesheet" href="../style.css">
  <!-- ✅ CSS LINK -->
<link rel="stylesheet" href="../style.css">

<!-- Font Awesome (icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    /* ─── LAYOUT ─────────────────────────── */
    .admin-wrap { display:flex; min-height:calc(100vh - 70px); margin-top:70px; }

    /* ─── SIDEBAR ────────────────────────── */
    .sidebar {
      width:235px; background:var(--bg2); border-right:1px solid var(--border);
      flex-shrink:0; position:sticky; top:70px; height:calc(100vh - 70px); overflow-y:auto;
    }
    .sidebar-label {
      padding:20px 22px 12px; font-size:0.67rem; font-weight:700;
      letter-spacing:0.12em; text-transform:uppercase; color:var(--muted);
      border-bottom:1px solid var(--border); margin-bottom:6px;
    }
    .sidebar-link {
      display:flex; align-items:center; gap:11px; padding:12px 22px;
      color:var(--muted); border-left:3px solid transparent;
      text-decoration:none; font-size:0.87rem; font-weight:500; transition:all 0.18s;
    }
    .sidebar-link i { width:17px; font-size:0.9rem; text-align:center; }
    .sidebar-link:hover { background:rgba(245,166,35,0.05); color:var(--text); }
    .sidebar-link.active { background:rgba(245,166,35,0.1); color:var(--accent); border-left-color:var(--accent); }
    .sidebar-divider { height:1px; background:var(--border); margin:8px 18px; }

    /* ─── MAIN ───────────────────────────── */
    .admin-main { flex:1; padding:28px 32px; overflow-x:auto; background:var(--bg); min-width:0; }

    /* ─── TOP NAV BAR ────────────────────── */
    .top-tabs {
      display:flex; gap:6px; margin-bottom:26px;
      background:var(--card); border:1px solid var(--border);
      border-radius:12px; padding:5px; width:fit-content; flex-wrap:wrap;
    }
    .tab-link {
      display:inline-flex; align-items:center; gap:7px;
      padding:9px 16px; border-radius:9px; font-size:0.84rem;
      font-weight:600; text-decoration:none; color:var(--muted); transition:all 0.18s;
    }
    .tab-link i { font-size:0.86rem; }
    .tab-link:hover { background:var(--bg2); color:var(--text); }
    .tab-link.active { background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#0b0c0f; }

    /* ─── PAGE TITLE ─────────────────────── */
    .page-title { font-family:var(--font-head); font-size:1.35rem; font-weight:800; margin-bottom:22px; display:flex; align-items:center; gap:10px; }
    .page-title i { color:var(--accent); }

    /* ─── STAT CARDS ─────────────────────── */
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:26px; }
    .stat-card { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:20px 22px; display:flex; align-items:center; gap:14px; transition:border-color 0.2s; }
    .stat-card:hover { border-color:rgba(245,166,35,0.35); }
    .stat-icon { width:46px; height:46px; border-radius:12px; background:rgba(245,166,35,0.1); display:flex; align-items:center; justify-content:center; font-size:1.15rem; color:var(--accent); flex-shrink:0; }
    .stat-info h3 { font-family:var(--font-head); font-size:1.85rem; font-weight:800; color:var(--accent); line-height:1; margin-bottom:3px; }
    .stat-info p { color:var(--muted); font-size:0.77rem; }

    /* ─── CARDS ──────────────────────────── */
    .card { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:22px; margin-bottom:22px; }
    .card-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid var(--border); }
    .card-head h3 { font-family:var(--font-head); font-size:0.93rem; font-weight:700; display:flex; align-items:center; gap:8px; margin:0; }
    .card-head h3 i { color:var(--accent); }

    /* ─── TABLE ──────────────────────────── */
    .tbl { width:100%; border-collapse:collapse; }
    .tbl th { padding:9px 12px; text-align:left; font-size:0.69rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--muted); border-bottom:1px solid var(--border); white-space:nowrap; }
    .tbl td { padding:12px; font-size:0.85rem; border-bottom:1px solid rgba(245,166,35,0.04); vertical-align:middle; }
    .tbl tbody tr:last-child td { border-bottom:none; }
    .tbl tbody tr:hover { background:rgba(245,166,35,0.02); }
    .empty-row td { text-align:center; color:var(--muted); padding:44px 12px !important; font-size:0.84rem; }
    .empty-row i { font-size:2rem; color:rgba(245,166,35,0.15); display:block; margin-bottom:10px; }

    /* ─── ACTION BUTTONS ─────────────────── */
    .btn-edit    { display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:6px;font-size:0.72rem;font-weight:600;text-decoration:none;background:rgba(245,166,35,0.1);color:var(--accent);border:1px solid rgba(245,166,35,0.22);transition:all 0.15s; }
    .btn-edit:hover { background:rgba(245,166,35,0.2); }
    .btn-del     { display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:6px;font-size:0.72rem;font-weight:600;text-decoration:none;background:rgba(255,80,80,0.1);color:#ff8080;border:1px solid rgba(255,80,80,0.22);transition:all 0.15s; }
    .btn-del:hover  { background:rgba(255,80,80,0.2); }
    .btn-approve { display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:6px;font-size:0.72rem;font-weight:600;text-decoration:none;background:rgba(80,200,120,0.1);color:#50c878;border:1px solid rgba(80,200,120,0.22);transition:all 0.15s; }
    .btn-approve:hover { background:rgba(80,200,120,0.2); }
    .btn-reject  { display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:6px;font-size:0.72rem;font-weight:600;text-decoration:none;background:rgba(255,80,80,0.1);color:#ff8080;border:1px solid rgba(255,80,80,0.22);transition:all 0.15s; }
    .act-cell    { display:flex;gap:6px;align-items:center;flex-wrap:wrap; }

    /* ─── PILLS / BADGES ─────────────────── */
    .pill         { display:inline-block;padding:3px 9px;border-radius:99px;font-size:0.7rem;font-weight:600; }
    .pill-green   { background:rgba(80,200,120,0.1); color:#50c878; border:1px solid rgba(80,200,120,0.22); }
    .pill-yellow  { background:rgba(245,166,35,0.1); color:#f5a623; border:1px solid rgba(245,166,35,0.22); }
    .pill-red     { background:rgba(255,80,80,0.1);  color:#ff8080; border:1px solid rgba(255,80,80,0.22); }
    .pill-muted   { background:rgba(122,125,142,0.1);color:var(--muted);border:1px solid rgba(122,125,142,0.18); }
    .badge        { background:rgba(245,166,35,0.1);color:var(--accent);border:1px solid rgba(245,166,35,0.2);border-radius:99px;padding:2px 9px;font-size:0.68rem;font-weight:700; }

    /* ─── ALERTS ─────────────────────────── */
    .alert         { padding:12px 16px;border-radius:9px;margin-bottom:20px;font-size:0.86rem;display:flex;align-items:center;gap:9px; }
    .alert-success { background:rgba(80,200,120,0.1);border:1px solid rgba(80,200,120,0.28);color:#50c878; }
    .alert-error   { background:rgba(255,80,80,0.1); border:1px solid rgba(255,80,80,0.28); color:#ff8080; }

    /* ─── CONFIRM BOX ────────────────────── */
    .confirm-box { background:rgba(255,80,80,0.06);border:1px solid rgba(255,80,80,0.28);border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap; }
    .confirm-box p { font-size:0.87rem; }
    .confirm-box p strong { color:#ff8080; }
    .confirm-btns { display:flex;gap:8px; }

    /* ─── FORMS ──────────────────────────── */
    .form-row   { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
    .form-row-3 { display:grid;grid-template-columns:2fr 1fr 1fr;gap:14px; }
    .form-group { margin-bottom:14px; }
    .form-group label { display:block;font-size:0.71rem;font-weight:700;color:var(--muted);margin-bottom:6px;letter-spacing:0.05em;text-transform:uppercase; }
    .form-group input,
    .form-group select,
    .form-group textarea { width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:10px 13px;font-size:0.86rem;color:var(--text);font-family:inherit;transition:border-color 0.2s,box-shadow 0.2s; }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color:var(--accent);outline:none;box-shadow:0 0 0 3px rgba(245,166,35,0.1); }
    .form-group textarea { resize:vertical;min-height:80px; }
    .form-actions { display:flex;gap:10px;align-items:center;margin-top:4px; }

    /* ─── THUMB ──────────────────────────── */
    .img-thumb { width:60px;height:44px;object-fit:cover;border-radius:6px;border:1px solid var(--border); }
    .img-placeholder-sm { width:60px;height:44px;background:var(--bg2);border-radius:6px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:1rem; }

    /* ─── RESPONSIVE ─────────────────────── */
    @media(max-width:1100px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:900px) { .form-row-3{ grid-template-columns:1fr 1fr; } }
    @media(max-width:768px) {
      .admin-wrap { flex-direction:column; }
      .sidebar { width:100%;height:auto;position:static;border-right:none;border-bottom:1px solid var(--border); }
      .sidebar-label { display:none; }
      .sidebar-nav { display:flex;overflow-x:auto; }
      .sidebar-link { border-left:none;border-bottom:3px solid transparent;white-space:nowrap;padding:11px 14px; }
      .sidebar-link.active { border-left:none;border-bottom-color:var(--accent); }
      .stats-grid { grid-template-columns:1fr 1fr; }
      .form-row,.form-row-3 { grid-template-columns:1fr; }
      .admin-main { padding:18px 14px; }
      .top-tabs { width:100%; }
    }
    @media(max-width:480px){ .stats-grid{ grid-template-columns:1fr; } }
  </style>
</head>
<body>

<!-- TOP HEADER -->
<header class="header">
<a href="../index.php" class="logo">
  <svg width="32" height="32" viewBox="0 0 36 36" 
       xmlns="http://www.w3.org/2000/svg" 
       style="vertical-align:middle; margin-right:6px;">
    <rect width="36" height="36" rx="8" fill="#1f1f1f" stroke="#f59e0b" stroke-width="1.5"/>
    <rect x="8"  y="10" width="13" height="4" rx="2" fill="#f59e0b"/>
    <rect x="8"  y="16" width="9"  height="4" rx="2" fill="#f59e0b" opacity="0.55"/>
    <rect x="8"  y="22" width="13" height="4" rx="2" fill="#f59e0b"/>
    <circle cx="25" cy="18" r="4" fill="#f59e0b" opacity="0.9"/>
  </svg>e<span>Smart</span>
</a>

<div style="display:flex;align-items:center;gap:10px;">
  <span style="color:var(--muted);font-size:0.82rem;">
    <i class="fas fa-user-shield" style="color:var(--accent);margin-right:4px;"></i>
    <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
  </span>
  <a href="feedback.php" class="btn btn-outline btn-sm"><i class="fas fa-star"></i> Feedback</a>
  <a href="message.php" class="btn btn-outline btn-sm"><i class="fas fa-globe"></i> View Contact Us</a>
  <a href="logout.php" class="btn btn-primary btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
</header>

<div class="admin-wrap">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-label">Admin Panel</div>
    <nav class="sidebar-nav" style="padding:6px 0 20px;display:flex;flex-direction:column;">
      <a href="dashboard.php"   class="sidebar-link <?php echo $current==='dashboard.php'  ?'active':''; ?>"><i class="fas fa-chart-pie"></i> Dashboard</a>
      <a href="courses.php"     class="sidebar-link <?php echo $current==='courses.php'    ?'active':''; ?>"><i class="fas fa-book-open"></i> Courses</a>
      <a href="teachers.php"    class="sidebar-link <?php echo $current==='teachers.php'   ?'active':''; ?>"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
      <a href="learners.php"    class="sidebar-link <?php echo $current==='learners.php'   ?'active':''; ?>"><i class="fas fa-user-graduate"></i> Learners</a>
      <a href="enrollments.php" class="sidebar-link <?php echo $current==='enrollments.php'?'active':''; ?>"><i class="fas fa-clipboard-list"></i> Enrollments</a>
      <div class="sidebar-divider"></div>
      <a href="logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </aside>

  <!-- MAIN -->
  <main class="admin-main">

    <!-- TOP TABS -->
    <div class="top-tabs">
      <a href="courses.php"     class="tab-link <?php echo $current==='courses.php'    ?'active':''; ?>"><i class="fas fa-book-open"></i> Courses</a>
      <a href="teachers.php"    class="tab-link <?php echo $current==='teachers.php'   ?'active':''; ?>"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
      <a href="learners.php"    class="tab-link <?php echo $current==='learners.php'   ?'active':''; ?>"><i class="fas fa-user-graduate"></i> Learners</a>
      <a href="enrollments.php" class="tab-link <?php echo $current==='enrollments.php'?'active':''; ?>"><i class="fas fa-clipboard-list"></i> Enrollments</a>
    </div>
<?php // main content starts here ?>
