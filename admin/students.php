<?php
session_start();
require_once '../config/database.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$message = '';

// Delete student
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM users WHERE id = ? AND role = 'student'")->execute([$id]);
    $message = 'Student removed.';
}

$students = $db->query("SELECT * FROM users WHERE role = 'student' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | Grand TAR ABC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 admin-sidebar px-0">
                <div class="text-center py-4">
                    <i class="fas fa-building fa-2x text-warning"></i>
                    <h5 class="text-white mt-2 mb-0" style="font-family:'Playfair Display',serif;">Grand TAR ABC</h5>
                    <small class="text-white-50">Admin Panel</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    <a class="nav-link" href="bookings.php"><i class="fas fa-calendar-check me-2"></i> Bookings</a>
                    <a class="nav-link" href="rooms.php"><i class="fas fa-bed me-2"></i> Rooms</a>
                    <a class="nav-link active" href="students.php"><i class="fas fa-user-graduate me-2"></i> Students</a>
                    <a class="nav-link" href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i> View Site</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-9 col-lg-10 bg-light min-vh-100">
                <div class="p-4">
                    <h3 class="mb-4" style="font-family:'Playfair Display',serif;">Manage Students</h3>

                    <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Student ID</th>
                                        <th>Registered</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($students)): ?>
                                    <tr><td colspan="8" class="text-center text-muted py-4">No students registered yet.</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td><?php echo $s['id']; ?></td>
                                        <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($s['username']); ?></td>
                                        <td><?php echo htmlspecialchars($s['email']); ?></td>
                                        <td><?php echo htmlspecialchars($s['phone'] ?? '—'); ?></td>
                                        <td><?php echo htmlspecialchars($s['student_id'] ?? '—'); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($s['created_at'])); ?></td>
                                        <td>
                                            <a href="students.php?delete=<?php echo $s['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Remove this student account?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
