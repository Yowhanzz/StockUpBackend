<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
require '../Connection.php';

$data = json_decode(file_get_contents("php://input"));
$full_name = $data->full_name; 
$username = $data->username;
$password = password_hash($data->password, PASSWORD_BCRYPT);

try {
    $pdo = connectDB();
    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password) VALUES (:full_name, :username, :password)");
    $stmt->execute(['full_name' => $full_name, 'username' => $username, 'password' => $password]);
    echo json_encode(["message" => "User registered successfully"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Username already exists"]);
}
?>