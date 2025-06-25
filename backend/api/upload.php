<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_FILES['image']) {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo json_encode([
            "status" => "success",
            "message" => "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.",
            "path" => "uploads/" . basename( $_FILES["image"]["name"])
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Sorry, there was an error uploading your file."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No file was uploaded."
    ]);
}
?> 