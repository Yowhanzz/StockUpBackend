<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'system_stockup');
define('JWT_SECRET_KEY', 'stock_up_beshie'); // Use a strong key in production

function connectDB() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    $username = DB_USER;
    $password = DB_PASS;
    
    try {
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        // Make sure to return a JSON response if there is an error
        echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
        exit; // Stop execution after error
    }
}
?>