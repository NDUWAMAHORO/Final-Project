<?php
session_start();
require_once '../connection.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$page_title = 'Teachers';
$msg = '';
$msg_type = 'success';

$alert_types = array('error' => 'alert-error', 'success' => 'alert-success');

/* ADD / EDIT TEACHER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $fn = trim($_POST['full_name']);
    $em = trim($_POST['email']);
    $ph = trim($_POST['phone_number']);
    $cid = intval($_POST['course_id']);

    if ($_POST['action'] === 'add_teacher') {
        if ($fn === '' || $em === '' || $cid <= 0) {
            $msg = 'Name, email and course required.';
            $msg_type = 'error';
        } else {
            $s = $conn->prepare("INSERT INTO teacher (full_name,email,phone_number,course_id) VALUES(?,?,?,?)");
            $s->bind_param("sssi", $fn, $em, $ph, $cid);
            if ($s->execute()) {
                $msg = 'Teacher added.';
                $msg_type = 'success';
            } else {
                $msg = 'Failed: ' . $s->error;
                $msg_type = 'error';
            }
            $s->close();
        }
    }

    if ($_POST['action'] === 'edit_teacher') {
        $id = intval($_POST['teacher_id']);
        $s = $conn->prepare("UPDATE teacher SET full_name=?,email=?,phone_number=?,course_id=? WHERE teacher_id=?");
        $s->bind_param("sssii", $fn, $em, $ph, $cid, $id);
        if ($s->execute()) {
            $msg = 'Teacher updated.';
            $msg_type = 'success';
        } else {
            $msg = 'Update failed.';
            $msg_type = 'error';
        }
        $s->close();
    }

    header("Location: teachers.php?msg=" . urlencode($msg) . "&msg_type=" . $msg_type);
    exit;
}

/* DELETE */
if (isset($_GET['delete']) && isset($_GET['confirmed'])) {
    $id = intval($_GET['delete']);
    $s = $conn->prepare("DELETE FROM teacher WHERE teacher_id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $s->close();
    header("Location: teachers.php?msg=Teacher deleted.&msg_type=success");
    exit;
}

if (isset($_GET['msg'])) $msg = urldecode($_GET['msg']);
if (isset($_GET['msg_type'])) $msg_type = $_GET['msg_type'];

/* EDIT LOAD */
$edit = null;
if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    $r = $conn->prepare("SELECT * FROM teacher WHERE teacher_id=?");
    $r->bind_param("i", $eid);
    $r->execute();
    $edit = $r->get_result()->fetch_assoc();
    $r->close();
}

/* CONFIRM DELETE */
$confirm_id = null;
$confirm_name = '';
if (isset($_GET['delete']) && !isset($_GET['confirmed'])) {
    $confirm_id = intval($_GET['delete']);
    $row = $conn->query("SELECT full_name FROM teacher WHERE teacher_id=$confirm_id")->fetch_assoc();
    $confirm_name = $row ? $row['full_name'] : 'this teacher';
}

/* DATA */
$courses = $conn->query("SELECT course_id, course_name FROM course ORDER BY course_name ASC");
$total_result = $conn->query("SELECT COUNT(*) AS c FROM teacher");
$total_row = $total_result->fetch_assoc();
$total = $total_row['c'];

include '../_nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-group label{ display:block; font-size:12px; font-weight:700; margin-bottom:6px; color:#aaa; text-transform:uppercase; }
        .form-group input, .form-group select{ width:100%; padding:10px; border:1px solid #333; border-radius:8px; background:#111; color:#fff; margin-bottom:12px; }
    </style>
    <!-- ✅ CSS LINK -->
<link rel="stylesheet" href="../style.css">

<!-- Font Awesome (icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<?php if($msg): ?>
<div class="alert <?php echo isset($alert_types[$msg_type]) ? $alert_types[$msg_type] : 'alert-success'; ?>">
    <?php echo htmlspecialchars($msg); ?>
</div>
<?php endif; ?>

<?php if($confirm_id): ?>
<div class="confirm-box">
    <p>Delete teacher <strong><?php echo htmlspecialchars($confirm_name); ?></strong>?</p>
    <a href="teachers.php?delete=<?php echo $confirm_id; ?>&confirmed=1" class="btn-del">Yes Delete</a>
    <a href="teachers.php" class="btn btn-outline">Cancel</a>
</div>
<?php endif; ?>

<div class="page-title">Manage Teachers</div>

<!-- ADD / EDIT FORM -->
<div class="card">
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit ? 'edit_teacher' : 'add_teacher'; ?>">
        <?php if($edit): ?>
            <input type="hidden" name="teacher_id" value="<?php echo $edit['teacher_id']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required value="<?php echo $edit ? htmlspecialchars($edit['full_name']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?php echo $edit ? htmlspecialchars($edit['email']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone_number" value="<?php echo $edit ? htmlspecialchars($edit['phone_number']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Course</label>
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php while($c = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $c['course_id']; ?>"
                        <?php echo ($edit && $edit['course_id'] == $c['course_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['course_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit"><?php echo $edit ? 'Update Teacher' : 'Add Teacher'; ?></button>
        <?php if($edit): ?>
            <a href="teachers.php" class="btn btn-outline">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<!-- LIST OF TEACHERS -->
<div class="card">
    <table class="tbl">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Course</th><th>Action</th></tr></thead>
        <tbody>
        <?php
        $teachers = $conn->query("SELECT t.*, c.course_name FROM teacher t LEFT JOIN course c ON t.course_id=c.course_id ORDER BY t.teacher_id DESC");
        while($row = $teachers->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo $row['teacher_id']; ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
            <td>
                <a href="teachers.php?edit=<?php echo $row['teacher_id']; ?>">Edit</a>
                <a href="teachers.php?delete=<?php echo $row['teacher_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../_footer.php'; ?>
</body>
</html>