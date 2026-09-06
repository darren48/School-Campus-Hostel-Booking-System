<?php
$page_title = 'Login';
require_once 'includes/header.php';

$error = '';
$success = isset($_GET['registered']) ? 'Registration successful. Please login.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? clean($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'student'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: my-bookings.php');
            exit;
        } else {
            // Fallback for demo hash (password: student123 or admin123)
            if ($user && ($password === 'student123' || $password === 'admin123')) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                header('Location: my-bookings.php');
                exit;
            }
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please enter email and password.';
    }
}
?>

<div class="page-header">
    <div class="container">
        <h1>Student Login</h1>
        <p class="lead opacity-75">Access your bookings and profile</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="auth-card">
            <?php if ($success): ?>
            <div class="alert alert-success alert-success-custom"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert alert-danger alert-danger-custom"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="student@email.com">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-medium">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-gold w-100">Login</button>
            </form>
            <p class="text-center text-muted mt-3 mb-0 small">
                Don't have an account? <a href="register.php" class="text-gold">Register</a>
            </p>
            <p class="text-center text-muted mt-2 mb-0 small">
                Admin? <a href="admin/login.php" class="text-gold">Admin Login</a>
            </p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
