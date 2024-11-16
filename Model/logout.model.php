<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require '../Connection.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$headers = getallheaders();
$jwt = $headers['Authorization'] ?? '';

if ($jwt) {
    try {
        // Decode the JWT token
        $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS256'));
        $userId = $decoded->data->userId;

        // Check if the token has expired
        $currentTimestamp = time();
        $tokenExpirationTime = $decoded->exp;

        if ($currentTimestamp > $tokenExpirationTime) {
            // Token has expired, log the user out
            $pdo = connectDB();
            $stmt = $pdo->prepare("UPDATE user_sessions SET is_logged_in = FALSE, time_out = NOW() WHERE user_id = :user_id AND is_logged_in = TRUE");
            $stmt->execute(['user_id' => $userId]);

            echo json_encode(["message" => "Token expired, logged out automatically"]);
        } else {
            // Token is still valid, proceed with manual logout
            $pdo = connectDB();
            $stmt = $pdo->prepare("UPDATE user_sessions SET is_logged_in = FALSE, time_out = NOW() WHERE user_id = :user_id AND is_logged_in = TRUE");
            $stmt->execute(['user_id' => $userId]);

            echo json_encode(["message" => "Logged out successfully"]);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Access denied", "details" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Token not provided"]);
}
?>
