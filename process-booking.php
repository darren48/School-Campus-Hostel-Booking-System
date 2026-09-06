<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: booking.php');
    exit;
}

$db = getDB();

$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
$move_in = isset($_POST['move_in']) ? clean($_POST['move_in']) : '';
$move_out = isset($_POST['move_out']) ? clean($_POST['move_out']) : '';
$number_of_students = isset($_POST['number_of_students']) ? (int)$_POST['number_of_students'] : 1;
$student_name = isset($_POST['student_name']) ? clean($_POST['student_name']) : '';
$student_email = isset($_POST['student_email']) ? clean($_POST['student_email']) : '';
$student_phone = isset($_POST['student_phone']) ? clean($_POST['student_phone']) : '';
$student_id_number = isset($_POST['student_id_number']) ? clean($_POST['student_id_number']) : '';
$special_requests = isset($_POST['special_requests']) ? clean($_POST['special_requests']) : '';

// Validation
if (!$room_id || !$move_in || !$move_out || !$student_name || !$student_email || !$student_phone) {
    header('Location: booking.php?error=' . urlencode('Please fill in all required fields.'));
    exit;
}

if (strtotime($move_out) <= strtotime($move_in)) {
    header('Location: booking.php?error=' . urlencode('Move-out date must be after move-in date.'));
    exit;
}

// Get room
$stmt = $db->prepare("SELECT * FROM rooms WHERE id = ? AND is_available = 1");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    header('Location: booking.php?error=' . urlencode('Selected room is not available.'));
    exit;
}

if ($number_of_students > $room['capacity']) {
    header('Location: booking.php?error=' . urlencode('Number of students exceeds room capacity (' . $room['capacity'] . ').'));
    exit;
}

// Check overlapping bookings (simple capacity check for shared rooms)
$stmt = $db->prepare("
    SELECT COALESCE(SUM(number_of_students), 0) as occupied
    FROM bookings
    WHERE room_id = ?
      AND status IN ('pending', 'confirmed')
      AND move_in < ?
      AND move_out > ?
");
$stmt->execute([$room_id, $move_out, $move_in]);
$occupied = (int)$stmt->fetchColumn();
$available = $room['capacity'] - $occupied;

if ($number_of_students > $available) {
    header('Location: booking.php?error=' . urlencode("Only {$available} bed(s) available for the selected dates."));
    exit;
}

// Calculate price
$months = calculateMonths($move_in, $move_out);
$total_price = $room['price_per_month'] * $months * $number_of_students;

$booking_ref = generateBookingRef();
$user_id = isLoggedIn() ? (int)$_SESSION['user_id'] : null;

try {
    $stmt = $db->prepare("
        INSERT INTO bookings 
        (booking_ref, room_id, user_id, student_name, student_email, student_phone, student_id_number, 
         move_in, move_out, number_of_students, total_price, special_requests, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $booking_ref,
        $room_id,
        $user_id,
        $student_name,
        $student_email,
        $student_phone,
        $student_id_number,
        $move_in,
        $move_out,
        $number_of_students,
        $total_price,
        $special_requests
    ]);

    header('Location: booking.php?success=1&ref=' . urlencode($booking_ref));
    exit;
} catch (PDOException $e) {
    header('Location: booking.php?error=' . urlencode('Booking failed. Please try again.'));
    exit;
}
?>
