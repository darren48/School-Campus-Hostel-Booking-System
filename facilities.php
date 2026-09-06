<?php
$page_title = 'Facilities';
require_once 'includes/header.php';

$db = getDB();
$stmt = $db->query("SELECT * FROM facilities ORDER BY id ASC");
$facilities = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="container">
        <h1>Hostel Facilities</h1>
        <p class="lead opacity-75">Everything you need for comfortable campus living</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($facilities as $f): ?>
            <div class="col-md-6 col-lg-4">
                <div class="facility-card">
                    <div class="icon">
                        <i class="<?php echo htmlspecialchars($f['icon']); ?>"></i>
                    </div>
                    <h5><?php echo htmlspecialchars($f['name']); ?></h5>
                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($f['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="row mt-5 g-4 align-items-center">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1557597774-9d273605dfa9?w=800" alt="Security" class="img-fluid rounded-3 shadow">
            </div>
            <div class="col-lg-6">
                <p class="section-subtitle">Safety First</p>
                <h2 class="section-title">Secure Living Environment</h2>
                <p class="text-muted">Grand TAR ABC prioritises student safety with controlled access, CCTV coverage and on-site wardens. Our facilities are maintained regularly so you can focus on your studies with peace of mind.</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-gold me-2"></i> Controlled entry access</li>
                    <li class="mb-2"><i class="fas fa-check text-gold me-2"></i> CCTV monitoring</li>
                    <li class="mb-2"><i class="fas fa-check text-gold me-2"></i> On-site hostel staff</li>
                    <li class="mb-2"><i class="fas fa-check text-gold me-2"></i> Emergency contact procedures</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
