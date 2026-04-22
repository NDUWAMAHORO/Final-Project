<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = 'Courses';
$msg = '';
$msg_type = 'success';

/* CREATE UPLOAD FOLDER IF NOT EXISTS */
$upload_dir = '../uploads/courses/';
if (!file_exists('../uploads')) {
    mkdir('../uploads', 0777, true);
}
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

/* ADD COURSE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_course') {

    $name  = trim($_POST['course_name']);
    $desc  = trim($_POST['description']);
    $cred  = intval($_POST['credits']);
    $price = floatval($_POST['price']);
    $image = '';

    if ($name == '' || $cred <= 0 || $price < 0) {
        $msg = 'Fill all required fields.';
        $msg_type = 'error';
    } else {

        if (!empty($_FILES['image']['name'])) {

            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = array('jpg','jpeg','png','webp','gif');

            if (!in_array($ext, $allowed)) {
                $msg = 'Invalid image type.';
                $msg_type = 'error';
            }
            elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $msg = 'Image must be under 2MB.';
                $msg_type = 'error';
            }
            else {
                $image = uniqid('course_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
            }
        }

        if ($msg_type != 'error') {

            $s = $conn->prepare("INSERT INTO course (course_name,description,credits,price,image) VALUES(?,?,?,?,?)");
            $s->bind_param("ssids", $name, $desc, $cred, $price, $image);

            if ($s->execute()) {
                $msg = 'Course added successfully.';
            } else {
                $msg = 'Failed: ' . $s->error;
                $msg_type = 'error';
            }

            $s->close();
        }
    }

    header("Location: courses.php?msg=" . urlencode($msg) . "&msg_type=" . $msg_type);
    exit;
}

/* EDIT COURSE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_course') {

    $id   = intval($_POST['course_id']);
    $name = trim($_POST['course_name']);
    $desc = trim($_POST['description']);
    $cred = intval($_POST['credits']);
    $price = floatval($_POST['price']);

    $old = isset($_POST['old_image']) ? $_POST['old_image'] : '';
    $image = $old;

    if (!empty($_FILES['image']['name'])) {

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg','jpeg','png','webp','gif');

        if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {

            $image = uniqid('course_') . '.' . $ext;

            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);

            if ($old != '' && file_exists($upload_dir . $old)) {
                unlink($upload_dir . $old);
            }
        }
    }

    $s = $conn->prepare("UPDATE course SET course_name=?,description=?,credits=?,price=?,image=? WHERE course_id=?");
    $s->bind_param("ssidsi", $name, $desc, $cred, $price, $image, $id);

    if ($s->execute()) {
        $msg = 'Course updated.';
    } else {
        $msg = 'Update failed.';
        $msg_type = 'error';
    }

    $s->close();

    header("Location: courses.php?msg=" . urlencode($msg) . "&msg_type=" . $msg_type);
    exit;
}

/* DELETE COURSE */
if (isset($_GET['delete']) && isset($_GET['confirmed'])) {

    $id = intval($_GET['delete']);

    $row = $conn->query("SELECT image FROM course WHERE course_id=$id")->fetch_assoc();

    if ($row && $row['image'] != '' && file_exists($upload_dir . $row['image'])) {
        unlink($upload_dir . $row['image']);
    }

    $s = $conn->prepare("DELETE FROM course WHERE course_id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $s->close();

    header("Location: courses.php?msg=" . urlencode('Course deleted.') . "&msg_type=success");
    exit;
}

if (isset($_GET['msg'])) $msg = urldecode($_GET['msg']);
if (isset($_GET['msg_type'])) $msg_type = $_GET['msg_type'];

$edit = null;

if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    $r = $conn->prepare("SELECT * FROM course WHERE course_id=?");
    $r->bind_param("i", $eid);
    $r->execute();
    $edit = $r->get_result()->fetch_assoc();
    $r->close();
}

$total = $conn->query("SELECT COUNT(*) AS c FROM course")->fetch_assoc();
$total = $total['c'];

