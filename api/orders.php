<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Order.php';

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $stmt = $order->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $orders_arr = array();
            $orders_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $order_item = array(
                    "id" => $id,
                    "user_id" => $user_id,
                    "username" => $username,
                    "total_amount" => $total_amount,
                    "status" => $status,
                    "shipping_address" => $shipping_address,
                    "contact_number" => $contact_number,
                    "created_at" => $created_at
                );

                // Get order items
                $items_stmt = $order->getOrderItems($id);
                $order_item["items"] = array();
                while ($item = $items_stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($order_item["items"], $item);
                }

                array_push($orders_arr["records"], $order_item);
            }

            http_response_code(200);
            echo json_encode($orders_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No orders found."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (
            !empty($data->user_id) &&
            !empty($data->total_amount) &&
            !empty($data->shipping_address) &&
            !empty($data->contact_number) &&
            !empty($data->items)
        ) {
            $order->user_id = $data->user_id;
            $order->total_amount = $data->total_amount;
            $order->status = 'pending';
            $order->shipping_address = $data->shipping_address;
            $order->contact_number = $data->contact_number;

            $order_id = $order->create();

            if ($order_id) {
                // Add order items
                foreach ($data->items as $item) {
                    $order->addOrderItem(
                        $order_id,
                        $item->product_id,
                        $item->quantity,
                        $item->price
                    );
                }

                http_response_code(201);
                echo json_encode(array(
                    "message" => "Order was created.",
                    "order_id" => $order_id
                ));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create order."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create order. Data is incomplete."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->status)) {
            $order->id = $data->id;
            $order->status = $data->status;

            if ($order->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Order was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update order."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update order. ID and status are required."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?> 