<?php
session_start();
require_once '../config/database.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$db = getDB();

// Update status
if (isset($_GET['action'], $_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    $allowed = ['confirm' => 'confirmed', 'cancel' => 'cancelled', 'complete' => 'completed'];
    if (isset($allowed[$action])) {
        $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$allowed[$action], $id]);
    }
    header('Location: bookings.php');
    exit;
}

$filter = isset($_GET['status']) ? clean($_GET['status']) : '';
$sql = "
    SELECT b.*, r.name as room_name, r.room_number 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
";
$params = [];
if ($filter && in_array($filter, ['pending', 'confirmed', 'cancelled', 'completed'])) {
    $sql .= " WHERE b.status = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY b.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings | Grand TAR ABC Admin</title>
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
                    <a class="nav-link active" href="bookings.php"><i class="fas fa-calendar-check me-2"></i> Bookings</a>
                    <a class="nav-link" href="rooms.php"><i class="fas fa-bed me-2"></i> Rooms</a>
                    <a class="nav-link" href="students.php"><i class="fas fa-user-graduate me-2"></i> Students</a>
                    <a class="nav-link" href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i> View Site</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-9 col-lg-10 bg-light min-vh-100">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h3 class="mb-0" style="font-family:'Playfair Display',serif;">Manage Bookings</h3>
                        <div class="btn-group">
                            <a href="bookings.php" class="btn btn-sm <?php echo $filter === '' ? 'btn-gold' : 'btn-outline-secondary'; ?>">All</a>
                            <a href="bookings.php?status=pending" class="btn btn-sm <?php echo $filter === 'pending' ? 'btn-gold' : 'btn-outline-secondary'; ?>">Pending</a>
                            <a href="bookings.php?status=confirmed" class="btn btn-sm <?php echo $filter === 'confirmed' ? 'btn-gold' : 'btn-outline-secondary'; ?>">Confirmed</a>
                            <a href="bookings.php?status=cancelled" class="btn btn-sm <?php echo $filter === 'cancelled' ? 'btn-gold' : 'btn-outline-secondary'; ?>">Cancelled</a>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref</th>
                                        <th>Student</th>
                                        <th>Contact</th>
                                        <th>Room</th>
                                        <th>Move-in</th>
                                        <th>Move-out</th>
                                        <th>Pax</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($bookings)): ?>
                                    <tr><td colspan="10" class="text-center text-muted py-4">No bookings found.</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($bookings as $b): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($b['booking_ref']); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($b['student_name']); ?>
                                            <?php if ($b['student_id_number']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($b['student_id_number']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($b['student_email']); ?></small><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($b['student_phone'] ?? ''); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($b['room_name']); ?><br><small class="text-muted"><?php echo htmlspecialchars($b['room_number']); ?></small></td>
                                        <td><?php echo date('d/m/Y', strtotime($b['move_in'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($b['move_out'])); ?></td>
                                        <td><?php echo (int)$b['number_of_students']; ?></td>
                                        <td>RM <?php echo number_format($b['total_price'], 0); ?></td>
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
                                        <td>
                                            <?php if ($b['status'] === 'pending'): ?>
                                            <a href="bookings.php?action=confirm&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-success" title="Confirm">Confirm</a>
                                            <a href="bookings.php?action=cancel&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-danger" title="Cancel" onclick="return confirm('Cancel this booking?');">Cancel</a>
                                            <?php elseif ($b['status'] === 'confirmed'): ?>
                                            <a href="bookings.php?action=complete&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-secondary" title="Complete">Complete</a>
                                            <a href="bookings.php?action=cancel&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this booking?');">Cancel</a>
                                            <?php else: ?>
                                            —
                                            <?php endif; ?>
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
