<?php
// Basic test file to check if PHP is working on cloud server
echo "PHP is working!<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";

// Test if we can include files
if (file_exists('php-bin/connection.php')) {
    echo "connection.php exists<br>";
    try {
        require_once('php-bin/connection.php');
        echo "connection.php included successfully<br>";
        
        // Test database connection
        if (isset($con)) {
            echo "Database connection variable exists<br>";
            // Try a simple query
            $result = pg_query($con, "SELECT version()");
            if ($result) {
                $row = pg_fetch_array($result);
                echo "Database connected successfully: " . $row[0] . "<br>";
            } else {
                echo "Database query failed: " . pg_last_error($con) . "<br>";
            }
        } else {
            echo "Database connection variable not found<br>";
        }
    } catch (Exception $e) {
        echo "Error including connection.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "connection.php not found<br>";
}

if (file_exists('php-bin/supports.php')) {
    echo "supports.php exists<br>";
} else {
    echo "supports.php not found<br>";
}
?>