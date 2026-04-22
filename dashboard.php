<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit;
}

$tid = intval($_SESSION['teacher_id']);
$teacher = $conn->query("SELECT * FROM teacher WHERE teacher_id=$tid")->fetch_assoc();

$sql = "
SELECT e.enrollment_id, e.status, l.full_name, l.email, c.course_name
FROM enrollment e
JOIN learner l ON e.learner_id = l.learner_id
JOIN course c ON e.course_id = c.course_id
WHERE e.teacher_id = $tid
ORDER BY e.enrollment_id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head><title>Teacher Dashboard</title></head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($teacher['full_name']); ?></h2>
<h3>Your Learners</h3>
<table border="1" cellpadding="8">
<tr><th>Learner</th><th>Email</th><th>Course</th><th>Status</th><th>Action</th></tr>
<?php if ($result && $result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['full_name']); ?></td>
      <td><?php echo htmlspecialchars($row['email']); ?></td>
      <td><?php echo htmlspecialchars($row['course_name']); ?></td>
      <td><?php echo ucfirst($row['status']); ?></td>
      <td>
        <?php if ($row['status'] == 'pending'): ?>
          <a href="teacher_dashboard.php?approve=<?php echo $row['enrollment_id']; ?>">Approve</a> |
          <a href="teacher_dashboard.php?reject=<?php echo $row['enrollment_id']; ?>">Reject</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr><td colspan="5">No learners enrolled yet.</td></tr>
<?php endif; ?>
</table>
</body>
</html>
