<?php
session_start();
require_once '../config/database.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$db = getDB();

// Stats
$total_students = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$total_rooms = $db->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$total_bookings = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$confirmed = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();

// Occupied beds approximation: sum of confirmed/pending current bookings
$occupied_beds = $db->query("
    SELECT COALESCE(SUM(number_of_students), 0) FROM bookings 
    WHERE status IN ('pending', 'confirmed') 
    AND move_in <= CURDATE() AND move_out >= CURDATE()
")->fetchColumn();

$total_capacity = $db->query("SELECT COALESCE(SUM(capacity), 0) FROM rooms WHERE is_available = 1")->fetchColumn();
$available_beds = max(0, $total_capacity - $occupied_beds);

// Recent bookings
$recent = $db->query("
    SELECT b.*, r.name as room_name, r.room_number 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    ORDER BY b.created_at DESC 
    LIMIT 8
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Grand TAR ABC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar px-0">
                <div class="text-center py-4">
                    <i class="fas fa-building fa-2x text-warning"></i>
                    <h5 class="text-white mt-2 mb-0" style="font-family:'Playfair Display',serif;">Grand TAR ABC</h5>
                    <small class="text-white-50">Admin Panel</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    <a class="nav-link" href="bookings.php"><i class="fas fa-calendar-check me-2"></i> Bookings</a>
                    <a class="nav-link" href="rooms.php"><i class="fas fa-bed me-2"></i> Rooms</a>
                    <a class="nav-link" href="students.php"><i class="fas fa-user-graduate me-2"></i> Students</a>
                    <a class="nav-link" href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i> View Site</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </nav>
            </div>

            <!-- Main -->
            <div class="col-md-9 col-lg-10 bg-light min-vh-100">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0" style="font-family:'Playfair Display',serif;">Dashboard</h3>
                        <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    </div>

                    <!-- Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4 col-lg-2">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-user-graduate text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?php echo $total_students; ?></h4>
                                        <small class="text-muted">Students</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-door-open text-info"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?php echo $total_rooms; ?></h4>
                                        <small class="text-muted">Rooms</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-bed text-success"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?php echo $available_beds; ?></h4>
                                        <small class="text-muted">Beds Free</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-calendar-check text-secondary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?php echo $total_bookings; ?></h4>
                                        <small class="text-muted">Bookings</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?php echo $pending; ?></h4>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?php echo $confirmed; ?></h4>
                                        <small class="text-muted">Confirmed</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 font-serif">Recent Bookings</h5>
                            <a href="bookings.php" class="btn btn-sm btn-outline-gold">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ref</th>
                                            <th>Student</th>
                                            <th>Room</th>
                                            <th>Move-in</th>
                                            <th>Move-out</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recent)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-4">No bookings yet.</td></tr>
                                        <?php else: ?>
                                        <?php foreach ($recent as $b): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($b['booking_ref']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($b['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($b['room_name']); ?> (<?php echo htmlspecialchars($b['room_number']); ?>)</td>
                                            <td><?php echo date('d/m/Y', strtotime($b['move_in'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($b['move_out'])); ?></td>
                                            <td>
                                                <?php
                                                $badge = match($b['status']) {
                                                    'confirmed' => 'success',
                                                    'pending' => 'warning',
                                                    'cancelled' => 'danger',
                                                    'completed' => 'secondary',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($b['status']); ?></span>
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
    </div>
</body>
</html>
