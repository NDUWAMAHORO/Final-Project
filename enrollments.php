<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = 'Enrollments';
$msg = '';
$msg_type = 'success';

/* APPROVE */
if (isset($_GET['action']) && $_GET['action'] === 'approve' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $s = $conn->prepare("UPDATE enrollment SET status='approved' WHERE enrollment_id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $s->close();

    header("Location: enrollments.php?msg=" . urlencode('Enrollment approved.') . "&msg_type=success");
    exit;
}

/* REJECT */
if (isset($_GET['action']) && $_GET['action'] === 'reject' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $s = $conn->prepare("UPDATE enrollment SET status='rejected' WHERE enrollment_id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $s->close();

    header("Location: enrollments.php?msg=" . urlencode('Enrollment rejected.') . "&msg_type=success");
    exit;
}

/* DELETE */
if (isset($_GET['delete']) && isset($_GET['confirmed'])) {
    $id = intval($_GET['delete']);
    $s = $conn->prepare("DELETE FROM enrollment WHERE enrollment_id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $s->close();

    header("Location: enrollments.php?msg=" . urlencode('Enrollment deleted.') . "&msg_type=success");
    exit;
}

/* MESSAGES */
if (isset($_GET['msg'])) $msg = urldecode($_GET['msg']);
if (isset($_GET['msg_type'])) $msg_type = $_GET['msg_type'];

$confirm_id = null;
if (isset($_GET['delete']) && !isset($_GET['confirmed'])) {
    $confirm_id = intval($_GET['delete']);
}

/* FILTER */
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$allowed = array('all', 'pending', 'approved', 'rejected');
if (!in_array($filter, $allowed)) $filter = 'all';

$where = ($filter !== 'all') ? "WHERE e.status='$filter'" : '';

/* COUNTS */
$counts = array(
    'all' => $conn->query("SELECT COUNT(*) AS c FROM enrollment")->fetch_assoc(),
    'pending' => $conn->query("SELECT COUNT(*) AS c FROM enrollment WHERE status='pending'")->fetch_assoc(),
    'approved' => $conn->query("SELECT COUNT(*) AS c FROM enrollment WHERE status='approved'")->fetch_assoc(),
    'rejected' => $conn->query("SELECT COUNT(*) AS c FROM enrollment WHERE status='rejected'")->fetch_assoc()
);

$counts['all'] = $counts['all']['c'];
$counts['pending'] = $counts['pending']['c'];
$counts['approved'] = $counts['approved']['c'];
$counts['rejected'] = $counts['rejected']['c'];

include '../_nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Enrollments</title>

<!-- ✅ CSS LINK -->
<link rel="stylesheet" href="../style.css">

<!-- Font Awesome (icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<?php if ($msg): ?>
<div class="alert alert-<?php echo ($msg_type == 'error' ? 'error' : 'success'); ?>">
    <i class="fas fa-<?php echo ($msg_type == 'error' ? 'exclamation-circle' : 'check-circle'); ?>"></i>
    <?php echo htmlspecialchars($msg); ?>
</div>
<?php endif; ?>

<?php if ($confirm_id): ?>
<div class="confirm-box">
    <p>
        <i class="fas fa-exclamation-triangle" style="color:#ff8080;margin-right:8px;"></i>
        Delete this enrollment permanently?
    </p>
    <div class="confirm-btns">
        <a href="enrollments.php?delete=<?php echo $confirm_id; ?>&confirmed=1" class="btn-del">
            <i class="fas fa-trash"></i> Yes Delete
        </a>
        <a href="enrollments.php" class="btn btn-outline btn-sm">
            Cancel
        </a>
    </div>
</div>
<?php endif; ?>

<div class="page-title">
    <i class="fas fa-clipboard-list"></i> Enrollment Requests
</div>

<!-- FILTER -->
<div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
<?php
$tabs = array('all','pending','approved','rejected');

foreach ($tabs as $t):
    $active = ($filter == $t);
?>
<a href="enrollments.php?filter=<?php echo $t; ?>"
   style="
   padding:8px 14px;
   border-radius:8px;
   text-decoration:none;
   font-size:0.85rem;
   font-weight:600;
   background:<?php echo $active ? 'rgba(245,166,35,0.15)' : 'var(--card)'; ?>;
   border:1px solid var(--border);
   color:var(--text);
   ">
   <?php echo ucfirst($t); ?> (<?php echo $counts[$t]; ?>)
</a>
<?php endforeach; ?>
</div>

<!-- TABLE -->
<div class="card">
<div class="card-head">
    <h3><i class="fas fa-list"></i> <?php echo ucfirst($filter); ?> Enrollments</h3>
</div>

<table class="tbl">
<thead>
<tr>
    <th>ID</th>
    <th>Learner</th>
    <th>Course</th>
    <th>Price</th>
    <th>Status</th>
    <th>Date</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>
<?php
$enr = $conn->query("
SELECT e.*, l.full_name, l.email, c.course_name, c.price
FROM enrollment e
JOIN learner l ON e.learner_id=l.learner_id
JOIN course c ON e.course_id=c.course_id
$where
ORDER BY e.enrolled_at DESC
");

if ($enr && $enr->num_rows > 0):
while ($r = $enr->fetch_assoc()):
?>
<tr>
    <td><?php echo $r['enrollment_id']; ?></td>

    <td>
        <strong><?php echo htmlspecialchars($r['full_name']); ?></strong><br>
        <span style="font-size:12px;color:gray;"><?php echo $r['email']; ?></span>
    </td>

    <td><?php echo htmlspecialchars($r['course_name']); ?></td>

    <td style="color:green;font-weight:bold;">
        $<?php echo number_format($r['price'],2); ?>
    </td>

    <td>
        <span class="pill"><?php echo ucfirst($r['status']); ?></span>
    </td>

    <td><?php echo date('M d, Y', strtotime($r['enrolled_at'])); ?></td>

    <td>
        <?php if ($r['status'] == 'pending'): ?>
            <a href="enrollments.php?action=approve&id=<?php echo $r['enrollment_id']; ?>" class="btn-approve">Approve</a>
            <a href="enrollments.php?action=reject&id=<?php echo $r['enrollment_id']; ?>" class="btn-reject">Reject</a>
        <?php endif; ?>

        <a href="enrollments.php?delete=<?php echo $r['enrollment_id']; ?>" class="btn-del">Delete</a>
    </td>
</tr>
<?php endwhile; else: ?>
<tr>
    <td colspan="7" style="text-align:center;">No enrollments found</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

<footer class="footer">
    <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> eSmart. All rights reserved.</p>
    </div>
</footer>
</body>
</html>