include '../_nav.php';
?>
<!-- Display existing courses with Edit/Delete actions -->
<div class="card" style="margin-top: 40px;">
    <h3>Existing Courses</h3>
    <table class="tbl" width="100%" cellpadding="8" cellspacing="0" border="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Course Name</th>
                <th>Credits</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $courses = $conn->query("SELECT * FROM course ORDER BY course_id DESC");
            if ($courses && $courses->num_rows > 0):
                while ($row = $courses->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['course_id']; ?></td>
                <td>
                    <?php if (!empty($row['image']) && file_exists($upload_dir . $row['image'])): ?>
                        <img src="<?php echo $upload_dir . $row['image']; ?>" style="width: 50px; height: 40px; object-fit: cover; border-radius: 6px;">
                    <?php else: ?>
                        <span style="color: var(--muted);">No image</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                <td><?php echo $row['credits']; ?></td>
                <td>$<?php echo number_format($row['price'], 2); ?></td>
                <td class="act-cell">
                    <a href="courses.php?edit=<?php echo $row['course_id']; ?>" class="btn-edit">✏️ Edit</a>
                    <a href="courses.php?delete=<?php echo $row['course_id']; ?>&confirmed=1" 
                       class="btn-del" 
                       onclick="return confirm('Are you sure you want to delete this course? This will also delete all related enrollments and feedback.');">
                       🗑️ Delete
                    </a>
                </td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="6" style="text-align: center; color: var(--muted);">No courses found. Add your first course above.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- ✅ CSS LINK -->
<link rel="stylesheet" href="../style.css">

<!-- Font Awesome (icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<!-- ✅ EXTRA MODERN FORM STYLE -->
<style>
label {
    display:block;
    font-size:0.78rem;
    font-weight:700;
    color:#b8b8b8;
    margin-bottom:6px;
    letter-spacing:0.06em;
    text-transform:uppercase;
}

input[type="text"],
input[type="number"],
textarea,
select {
    width:100%;
    padding:12px 14px;
    font-size:0.92rem;
    color:#fff;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.08);
    border-radius:10px;
    outline:none;
    transition:0.25s;
}

input:focus,
textarea:focus,
select:focus {
    border-color:#f5a623;
    box-shadow:0 0 0 3px rgba(245,166,35,0.15);
}

textarea {
    min-height:110px;
    resize:vertical;
}

.form-group {
    margin-bottom:16px;
}
</style>

<?php if ($msg): ?>
<div class="alert alert-<?php echo ($msg_type == 'error' ? 'error' : 'success'); ?>">
    <?php echo htmlspecialchars($msg); ?>
</div>
<?php endif; ?>

<div class="page-title">Manage Courses</div>

<div class="card">
<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="action" value="<?php echo $edit ? 'edit_course' : 'add_course'; ?>">

<?php if ($edit): ?>
<input type="hidden" name="course_id" value="<?php echo $edit['course_id']; ?>">
<input type="hidden" name="old_image" value="<?php echo $edit['image']; ?>">
<?php endif; ?>

<div class="form-group">
<label>Course Name</label>
<input type="text" name="course_name" required value="<?php echo $edit ? $edit['course_name'] : ''; ?>">
</div>

<div class="form-group">
<label>Credits</label>
<input type="number" name="credits" required value="<?php echo $edit ? $edit['credits'] : ''; ?>">
</div>

<div class="form-group">
<label>Price</label>
<input type="number" step="0.01" name="price" required value="<?php echo $edit ? $edit['price'] : ''; ?>">
</div>

<div class="form-group">
<label>Description</label>
<textarea name="description"><?php echo $edit ? $edit['description'] : ''; ?></textarea>
</div>

<div class="form-group">
<label>Image</label>
<input type="file" name="image">
</div>

<button type="submit">
<?php echo $edit ? 'Update' : 'Add'; ?>
</button>

</form>
</div>

<?php include '../_footer.php'; ?>