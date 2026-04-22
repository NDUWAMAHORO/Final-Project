<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require_once '../connection.php';

// Delete feedback
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM feedback WHERE feedback_id = $id");
    header("Location: feedback.php?msg=deleted");
    exit;
}

// Load feedback with learner and course names
$feedback = $conn->query("
    SELECT f.feedback_id, f.rating, f.comment, f.created_at,
           l.full_name AS learner_name,
           c.course_name
    FROM feedback f
    JOIN learner l ON f.learner_id = l.learner_id
    JOIN course c ON f.course_id = c.course_id
    ORDER BY f.created_at DESC
");

include '../_nav.php';
?>

<div class="page-title">
    <i class="fas fa-star"></i> Learner Feedback
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">Feedback deleted successfully.</div>
<?php endif; ?>

<div class="card">
    <div class="card-head">
        <h3><i class="fas fa-comment"></i> All Course Reviews</h3>
    </div>
    <table class="tbl">
        <thead>
            <tr>
                <th>ID</th>
                <th>Learner</th>
                <th>Course</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($feedback && $feedback->num_rows > 0): ?>
            <?php while ($row = $feedback->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['feedback_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['learner_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                    <td style="color: var(--accent);">
                        <?php echo str_repeat('★', $row['rating']); ?>
                        (<?php echo $row['rating']; ?>)
                    </td>
                    <td style="max-width: 300px;"><?php echo nl2br(htmlspecialchars($row['comment'])); ?></td>
                    <td style="font-size: 0.8rem;"><?php echo $row['created_at']; ?></td>
                    <td class="act-cell">
                    <a href="feedback.php?delete=<?php echo $row['feedback_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr class="empty-row">
                <td colspan="7">
                    <i class="fas fa-comment-slash"></i> No feedback yet.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
     </table>
</div>

<?php include '../_footer.php'; ?>