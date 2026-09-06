<?php
$page_title = 'Register';
require_once 'includes/header.php';

$error = '';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['full_name']) ? clean($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? clean($_POST['email']) : '';
    $username = isset($_POST['username']) ? clean($_POST['username']) : '';
    $phone = isset($_POST['phone']) ? clean($_POST['phone']) : '';
    $student_id = isset($_POST['student_id']) ? clean($_POST['student_id']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (!$full_name || !$email || !$username || !$password) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("
                INSERT INTO users (username, email, password, full_name, phone, student_id, role)
                VALUES (?, ?, ?, ?, ?, ?, 'student')
            ");
            $stmt->execute([$username, $email, $hash, $full_name, $phone, $student_id]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Email or username already exists.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<div class="page-header">
    <div class="container">
        <h1>Student Registration</h1>
        <p class="lead opacity-75">Create an account to manage your bookings</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="auth-card" style="max-width: 520px;">
            <?php if ($error): ?>
            <div class="alert alert-danger alert-danger-custom"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-medium">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Username *</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Student ID</label>
                        <input type="text" name="student_id" class="form-control" placeholder="TAR2024xxx">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Phone</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Confirm Password *</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-gold w-100">Register</button>
            </form>
            <p class="text-center text-muted mt-3 mb-0 small">
                Already have an account? <a href="login.php" class="text-gold">Login</a>
            </p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
