<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config.php';
include_once '../Database.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        if(!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            get_product($id);
        } else {
            get_products();
        }
        break;
    case 'POST':
        create_product();
        break;
    case 'PUT':
        update_product();
        break;
    case 'DELETE':
        delete_product();
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_products() {
    global $db;
    $query = "
        SELECT p.*, GROUP_CONCAT(c.name SEPARATOR ', ') as categories
        FROM products p
        LEFT JOIN product_categories pc ON p.id = pc.product_id
        LEFT JOIN categories c ON pc.category_id = c.id
        GROUP BY p.id
    ";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
}

function get_product($id) {
    global $db;
    $query = "
        SELECT p.*, GROUP_CONCAT(c.id SEPARATOR ',') as category_ids
        FROM products p
        LEFT JOIN product_categories pc ON p.id = pc.product_id
        LEFT JOIN categories c ON pc.category_id = c.id
        WHERE p.id = ?
        GROUP BY p.id
    ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($product);
}

function create_product() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));

    $query = "INSERT INTO products (name, price, image) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $data->name);
    $stmt->bindParam(2, $data->price);
    $stmt->bindParam(3, $data->image);

    if($stmt->execute()) {
        $product_id = $db->lastInsertId();
        if(!empty($data->categories)) {
            foreach($data->categories as $category_id) {
                $query = "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(1, $product_id);
                $stmt->bindParam(2, $category_id);
                $stmt->execute();
            }
        }
        $response = array("status" => "success", "message" => "Product created successfully.");
        http_response_code(201);
    } else {
        $response = array("status" => "error", "message" => "Failed to create product.");
        http_response_code(500);
    }
    echo json_encode($response);
}

function update_product() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));
    $id = intval($_GET["id"]);

    $query = "UPDATE products SET name = ?, price = ?, image = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $data->name);
    $stmt->bindParam(2, $data->price);
    $stmt->bindParam(3, $data->image);
    $stmt->bindParam(4, $id);

    if($stmt->execute()) {
        $query = "DELETE FROM product_categories WHERE product_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        if(!empty($data->categories)) {
            foreach($data->categories as $category_id) {
                $query = "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(1, $id);
                $stmt->bindParam(2, $category_id);
                $stmt->execute();
            }
        }
        $response = array("status" => "success", "message" => "Product updated successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to update product.");
        http_response_code(500);
    }
    echo json_encode($response);
}

function delete_product() {
    global $db;
    $id = intval($_GET["id"]);

    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);

    if($stmt->execute()) {
        $response = array("status" => "success", "message" => "Product deleted successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to delete product.");
        http_response_code(500);
    }
    echo json_encode($response);
}
?> 