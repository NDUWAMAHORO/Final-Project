<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = "Contact Messages";
$msg = "";
$msg_type = "success";

/* DELETE MESSAGE */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $s = $conn->prepare("DELETE FROM contact_messages WHERE id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $s->close();

    header("Location: messages.php?msg=" . urlencode("Message deleted") . "&msg_type=success");
    exit;
}

/* LOAD MESSAGES */
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");

include '../_nav.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?php echo $msg_type; ?>">
    <?php echo htmlspecialchars($msg); ?>
</div>
<?php endif; ?>

<div class="page-title">
    <i class="fas fa-envelope"></i> Contact Messages
</div>

<div class="card">
    <div class="card-head">
        <h3><i class="fas fa-inbox"></i> All Messages</h3>
    </div>

    <table class="tbl">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td style="color:var(--muted);">
                        <?php echo $row['id']; ?>
                    </td>

                    <td>
                        <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                    </td>

                    <td style="color:var(--muted);">
                        <?php echo htmlspecialchars($row['email']); ?>
                    </td>

                    <td style="max-width:300px;color:var(--muted);">
                        <?php echo htmlspecialchars($row['message']); ?>
                    </td>

                    <td style="color:var(--muted);font-size:0.8rem;">
                        <?php echo $row['created_at']; ?>
                    </td>

                    <td>
                        <a class="btn-del"
                           href="messages.php?delete=<?php echo $row['id']; ?>"
                           onclick="return confirm('Delete this message?')">
                           <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr class="empty-row">
                <td colspan="6">
                    <i class="fas fa-envelope-open"></i> No messages yet
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../_footer.php'; ?>