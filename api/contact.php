<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/ContactMessage.php';

$database = new Database();
$db = $database->getConnection();

$message = new ContactMessage($db);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $stmt = $message->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $messages_arr = array();
            $messages_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $message_item = array(
                    "id" => $id,
                    "name" => $name,
                    "email" => $email,
                    "subject" => $subject,
                    "message" => $message,
                    "status" => $status,
                    "created_at" => $created_at
                );

                array_push($messages_arr["records"], $message_item);
            }

            http_response_code(200);
            echo json_encode($messages_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No messages found."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (
            !empty($data->name) &&
            !empty($data->email) &&
            !empty($data->subject) &&
            !empty($data->message)
        ) {
            $message->name = $data->name;
            $message->email = $data->email;
            $message->subject = $data->subject;
            $message->message = $data->message;

            if ($message->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Message was sent successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to send message."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to send message. Data is incomplete."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id) && !empty($data->status)) {
            $message->id = $data->id;
            $message->status = $data->status;

            if ($message->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Message status was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update message status."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update message status. ID and status are required."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?> 