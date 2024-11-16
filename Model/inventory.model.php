<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require_once '../Connection.php';

ini_set('display_errors', 1);  // Enable error display
error_reporting(E_ALL);         // Report all types of errors

class InventoryModel {
    private $pdo;

    public function __construct() {
        $this->pdo = connectDB();
    }

    public function validateItemStatus($quantity) {
        if ($quantity <= 25) {
            return 'very low';
        } elseif ($quantity <= 50) {
            return 'low';
        } elseif ($quantity <= 80) {
            return 'average';
        } elseif ($quantity <= 100) {
            return 'high';
        } else {
            return 'very high';
        }
    }

    // Create an item
    public function createItem($item_name, $category, $brand, $quantity, $status) {
        $sql = "INSERT INTO inventory (item_name, category, brand, quantity, status) 
                VALUES (:item_name, :category, :brand, :quantity, :status)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':item_name', $item_name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    // Read all items (or with filtering based on status)
    public function readItems($status = null, $sortBy = 'item_name') {
        $sql = "SELECT * FROM inventory";
        if ($status) {
            $sql .= " WHERE status = :status";
        }
        $sql .= " ORDER BY $sortBy ASC"; // Sorting by item name or status
        $stmt = $this->pdo->prepare($sql);

        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update an item's details
    public function updateItem($item_id, $item_name, $category, $brand, $quantity, $status) {
        $sql = "UPDATE inventory SET item_name = :item_name, category = :category, brand = :brand, 
                quantity = :quantity, status = :status WHERE item_id = :item_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':item_name', $item_name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    // Delete an item by item_id
    public function deleteItem($item_id) {
        $sql = "DELETE FROM inventory WHERE item_id = :item_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':item_id', $item_id);
        return $stmt->execute();
        
    }

    // Get the highest quantity in a given status
    public function getHighestInStatus($status) {
        $sql = "SELECT * FROM inventory WHERE status = :status ORDER BY quantity DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Handle HTTP requests
function handleRequest() {
    header('Content-Type: application/json');
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode('/', $uri);

    // Initialize InventoryModel
    $inventory = new InventoryModel();

    // If URI has an item ID, handle accordingly
    $item_id = isset($uri[3]) ? (int)$uri[3] : null;

    // Switch based on the HTTP method
    switch ($method) {
        case 'GET':
            if ($item_id) {
                // Get a specific item by ID
                $item = $inventory->readItems(null, 'item_name');
                $item = array_filter($item, fn($i) => $i['item_id'] == $item_id);
                if ($item) {
                    echo json_encode(array_values($item)[0]);
                } else {
                    echo json_encode(["error" => "Item not found."]);
                }
            } else {
                // Get all items, optionally filtered by status or sorting
                $status = isset($_GET['status']) ? $_GET['status'] : null;
                $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'item_name';
                $items = $inventory->readItems($status, $sortBy);
                echo json_encode($items);
            }
            break;

            case 'POST':
                // Create a new item
                $data = json_decode(file_get_contents("php://input"), true);
                if (isset($data['item_name'], $data['category'], $data['brand'], $data['quantity'])) {
                    // Calculate status based on quantity
                    $data['status'] = $inventory->validateItemStatus($data['quantity']);
                    $inventory->createItem($data['item_name'], $data['category'], $data['brand'], $data['quantity'], $data['status']);
                    echo json_encode(["message" => "Item created successfully."]);
                } else {
                    echo json_encode(["error" => "Invalid input data."]);
                }
                break;
            
                case 'PUT':
                    // Get the input data from the request body
                    $data = json_decode(file_get_contents("php://input"), true);
                
                    // Debugging step: Check the received data
                    if (!$data) {
                        echo json_encode(["error" => "No input data received."]);
                        exit;
                    }
                
                    // Validate required fields
                    if (isset($data['item_id'], $data['item_name'], $data['category'], $data['brand'], $data['quantity'])) {
                        $item_id = (int) $data['item_id']; // Extract item_id from JSON
                
                        // Calculate the status based on the quantity
                        $status = $inventory->validateItemStatus($data['quantity']);
                
                        // Update the item
                        if ($inventory->updateItem(
                            $item_id,
                            $data['item_name'],
                            $data['category'],
                            $data['brand'],
                            $data['quantity'],
                            $status
                        )) {
                            echo json_encode(["message" => "Item updated successfully."]);
                        } else {
                            echo json_encode(["error" => "Failed to update item."]);
                        }
                    } else {
                        echo json_encode(["error" => "Invalid input data or missing item_id."]);
                    }
                    break;

                    case 'DELETE':
                        // Check if item_id is provided in the URL
                        if ($item_id) {
                            // Call the deleteItem function
                            if ($inventory->deleteItem($item_id)) {
                                echo json_encode(["message" => "Item deleted successfully."]);
                            } else {
                                echo json_encode(["error" => "Failed to delete item."]);
                            }
                        } else {
                            echo json_encode(["error" => "Item ID is required."]);
                        }
                        break;
                                        
                
    }                                        
}