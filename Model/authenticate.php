<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require '../Connection.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Get all headers
$headers = getallheaders();

// Extract the Authorization header
$jwt = $headers['Authorization'] ?? '';

// If the Authorization header is present
if ($jwt) {
    // Clean the token by removing "Bearer " prefix if it exists
    $jwt = str_replace("Bearer ", "", $jwt);

    // Log the raw JWT token for debugging (ensure you remove sensitive info before sharing)
    // echo json_encode(["raw_token" => $jwt]);

    try {
        // Decode the JWT token
        $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS256'));
        
        // If successful, return access granted message
        echo json_encode(["message" => "Access granted"]);
    } catch (Exception $e) {
        // Return the detailed error if decoding fails
        echo json_encode(["error" => "Access denied", "details" => $e->getMessage()]);
    }
} else {
    // Token not provided
    echo json_encode(["error" => "Token not provided"]);
}
?>
