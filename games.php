<?php
header("Content-Type: application/json; charset=UTF-8");
include_once 'config.php';
include_once 'Game.php';

if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Access denied. Please login."));
    exit;
}

$database = new Database();
$db = $database->getConnection();
$game = new Game($db);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"));

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $game->id = $_GET['id'];
            $game->readOne();
            
            if($game->title != null) {
                $game_arr = array(
                    "id" => $game->id,
                    "title" => $game->title,
                    "description" => $game->description,
                    "genre" => $game->genre,
                    "release_date" => $game->release_date,
                    "price" => $game->price,
                    "image_url" => $game->image_url,
                    "created_by" => $game->created_by,
                    "created_at" => $game->created_at
                );
                echo json_encode($game_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Game not found."));
            }
        } else if(isset($_GET['search'])) {
            $stmt = $game->search($_GET['search']);
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $games_arr = array();
                $games_arr["records"] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($games_arr["records"], $row);
                }
                echo json_encode($games_arr);
            } else {
                echo json_encode(array("message" => "No games found."));
            }
        } else {
            $stmt = $game->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $games_arr = array();
                $games_arr["records"] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($games_arr["records"], $row);
                }
                echo json_encode($games_arr);
            } else {
                echo json_encode(array("message" => "No games found."));
            }
        }
        break;

    case 'POST':
        if($_SESSION['account_type'] == 'player') {
            http_response_code(403);
            echo json_encode(array("message" => "Only admins and moderators can create games."));
            break;
        }
        
        if(!empty($input->title) && !empty($input->description)) {
            $game->title = $input->title;
            $game->description = $input->description;
            $game->genre = $input->genre;
            $game->release_date = $input->release_date;
            $game->price = $input->price;
            $game->image_url = $input->image_url;
            $game->created_by = $_SESSION['user_id'];
            
            if($game->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Game was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create game."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create game. Data is incomplete."));
        }
        break;

    case 'PUT':
        if($_SESSION['account_type'] == 'player') {
            http_response_code(403);
            echo json_encode(array("message" => "Only admins and moderators can update games."));
            break;
        }
        
        $game->id = $input->id;
        $game->readOne();
        
        if($game->title != null) {
            $game->title = $input->title;
            $game->description = $input->description;
            $game->genre = $input->genre;
            $game->release_date = $input->release_date;
            $game->price = $input->price;
            $game->image_url = $input->image_url;
            
            if($game->update()) {
                echo json_encode(array("message" => "Game was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update game."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Game not found."));
        }
        break;

    case 'DELETE':
        if($_SESSION['account_type'] != 'admin') {
            http_response_code(403);
            echo json_encode(array("message" => "Only admins can delete games."));
            break;
        }
        
        $game->id = $input->id;
        $game->readOne();
        
        if($game->title != null) {
            if($game->delete()) {
                echo json_encode(array("message" => "Game was deleted."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete game."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Game not found."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
}
?>