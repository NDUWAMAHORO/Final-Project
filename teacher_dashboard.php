if (isset($_GET['approve'])) {
    $eid = intval($_GET['approve']);
    $conn->query("UPDATE enrollment SET status='approved' WHERE enrollment_id=$eid AND teacher_id=$tid");
    header("Location: teacher_dashboard.php");
    exit;
}
if (isset($_GET['reject'])) {
    $eid = intval($_GET['reject']);
    $conn->query("UPDATE enrollment SET status='rejected' WHERE enrollment_id=$eid AND teacher_id=$tid");
    header("Location: teacher_dashboard.php");
    exit;
}
