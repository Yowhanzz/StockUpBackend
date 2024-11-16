<?php
// Include the database connection
require_once '../Connection.php';

// Set content type to JSON
header("Content-Type: application/json");

try {
    // Ensure the request method is GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["status" => "error", "message" => "Only GET method is allowed."]);
        exit;
    }

    // Connect to the database
    $db = connectDB();

    // Prepare and execute the query
    $query = "SELECT full_name, time_in, time_out FROM user_sessions";
    $stmt = $db->prepare($query);
    $stmt->execute();

    // Fetch all results
    $sessions = $stmt->fetchAll();

    // Respond with the data
    echo json_encode([
        "status" => "success",
        "data" => $sessions
    ]);
} catch (Exception $e) {
    // Handle errors and return a 500 Internal Server Error response
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
