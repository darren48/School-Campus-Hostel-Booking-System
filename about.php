<?php
$page_title = 'About';
require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>About Grand TAR ABC</h1>
        <p class="lead opacity-75">Student hostel designed for academic success and comfortable living</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <p class="section-subtitle">Our Mission</p>
                <h2 class="section-title">Supporting Student Life</h2>
                <p class="text-muted">Grand TAR ABC is a student hostel that provides affordable, comfortable and secure accommodation for tertiary students. We understand the importance of a good living environment for academic performance and personal growth.</p>
                <p class="text-muted">Our rooms range from private single rooms to shared dormitories, giving students flexible options according to budget and preference. All rooms come with essential facilities such as Wi-Fi, study space and storage.</p>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800" alt="Hostel Room" class="img-fluid rounded-3 shadow">
            </div>
        </div>

        <div class="row g-4 mt-5 text-center">
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon"><i class="fas fa-home"></i></div>
                    <h5>Comfortable Rooms</h5>
                    <p class="text-muted small mb-0">Clean, well-maintained rooms with beds, study desks and storage for every student.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon"><i class="fas fa-wallet"></i></div>
                    <h5>Affordable Rates</h5>
                    <p class="text-muted small mb-0">Competitive monthly rates with transparent pricing and flexible room types.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon"><i class="fas fa-shield-alt"></i></div>
                    <h5>Safe & Secure</h5>
                    <p class="text-muted small mb-0">24/7 security, CCTV and hostel staff to ensure a safe environment for residents.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding bg-cream">
    <div class="container text-center">
        <h2 class="section-title mb-3">Ready to Join Us?</h2>
        <p class="text-muted col-lg-6 mx-auto mb-4">Book your room online and start your comfortable campus living experience at Grand TAR ABC.</p>
        <a href="booking.php" class="btn btn-gold btn-lg px-5">Book Accommodation</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
