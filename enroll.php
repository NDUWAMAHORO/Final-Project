<?php
require_once '../connection.php';

if (!isset($_SESSION['learner_id'])) {
    $_SESSION['redirect_after_login'] = 'enroll.php?course_id=' . intval($_GET['course_id'] ?? 0);
    header("Location: login.php"); exit;
}

$learner_id = intval($_SESSION['learner_id']);
$course_id  = intval($_GET['course_id'] ?? 0);

if ($course_id <= 0) { header("Location: ../index.php"); exit; }

// Verify course exists
$cs = $conn->prepare("SELECT course_id FROM course WHERE course_id = ?");
$cs->bind_param("i", $course_id); $cs->execute(); $cs->store_result();
if ($cs->num_rows === 0) { header("Location: ../index.php"); exit; }
$cs->close();

// Check already enrolled
$ce = $conn->prepare("SELECT enrollment_id FROM enrollment WHERE learner_id = ? AND course_id = ?");
$ce->bind_param("ii", $learner_id, $course_id); $ce->execute(); $ce->store_result();
if ($ce->num_rows > 0) { header("Location: ../index.php?msg=already"); exit; }
$ce->close();

// Insert pending enrollment
$ins = $conn->prepare("INSERT INTO enrollment (learner_id, course_id, status) VALUES (?, ?, 'pending')");
$ins->bind_param("ii", $learner_id, $course_id);
$ins->execute(); $ins->close();

header("Location: ../index.php?msg=enrolled");
exit;
?>
