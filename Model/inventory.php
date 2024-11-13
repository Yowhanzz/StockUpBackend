<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require_once '../Connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['search'])) {
            searchItems($_GET['search']);
        } else {
            getItems();
        }
        break;
    case 'POST':
        addItem();
        break;
    case 'PUT':
        updateItem();
        break;
    case 'DELETE':
        if (isset($_GET['item_id'])) {
            deleteItem($_GET['item_id']);
        }
        break;
    default:
        echo json_encode(["message" => "Method not supported"]);
}
function getItems() {
    $pdo = connectDB();
    $stmt = $pdo->query("SELECT * FROM inventory ORDER BY item_name ASC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
}

function searchItems($query) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_name LIKE :query ORDER BY item_name ASC");
    $stmt->execute(['query' => '%' . $query . '%']);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
}

function addItem() {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['item_name'], $data['brand'], $data['category'], $data['quantity'])) {
        $pdo = connectDB();
        $stmt = $pdo->prepare("INSERT INTO inventory (item_name, brand, category, quantity) VALUES (:item_name, :brand, :category, :quantity)");
        $stmt->execute([
            'item_name' => $data['item_name'],
            'brand' => $data['brand'],
            'category' => $data['category'],
            'quantity' => $data['quantity']
        ]);
        echo json_encode(["message" => "Item added successfully"]);
    } else {
        echo json_encode(["error" => "Invalid input"]);
    }
}

function updateItem() {
    parse_str(file_get_contents("php://input"), $data);
    if (isset($data['item_id'], $data['item_name'], $data['brand'], $data['category'], $data['quantity'])) {
        $pdo = connectDB();
        $stmt = $pdo->prepare("UPDATE inventory SET item_name = :item_name, brand = :brand, category = :category, quantity = :quantity WHERE item_id = :item_id");
        $stmt->execute([
            'item_id' => $data['item_id'],
            'item_name' => $data['item_name'],
            'brand' => $data['brand'],
            'category' => $data['category'],
            'quantity' => $data['quantity']
        ]);
        echo json_encode(["message" => "Item updated successfully"]);
    } else {
        echo json_encode(["error" => "Invalid input"]);
    }
}

function deleteItem($item_id) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE item_id = :item_id");
    $stmt->execute(['item_id' => $item_id]);
    echo json_encode(["message" => "Item deleted successfully"]);
}
?>
