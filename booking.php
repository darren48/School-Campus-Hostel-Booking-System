<?php
$page_title = 'Book Accommodation';
require_once 'includes/header.php';

$db = getDB();

// Get all available rooms
$stmt = $db->query("SELECT * FROM rooms WHERE is_available = 1 ORDER BY price_per_month ASC");
$rooms = $stmt->fetchAll();

// Pre-fill from GET params
$prefill_room = isset($_GET['room']) ? clean($_GET['room']) : '';
$prefill_movein = isset($_GET['move_in']) ? clean($_GET['move_in']) : '';
$prefill_moveout = isset($_GET['move_out']) ? clean($_GET['move_out']) : '';
$prefill_students = isset($_GET['students']) ? (int)$_GET['students'] : 1;
$prefill_type = isset($_GET['room_type']) ? clean($_GET['room_type']) : '';

$success = isset($_GET['success']) ? true : false;
$error = isset($_GET['error']) ? clean($_GET['error']) : '';
$booking_ref = isset($_GET['ref']) ? clean($_GET['ref']) : '';

// Pre-select room by slug or type
$selected_room_id = '';
if ($prefill_room) {
    foreach ($rooms as $r) {
        if ($r['slug'] === $prefill_room) {
            $selected_room_id = $r['id'];
            break;
        }
    }
}
?>

<div class="page-header">
    <div class="container">
        <h1>Book Accommodation</h1>
        <p class="lead opacity-75">Reserve your hostel room in a few simple steps</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <?php if ($success && $booking_ref): ?>
                <div class="alert alert-success alert-dismissible fade show alert-success-custom" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Booking Submitted!</h5>
                    <p class="mb-1">Your booking has been received. Booking reference: <strong><?php echo htmlspecialchars($booking_ref); ?></strong></p>
                    <p class="mb-0 small">Our hostel office will review and confirm your booking. You can check status under My Bookings after login.</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show alert-danger-custom" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="booking-form-card">
                    <h3 class="font-serif mb-4">Accommodation Booking Form</h3>
                    
                    <form action="process-booking.php" method="POST" id="bookingForm">
                        <!-- Room Selection -->
                        <div class="mb-4">
                            <label for="room_id" class="form-label fw-medium">Select Room *</label>
                            <select name="room_id" id="room_id" class="form-select" required>
                                <option value="">-- Choose accommodation --</option>
                                <?php foreach ($rooms as $room): ?>
                                <option value="<?php echo $room['id']; ?>" 
                                        data-price="<?php echo $room['price_per_month']; ?>"
                                        data-capacity="<?php echo $room['capacity']; ?>"
                                        <?php 
                                        $sel = ($selected_room_id == $room['id']);
                                        if (!$sel && $prefill_type && $room['room_type'] === $prefill_type && !$selected_room_id) {
                                            $sel = true;
                                            $selected_room_id = $room['id'];
                                        }
                                        echo $sel ? 'selected' : '';
                                        ?>>
                                    <?php echo htmlspecialchars($room['name']); ?> (<?php echo htmlspecialchars($room['room_number']); ?>) — RM <?php echo number_format($room['price_per_month'], 0); ?>/month
                                    · Max <?php echo $room['capacity']; ?> students
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dates -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="move_in" class="form-label fw-medium">Move-in Date *</label>
                                <input type="date" name="move_in" id="move_in" class="form-control" 
                                       value="<?php echo htmlspecialchars($prefill_movein); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="move_out" class="form-label fw-medium">Move-out Date *</label>
                                <input type="date" name="move_out" id="move_out" class="form-control" 
                                       value="<?php echo htmlspecialchars($prefill_moveout); ?>" required>
                            </div>
                        </div>

                        <!-- Number of students -->
                        <div class="mb-4">
                            <label for="number_of_students" class="form-label fw-medium">Number of Students *</label>
                            <select name="number_of_students" id="number_of_students" class="form-select" required>
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $prefill_students == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="form-text">Must not exceed room capacity.</div>
                        </div>

                        <hr class="my-4">
                        <h5 class="font-serif mb-3">Student Details</h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="student_name" class="form-label fw-medium">Full Name *</label>
                                <input type="text" name="student_name" id="student_name" class="form-control" required
                                       value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['user_name'] ?? '') : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="student_id_number" class="form-label fw-medium">Student ID</label>
                                <input type="text" name="student_id_number" id="student_id_number" class="form-control" placeholder="e.g. TAR2024001">
                            </div>
                            <div class="col-md-6">
                                <label for="student_email" class="form-label fw-medium">Email *</label>
                                <input type="email" name="student_email" id="student_email" class="form-control" required
                                       value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['user_email'] ?? '') : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="student_phone" class="form-label fw-medium">Phone *</label>
                                <input type="tel" name="student_phone" id="student_phone" class="form-control" required placeholder="01X-XXXXXXX">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="special_requests" class="form-label fw-medium">Special Requests</label>
                            <textarea name="special_requests" id="special_requests" class="form-control" rows="3" placeholder="Any preference or request..."></textarea>
                        </div>

                        <!-- Estimated price -->
                        <div class="bg-cream rounded-3 p-3 mb-4 d-flex justify-content-between align-items-center">
                            <span class="fw-medium">Estimated Total</span>
                            <span class="price" id="estimated_price" style="font-size: 1.4rem;">RM 0</span>
                        </div>

                        <button type="submit" class="btn btn-gold btn-lg w-100">
                            <i class="fas fa-paper-plane me-2"></i>Submit Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
