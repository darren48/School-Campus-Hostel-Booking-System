<?php
$page_title = 'Accommodation';
require_once 'includes/header.php';

$db = getDB();

// Filter by room type if provided
$room_type = isset($_GET['type']) ? clean($_GET['type']) : '';
$sql = "SELECT * FROM rooms WHERE is_available = 1";
$params = [];

if ($room_type && in_array($room_type, ['single', 'twin', '4-bed', '6-bed'])) {
    $sql .= " AND room_type = ?";
    $params[] = $room_type;
}
$sql .= " ORDER BY price_per_month ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rooms = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>Accommodation</h1>
        <p class="lead opacity-75">Find the right room for your student life</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <!-- Filter -->
        <div class="d-flex flex-wrap gap-2 justify-content-center mb-5">
            <a href="rooms.php" class="btn btn-sm <?php echo $room_type === '' ? 'btn-gold' : 'btn-outline-gold'; ?>">All</a>
            <a href="rooms.php?type=single" class="btn btn-sm <?php echo $room_type === 'single' ? 'btn-gold' : 'btn-outline-gold'; ?>">Single</a>
            <a href="rooms.php?type=twin" class="btn btn-sm <?php echo $room_type === 'twin' ? 'btn-gold' : 'btn-outline-gold'; ?>">Twin Sharing</a>
            <a href="rooms.php?type=4-bed" class="btn btn-sm <?php echo $room_type === '4-bed' ? 'btn-gold' : 'btn-outline-gold'; ?>">4-Bed Shared</a>
            <a href="rooms.php?type=6-bed" class="btn btn-sm <?php echo $room_type === '6-bed' ? 'btn-gold' : 'btn-outline-gold'; ?>">6-Bed Dormitory</a>
        </div>

        <?php if (empty($rooms)): ?>
        <div class="text-center py-5">
            <i class="fas fa-bed fa-3x text-muted mb-3"></i>
            <p class="text-muted">No rooms found for this filter.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($rooms as $room): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card room-card">
                    <div class="overflow-hidden">
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($room['name']); ?>">
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title font-serif mb-0"><?php echo htmlspecialchars($room['name']); ?></h5>
                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($room['room_number']); ?></span>
                        </div>
                        <div class="room-meta mb-2">
                            <span class="me-3"><i class="fas fa-user-graduate"></i> <?php echo (int)$room['capacity']; ?> Students</span>
                            <span class="me-3"><i class="fas fa-bed"></i> <?php echo htmlspecialchars($room['bed_type']); ?></span>
                            <span><i class="fas fa-ruler-combined"></i> <?php echo (int)$room['size_sqm']; ?> m²</span>
                        </div>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($room['description'], 0, 100)); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="price">RM <?php echo number_format($room['price_per_month'], 0); ?> <small>/ month</small></div>
                            <a href="room-details.php?slug=<?php echo urlencode($room['slug']); ?>" class="btn btn-sm btn-outline-gold">Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
