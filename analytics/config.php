<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'adile_analytics');  // Use the username you created
define('DB_PASS', 'your_secure_password');  // Use the password you created
define('DB_NAME', 'adile_analytics');

// Analytics configuration
define('TRACK_PAGEVIEWS', true);
define('TRACK_EVENTS', true);
define('TRACK_USER_BEHAVIOR', true);

// Session duration in minutes
define('SESSION_DURATION', 30);

// Initialize database connection
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        error_log("Connection failed: " . $e->getMessage());
        return null;
    }
}
?> 