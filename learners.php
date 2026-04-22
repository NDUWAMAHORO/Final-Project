<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = 'Learners';
$msg = '';
$msg_type = 'success';

/* DELETE LEARNER */
if (isset($_GET['delete']) && isset($_GET['confirmed'])) {

    $id = intval($_GET['delete']);

    $s = $conn->prepare("DELETE FROM learner WHERE learner_id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $s->close();

    header("Location: learners.php?msg=" . urlencode('Learner removed.') . "&msg_type=success");
    exit;
}

/* MESSAGES */
if (isset($_GET['msg'])) $msg = urldecode($_GET['msg']);
if (isset($_GET['msg_type'])) $msg_type = $_GET['msg_type'];

/* CONFIRM DELETE */
$confirm_id = null;
$confirm_name = '';

if (isset($_GET['delete']) && !isset($_GET['confirmed'])) {

    $confirm_id = intval($_GET['delete']);

    $row = $conn->query("SELECT full_name FROM learner WHERE learner_id=$confirm_id");
    $row = $row->fetch_assoc();

    $confirm_name = $row ? $row['full_name'] : 'this learner';
}

/* TOTAL */
$total = $conn->query("SELECT COUNT(*) AS c FROM learner");
$total = $total->fetch_assoc();
$total = $total['c'];

include '../_nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Learners</title>

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
      Remove <strong>"<?php echo htmlspecialchars($confirm_name); ?>"</strong>? Their enrollments will also be deleted.
    </p>
    <div class="confirm-btns">
      <a href="learners.php?delete=<?php echo $confirm_id; ?>&confirmed=1" class="btn-del">
        <i class="fas fa-trash"></i> Yes, Remove
      </a>
      <a href="learners.php" class="btn btn-outline btn-sm">
        <i class="fas fa-times"></i> Cancel
      </a>
    </div>
  </div>
<?php endif; ?>

<div class="page-title">
    <i class="fas fa-user-graduate"></i> Registered Learners
</div>

<div class="card">
  <div class="card-head">
    <h3><i class="fas fa-list"></i> All Learners</h3>
    <span class="badge"><?php echo $total; ?> learners</span>
  </div>

  <table class="tbl">
    <thead>
      <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Enrollments</th>
        <th>Date Joined</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $learners = $conn->query("
        SELECT l.*,
          COUNT(e.enrollment_id) AS total_enr,
          SUM(CASE WHEN e.status='approved' THEN 1 ELSE 0 END) AS approved_enr,
          SUM(CASE WHEN e.status='pending' THEN 1 ELSE 0 END) AS pending_enr
        FROM learner l
        LEFT JOIN enrollment e ON l.learner_id = e.learner_id
        GROUP BY l.learner_id
        ORDER BY l.date_joined DESC
      ");

      if ($learners && $learners->num_rows > 0):
        while ($row = $learners->fetch_assoc()):
      ?>
        <tr>
          <td style="color:var(--muted);"><?php echo $row['learner_id']; ?></td>

          <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>

          <td style="color:var(--muted);">
            <?php echo htmlspecialchars($row['email']); ?>
          </td>

          <td>
            <span class="pill pill-green">
              <?php echo $row['approved_enr']; ?> approved
            </span>

            <?php if ($row['pending_enr'] > 0): ?>
              <span class="pill pill-yellow">
                <?php echo $row['pending_enr']; ?> pending
              </span>
            <?php endif; ?>

            <span class="pill pill-muted">
              <?php echo $row['total_enr']; ?> total
            </span>
          </td>

          <td style="color:var(--muted);">
            <?php echo date('M d, Y', strtotime($row['date_joined'])); ?>
          </td>

          <td>
            <a href="learners.php?delete=<?php echo $row['learner_id']; ?>" class="btn-del">
              <i class="fas fa-trash"></i> Remove
            </a>
          </td>
        </tr>
      <?php
        endwhile;
      else:
      ?>
        <tr class="empty-row">
          <td colspan="6">
            <i class="fas fa-users"></i> No learners registered yet.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../_footer.php'; ?>

</body>
</html>