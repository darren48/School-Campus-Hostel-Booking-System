<?php
$page_title = 'My Bookings';
require_once 'includes/header.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$db = getDB();
$user_id = (int)$_SESSION['user_id'];
$user_email = $_SESSION['user_email'] ?? '';

// Cancel booking
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $bid = (int)$_GET['cancel'];
    $stmt = $db->prepare("
        UPDATE bookings SET status = 'cancelled' 
        WHERE id = ? AND (user_id = ? OR student_email = ?) AND status = 'pending'
    ");
    $stmt->execute([$bid, $user_id, $user_email]);
    header('Location: my-bookings.php?msg=cancelled');
    exit;
}

$stmt = $db->prepare("
    SELECT b.*, r.name as room_name, r.room_number 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = ? OR b.student_email = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$user_id, $user_email]);
$bookings = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>My Bookings</h1>
        <p class="lead opacity-75">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'cancelled'): ?>
        <div class="alert alert-info">Booking has been cancelled.</div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <p class="text-muted">You have no bookings yet.</p>
            <a href="booking.php" class="btn btn-gold mt-2">Book Accommodation</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white shadow-sm rounded">
                <thead class="table-dark">
                    <tr>
                        <th>Ref</th>
                        <th>Room</th>
                        <th>Move-in</th>
                        <th>Move-out</th>
                        <th>Students</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($b['booking_ref']); ?></strong></td>
                        <td><?php echo htmlspecialchars($b['room_name']); ?> <small class="text-muted">(<?php echo htmlspecialchars($b['room_number']); ?>)</small></td>
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
                            <a href="my-bookings.php?cancel=<?php echo $b['id']; ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Cancel this booking?');">Cancel</a>
                            <?php else: ?>
                            —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
