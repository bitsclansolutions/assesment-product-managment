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
        // Retrieve categories
        if(!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            get_category($id);
        } else {
            get_categories();
        }
        break;
    case 'POST':
        // Create a new category
        create_category();
        break;
    case 'PUT':
        // Update a category
        update_category();
        break;
    case 'DELETE':
        // Delete a category
        delete_category();
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_categories() {
    global $db;
    $query = "SELECT * FROM categories";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
}

function get_category($id) {
    global $db;
    $query = "SELECT * FROM categories WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($category);
}

function create_category() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));
    $query = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $data->name);
    if($stmt->execute()) {
        $response = array("status" => "success", "message" => "Category created successfully.");
        http_response_code(201);
    } else {
        $response = array("status" => "error", "message" => "Failed to create category.");
        http_response_code(500);
    }
    echo json_encode($response);
}

function update_category() {
    global $db;
    $data = json_decode(file_get_contents("php://input"));
    $id = intval($_GET["id"]);
    $query = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $data->name);
    $stmt->bindParam(2, $id);
    if($stmt->execute()) {
        $response = array("status" => "success", "message" => "Category updated successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to update category.");
        http_response_code(500);
    }
    echo json_encode($response);
}

function delete_category() {
    global $db;
    $id = intval($_GET["id"]);
    $query = "DELETE FROM categories WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    if($stmt->execute()) {
        $response = array("status" => "success", "message" => "Category deleted successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to delete category.");
        http_response_code(500);
    }
    echo json_encode($response);
}
?> 