<?php
require '../Connection.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->full_name) || !isset($data->username) || !isset($data->password)) {
    echo json_encode(["error" => "Missing required fields"]);
    return;
}

$full_name = $data->full_name;
$username = $data->username;
$password = password_hash($data->password, PASSWORD_BCRYPT);

try {
    $pdo = connectDB();
    $stmt = $pdo->prepare(
        "INSERT INTO users (full_name, username, password) VALUES (:full_name, :username, :password)"
    );
    $stmt->execute([
        'full_name' => $full_name,
        'username' => $username,
        'password' => $password,
    ]);
    echo json_encode(["message" => "User registered successfully"]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(["error" => "Username already exists"]);
    } else {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
}
?>
