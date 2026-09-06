<?php
require_once 'config/database.php';
$db = getDB();

$slug = isset($_GET['slug']) ? clean($_GET['slug']) : '';
if (!$slug) {
    header('Location: rooms.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM rooms WHERE slug = ? AND is_available = 1");
$stmt->execute([$slug]);
$room = $stmt->fetch();

if (!$room) {
    header('Location: rooms.php');
    exit;
}

$page_title = $room['name'];
require_once 'includes/header.php';

$facilities_list = array_filter(array_map('trim', explode(',', $room['facilities'] ?? '')));
?>

<div class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($room['name']); ?></h1>
        <p class="lead opacity-75">Room <?php echo htmlspecialchars($room['room_number']); ?> · <?php echo (int)$room['capacity']; ?> Students</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="img-fluid rounded-3 shadow w-100" style="max-height: 420px; object-fit: cover;">
                
                <div class="mt-4">
                    <h3 class="font-serif mb-3">Description</h3>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                </div>

                <?php if (!empty($facilities_list)): ?>
                <div class="mt-4">
                    <h4 class="font-serif mb-3">Room Facilities</h4>
                    <div class="row g-2">
                        <?php foreach ($facilities_list as $item): ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-gold me-2"></i>
                                <span><?php echo htmlspecialchars($item); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-5">
                <div class="booking-form-card sticky-top" style="top: 100px;">
                    <div class="price mb-2" style="font-size: 2rem;">
                        RM <?php echo number_format($room['price_per_month'], 0); ?>
                        <small class="text-muted" style="font-size: 0.9rem;">/ month</small>
                    </div>
                    
                    <ul class="list-unstyled room-meta mb-4">
                        <li class="mb-2"><i class="fas fa-door-open me-2"></i> Room No: <strong><?php echo htmlspecialchars($room['room_number']); ?></strong></li>
                        <li class="mb-2"><i class="fas fa-user-graduate me-2"></i> Capacity: <strong><?php echo (int)$room['capacity']; ?> Students</strong></li>
                        <li class="mb-2"><i class="fas fa-bed me-2"></i> Bed: <strong><?php echo htmlspecialchars($room['bed_type']); ?></strong></li>
                        <li class="mb-2"><i class="fas fa-ruler-combined me-2"></i> Size: <strong><?php echo (int)$room['size_sqm']; ?> m²</strong></li>
                        <li class="mb-2"><i class="fas fa-tag me-2"></i> Type: <strong><?php echo ucfirst(str_replace('-', ' ', $room['room_type'])); ?></strong></li>
                    </ul>

                    <a href="booking.php?room=<?php echo urlencode($room['slug']); ?>" class="btn btn-gold w-100 btn-lg">
                        <i class="fas fa-calendar-check me-2"></i>Book This Room
                    </a>
                    <a href="rooms.php" class="btn btn-outline-secondary w-100 mt-2">Back to Accommodation</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
