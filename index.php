<?php
$page_title = 'Home';
require_once 'includes/header.php';

$db = getDB();

// Fetch featured rooms
$stmt = $db->query("SELECT * FROM rooms WHERE is_available = 1 ORDER BY price_per_month ASC LIMIT 3");
$rooms = $stmt->fetchAll();

// Fetch facilities
$stmt = $db->query("SELECT * FROM facilities LIMIT 6");
$facilities = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-content">
                    <p class="section-subtitle text-white-50">Welcome to Grand TAR ABC</p>
                    <h1>Your Home Away From Campus</h1>
                    <p class="lead">Comfortable, affordable and convenient student accommodation designed for a better campus living experience.</p>
                    <div class="mt-4">
                        <a href="rooms.php" class="btn btn-gold btn-lg px-4 me-2">Explore Rooms</a>
                        <a href="booking.php" class="btn btn-outline-light btn-lg px-4">Book Accommodation</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-booking-form">
                    <h5 class="mb-3 text-dark font-serif">Find Your Accommodation</h5>
                    <form action="booking.php" method="GET">
                        <div class="row g-2">
                            <div class="col-6">
                                <label>Move-in Date</label>
                                <input type="date" name="move_in" id="move_in" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label>Move-out Date</label>
                                <input type="date" name="move_out" id="move_out" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label>Students</label>
                                <select name="students" class="form-select form-select-sm">
                                    <option value="1" selected>1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Room Type</label>
                                <select name="room_type" class="form-select form-select-sm">
                                    <option value="">Any</option>
                                    <option value="single">Single</option>
                                    <option value="twin">Twin Sharing</option>
                                    <option value="4-bed">4-Bed Shared</option>
                                    <option value="6-bed">6-Bed Dorm</option>
                                </select>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-gold w-100">Check Availability</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Preview -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800" alt="Grand TAR ABC Hostel" class="img-fluid rounded-3 shadow">
            </div>
            <div class="col-lg-6">
                <p class="section-subtitle">About Us</p>
                <h2 class="section-title">Comfortable Student Living</h2>
                <p class="text-muted">Grand TAR ABC provides safe, affordable and convenient hostel accommodation for students. Our rooms are designed to support your studies with individual beds, study space and essential facilities.</p>
                <p class="text-muted">Whether you prefer a private single room or shared dormitory living, we offer options that fit different budgets and lifestyles — all within a secure and supportive environment.</p>
                <a href="about.php" class="btn btn-outline-gold mt-3">Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- Rooms Section -->
<section class="section-padding bg-cream">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-subtitle">Accommodation</p>
            <h2 class="section-title">Our Rooms</h2>
            <p class="text-muted col-lg-6 mx-auto">Choose from single, twin sharing, 4-bed and 6-bed options designed for student living.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($rooms as $room): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card room-card">
                    <div class="overflow-hidden">
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($room['name']); ?>">
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title font-serif"><?php echo htmlspecialchars($room['name']); ?></h5>
                        <div class="room-meta mb-2">
                            <span class="me-3"><i class="fas fa-bed"></i> <?php echo (int)$room['capacity']; ?> Students</span>
                            <span><i class="fas fa-ruler-combined"></i> <?php echo (int)$room['size_sqm']; ?> m²</span>
                        </div>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($room['description'], 0, 90)); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="price">RM <?php echo number_format($room['price_per_month'], 0); ?> <small>/ month</small></div>
                            <a href="room-details.php?slug=<?php echo urlencode($room['slug']); ?>" class="btn btn-sm btn-outline-gold">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="rooms.php" class="btn btn-gold px-5">View All Rooms</a>
        </div>
    </div>
</section>

<!-- Facilities Highlight -->
<section class="section-padding">
    <div class="container">
        <div class="featured-facility">
            <div class="row g-0 align-items-center">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=900" alt="Study Area" class="img-fluid w-100" style="height: 400px; object-fit: cover;">
                </div>
                <div class="col-lg-6">
                    <div class="content">
                        <span class="badge-hostel mb-3 d-inline-block">HOSTEL FACILITIES</span>
                        <h2 class="text-white mb-3">Study Areas &amp; Essential Facilities</h2>
                        <p class="opacity-75">Focus on your studies with quiet study rooms, high-speed Wi-Fi, laundry facilities, shared pantry and 24/7 security. Everything you need for comfortable campus living is here.</p>
                        <ul class="list-unstyled mt-3 opacity-75">
                            <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> High-speed Wi-Fi in all rooms</li>
                            <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> Quiet study areas</li>
                            <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> Laundry &amp; pantry facilities</li>
                            <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> 24/7 security &amp; CCTV</li>
                        </ul>
                        <a href="facilities.php" class="btn btn-gold mt-3">Explore Facilities</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Facilities Grid -->
<section class="section-padding bg-cream">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-subtitle">Facilities</p>
            <h2 class="section-title">What We Provide</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($facilities as $f): ?>
            <div class="col-md-6 col-lg-4">
                <div class="facility-card">
                    <div class="icon">
                        <i class="<?php echo htmlspecialchars($f['icon']); ?>"></i>
                    </div>
                    <h5><?php echo htmlspecialchars($f['name']); ?></h5>
                    <p class="text-muted small mb-0"><?php echo htmlspecialchars(substr($f['description'], 0, 90)); ?>...</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section-padding bg-navy text-center">
    <div class="container">
        <h2 class="text-white mb-3">Ready to Book Your Hostel Room?</h2>
        <p class="text-white-50 col-lg-6 mx-auto mb-4">Secure your accommodation today and enjoy a comfortable, convenient student living experience at Grand TAR ABC.</p>
        <a href="booking.php" class="btn btn-gold btn-lg px-5">Book Accommodation</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
