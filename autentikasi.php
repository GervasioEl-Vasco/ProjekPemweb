<?php
header("Content-Type: application/json; charset=UTF-8");
include_once 'config.php';
include_once 'User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($data->action)) {
        switch($data->action) {
            case 'register':
                if(!empty($data->username) && !empty($data->email) && !empty($data->password)) {
                    
                    $user->username = $data->username;
                    $user->email = $data->email;
                    $user->password = $data->password;
                    $user->account_type = $data->account_type ?? 'player';
                    
                    if($user->usernameExists()) {
                        http_response_code(400);
                        echo json_encode(array("message" => "Username already exists."));
                    } else {
                        if($user->register()) {
                            http_response_code(201);
                            echo json_encode(array("message" => "User was created successfully."));
                        } else {
                            http_response_code(503);
                            echo json_encode(array("message" => "Unable to create user."));
                        }
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "Unable to create user. Data is incomplete."));
                }
                break;

            case 'login':
                if(!empty($data->username) && !empty($data->password)) {
                    $user->username = $data->username;
                    $user->password = $data->password;
                    
                    if($user->login()) {
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['username'] = $user->username;
                        $_SESSION['account_type'] = $user->account_type;
                        
                        echo json_encode(array(
                            "message" => "Login successful.",
                            "user_id" => $user->id,
                            "username" => $user->username,
                            "account_type" => $user->account_type
                        ));
                    } else {
                        http_response_code(401);
                        echo json_encode(array("message" => "Login failed."));
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "Unable to login. Data is incomplete."));
                }
                break;

            case 'logout':
                session_destroy();
                echo json_encode(array("message" => "Logged out successfully."));
                break;

            default:
                http_response_code(400);
                echo json_encode(array("message" => "Invalid action."));
        }
    }
}
?>