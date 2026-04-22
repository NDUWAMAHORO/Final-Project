<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['learner_id'])) {
    header("Location: login.php");
    exit;
}

$learner_id = $_SESSION['learner_id'];
$success = '';
$error = '';

// Get courses where learner has APPROVED enrollment
$courses_query = "
    SELECT c.course_id, c.course_name
    FROM enrollment e
    JOIN course c ON e.course_id = c.course_id
    WHERE e.learner_id = ? AND e.status = 'approved'
";
$stmt = $conn->prepare($courses_query);
$stmt->bind_param("i", $learner_id);
$stmt->execute();
$courses = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($course_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
        $error = "Please fill all fields correctly.";
    } else {
        $check = $conn->prepare("SELECT feedback_id FROM feedback WHERE learner_id = ? AND course_id = ?");
        $check->bind_param("ii", $learner_id, $course_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "You have already submitted feedback for this course.";
        } else {
            $insert = $conn->prepare("INSERT INTO feedback (learner_id, course_id, rating, comment) VALUES (?, ?, ?, ?)");
            $insert->bind_param("iiis", $learner_id, $course_id, $rating, $comment);
            if ($insert->execute()) {
                $success = "Thank you! Your feedback has been submitted.";
            } else {
                $error = "Submission failed. Please try again.";
            }
            $insert->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leave Feedback – eSmart</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .feedback-form {
            max-width: 600px;
            margin: 100px auto;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 32px;
        }
        .feedback-form h2 {
            font-family: var(--font-head);
            font-size: 1.6rem;
            margin-bottom: 24px;
            color: var(--text);
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
            letter-spacing: 0.05em;
        }
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 0.9rem;
            font-family: inherit;
        }
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--accent);
            outline: none;
        }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 8px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 28px;
            color: var(--muted);
            cursor: pointer;
            transition: 0.2s;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: var(--accent);
        }
        button[type="submit"] {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
            padding: 12px 28px;
            border-radius: 99px;
            font-weight: 700;
            font-size: 0.9rem;
            color: #0b0c0f;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
        }
        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245,166,35,0.3);
        }
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }
        .alert-success {
            background: rgba(80,200,120,0.1);
            border: 1px solid rgba(80,200,120,0.3);
            color: #50c878;
        }
        .alert-error {
            background: rgba(255,80,80,0.1);
            border: 1px solid rgba(255,80,80,0.3);
            color: #ff8080;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--accent);
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
    <!-- ✅ CSS LINK -->
<link rel="stylesheet" href="../style.css">

<!-- Font Awesome (icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header class="header">
    <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
        <svg width="32" height="32" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;">
            <rect width="36" height="36" rx="8" fill="#1f1f1f" stroke="#f59e0b" stroke-width="1.5"/>
            <rect x="8" y="10" width="13" height="4" rx="2" fill="#f59e0b"/>
            <rect x="8" y="16" width="9" height="4" rx="2" fill="#f59e0b" opacity="0.55"/>
            <rect x="8" y="22" width="13" height="4" rx="2" fill="#f59e0b"/>
            <circle cx="25" cy="18" r="4" fill="#f59e0b" opacity="0.9"/>
        </svg>
        <span>e<span style="color: var(--accent);">Smart</span></span>
    </a>
    <nav><a href="logout.php" class="btn btn-outline">Logout</a></nav>
</header>
<div class="feedback-form">
    <h2>Leave a Course Review</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="POST">
<div class="form-group">
    <label>Your Rating</label>
    <select name="rating" required>
        <option value="">-- Select rating --</option>
        <option value="5">5 Excellent</option>
        <option value="4">4 Very Good</option>
        <option value="3">3 Good</option>
        <option value="2">2 Fair</option>
        <option value="1">1 Poor</option>
    </select>
</div>
        <div class="form-group">
            <label>Select Course</label>
            <select name="course_id" required>
                <option value="">-- Choose a course you are enrolled in --</option>
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $course['course_id']; ?>">
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <?php if ($courses->num_rows == 0): ?>
                <p style="color: var(--accent); margin-top: 8px;">You have no approved enrollments yet. Once approved, you can leave feedback.</p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Your Comment</label>
            <textarea name="comment" rows="4" placeholder="Share your experience..." required></textarea>
        </div>
        <button type="submit">Submit Feedback</button>
    </form>
    <a href="index.php" class="back-link">Go Back to Home</a>
</div>
</body>
</html>