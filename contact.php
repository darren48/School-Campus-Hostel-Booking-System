<?php
$page_title = 'Contact';
require_once 'includes/header.php';

$db = getDB();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? clean($_POST['name']) : '';
    $email = isset($_POST['email']) ? clean($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? clean($_POST['subject']) : '';
    $message = isset($_POST['message']) ? clean($_POST['message']) : '';

    if ($name && $email && $message) {
        try {
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = true;
        } catch (PDOException $e) {
            $error = 'Failed to send message. Please try again.';
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}
?>

<div class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p class="lead opacity-75">Get in touch with the hostel office</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <h3 class="font-serif mb-4">Hostel Office</h3>
                <ul class="list-unstyled">
                    <li class="mb-3 d-flex">
                        <i class="fas fa-map-marker-alt text-gold mt-1 me-3"></i>
                        <div>
                            <strong>Address</strong><br>
                            <span class="text-muted">Campus Area, TAR UMT<br>Kuala Lumpur, Malaysia</span>
                        </div>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-phone text-gold mt-1 me-3"></i>
                        <div>
                            <strong>Phone</strong><br>
                            <span class="text-muted">+60 3-1234 5678</span>
                        </div>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-envelope text-gold mt-1 me-3"></i>
                        <div>
                            <strong>Email</strong><br>
                            <span class="text-muted">hostel@grandtarabc.edu.my</span>
                        </div>
                    </li>
                    <li class="mb-3 d-flex">
                        <i class="fas fa-clock text-gold mt-1 me-3"></i>
                        <div>
                            <strong>Office Hours</strong><br>
                            <span class="text-muted">Mon–Fri: 9:00 AM – 5:00 PM<br>Sat: 9:00 AM – 1:00 PM</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-7">
                <div class="booking-form-card">
                    <h3 class="font-serif mb-4">Send a Message</h3>

                    <?php if ($success): ?>
                    <div class="alert alert-success alert-success-custom">
                        <i class="fas fa-check-circle me-2"></i>Thank you! Your message has been sent.
                    </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-danger-custom">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Subject</label>
                                <input type="text" name="subject" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Message *</label>
                                <textarea name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-gold px-4">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
