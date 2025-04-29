<?php
// Prevent displaying PHP errors directly
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Log errors instead of displaying them
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("Error: $errstr in $errfile on line $errline");
    echo json_encode(['error' => true, 'message' => 'Server error occurred']);
    exit;
}
set_error_handler('handleError');

// Handle fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR])) {
        echo json_encode(['error' => true, 'message' => 'Fatal server error occurred']);
    }
});


// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and product class files
include_once '../config/database.php';
include_once '../models/product.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize product object
$product = new Product($db);

// Get HTTP method and request data
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"));

// Process based on HTTP method
switch($method) {
    case 'GET':
        // Read products
        if(isset($_GET['id'])) {
            // Get single product
            $product->id = $_GET['id'];
            $product_data = $product->readOne();
            
            if($product_data) {
                http_response_code(200);
                echo json_encode($product_data);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Product not found."));
            }
        } else {
            // Get all products
            $stmt = $product->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $products_arr = array();
                $products_arr["records"] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $product_item = array(
                        "id" => $id,
                        "name" => $name,
                        "description" => $description,
                        "category_id" => $category_id,
                        "category" => $category,
                        "color" => $color,
                        "price_range" => $price_range,
                        "image" => $image,
                        "is_available" => $is_available == 1 ? "available" : "unavailable",
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    );
                    
                    array_push($products_arr["records"], $product_item);
                }
                
                http_response_code(200);
                echo json_encode($products_arr);
            } else {
                http_response_code(200);
                echo json_encode(array("records" => array()));
            }
        }
        break;
        
    case 'POST':
        // Create product
        if(
            !empty($data->name) &&
            !empty($data->category) &&
            !empty($data->color) &&
            !empty($data->price_range) &&
            !empty($data->image)
        ){
            // Set product property values
            $product->name = $data->name;
            $product->description = $data->description;
            $product->category_id = $data->category_id ?? null;
            $product->category = $data->category;
            $product->color = $data->color;
            $product->price_range = $data->price_range;
            $product->image = $data->image;
            $product->is_available = $data->is_available == "available" ? 1 : 0;
            
            // Create the product
            if($product->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Product was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
        }
        break;
        
    case 'PUT':
        // Update product
        if(!empty($data->id)) {
            // Set ID property of product to be edited
            $product->id = $data->id;
            
            // Set product property values
            $product->name = $data->name ?? null;
            $product->description = $data->description ?? null;
            $product->category_id = $data->category_id ?? null;
            $product->category = $data->category ?? null;
            $product->color = $data->color ?? null;
            $product->price_range = $data->price_range ?? null;
            $product->image = $data->image ?? null;
            
            if(isset($data->is_available)) {
                $product->is_available = $data->is_available == "available" ? 1 : 0;
            } else {
                $product->is_available = null;
            }
            
            // Update the product
            if($product->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Product was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update product. No ID provided."));
        }
        break;
        
    case 'DELETE':
        // Delete product
        if(!empty($_GET['id'])) {
            // Set product id to be deleted
            $product->id = $_GET['id'];
            
            // Delete the product
            if($product->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Product was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete product."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to delete product. No ID provided."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>