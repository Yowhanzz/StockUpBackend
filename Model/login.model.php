<?php
require '../Connection.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"));

if (isset($data->username) && isset($data->password)) {
    $username = $data->username;
    $password = $data->password;

    try {
        // Check if user exists
        $pdo = connectDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Check if the user already has an active session
            $sessionStmt = $pdo->prepare("SELECT * FROM user_sessions WHERE user_id = :user_id AND is_logged_in = TRUE");
            $sessionStmt->execute(['user_id' => $user['id']]);
            $activeSession = $sessionStmt->fetch(PDO::FETCH_ASSOC);

            if ($activeSession) {
                echo json_encode(["error" => "User is already logged in. Please log out first."]);
                return;
            }

            // Create a new session for the user with full_name
            $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, full_name, is_logged_in) VALUES (:user_id, :full_name, TRUE)");
            $stmt->execute([
                'user_id' => $user['id'],
                'full_name' => $user['full_name'], // Storing full_name
            ]);

            // Create JWT token
            $payload = [
                'iss' => "http://yourwebsite.com",
                'iat' => time(),
                'exp' => time() + (60 * 60), // Token expires in 1 hour
                'data' => [
                    'username' => $username,
                    'userId' => $user['id']
                ]
            ];

            $jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');

            echo json_encode(["token" => $jwt]);
        } else {
            echo json_encode(["error" => "Invalid credentials"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Missing username or password"]);
}
?>