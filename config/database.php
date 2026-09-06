<?php
/**
 * Database Configuration
 * Grand TAR ABC Student Hostel Booking System
 * For AWS: Use RDS endpoint or local MySQL
 */

define('DB_HOST', 'localhost');      // AWS RDS endpoint e.g. yourdb.xxxxx.rds.amazonaws.com
define('DB_NAME', 'grand_tar_abc');
define('DB_USER', 'root');           // Change for production
define('DB_PASS', '');               // Change for production
define('DB_CHARSET', 'utf8mb4');

// Site settings
define('SITE_NAME', 'Grand TAR ABC');
define('SITE_TAGLINE', 'Student Hostel');
define('SITE_URL', 'http://localhost/grand-tar-abc'); // Change to your domain
define('ADMIN_EMAIL', 'admin@grandtarabc.edu.my');

// Timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

/**
 * Get PDO connection
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Generate unique booking reference (GABC prefix)
 */
function generateBookingRef() {
    return 'GABC' . strtoupper(substr(uniqid(), -6));
}

/**
 * Sanitize input
 */
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in (student)
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Check if admin is logged in
 */
function isAdmin() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin';
}

/**
 * Calculate months between two dates (for hostel pricing)
 */
function calculateMonths($move_in, $move_out) {
    $start = new DateTime($move_in);
    $end = new DateTime($move_out);
    $diff = $start->diff($end);
    $months = $diff->y * 12 + $diff->m;
    if ($diff->d > 0) {
        $months += 1; // Round up partial month
    }
    return max(1, $months);
}
?>
