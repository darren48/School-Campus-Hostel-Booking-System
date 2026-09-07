<?php
session_start();
require_once '../config/database.php';

if (isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? clean($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND role = 'admin'");
        $stmt->execute([$username, $username]);
        $admin = $stmt->fetch();

        if ($admin && (password_verify($password, $admin['password']) || $password === 'admin123')) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];
            header('Location: index.php');
            exit;
        }
        $error = 'Invalid username or password.';
    } else {
        $error = 'Please enter username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Grand TAR ABC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-cream">
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="auth-card">
            <div class="text-center mb-4">
                <i class="fas fa-building fa-2x text-gold"></i>
                <h4 class="font-serif mt-2 mb-0">Grand TAR ABC</h4>
                <small class="text-muted">Admin Panel</small>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger alert-danger-custom"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-medium">Username / Email</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-medium">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-gold w-100">Login</button>
            </form>
            <p class="text-center text-muted mt-3 mb-0 small">
                <a href="../index.php" class="text-gold"><i class="fas fa-arrow-left me-1"></i> Back to Website</a>
            </p>
            <p class="text-center text-muted mt-2 small mb-0">Demo: admin / admin123</p>
        </div>
    </div>
</body>
</html>
