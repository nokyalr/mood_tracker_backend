<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'POST':
        createPost($db);
        break;
    case 'GET':
        getPosts($db);
        break;
    case 'PUT':
        updatePost($db);
        break;
    case 'DELETE':
        deletePost($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
}

function createPost($db) {
    $data = json_decode(file_get_contents("php://input"), true);
    $query = "INSERT INTO posts (user_id, mood_id, mood_score, content, is_posted, post_date) 
              VALUES (:user_id, :mood_id, :mood_score, :content, :is_posted, :post_date)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $data['user_id']);
    $stmt->bindParam(":mood_id", $data['mood_id']);
    $stmt->bindParam(":mood_score", $data['mood_score']);
    $stmt->bindParam(":content", $data['content']);
    $stmt->bindParam(":is_posted", $data['is_posted']);
    $stmt->bindParam(":post_date", $data['post_date']);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Post created successfully"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Error creating post"]);
    }
}

function getPosts($db) {
    $query = "SELECT * FROM posts";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($posts);
}

function updatePost($db) {
    $data = json_decode(file_get_contents("php://input"), true);
    $query = "UPDATE posts SET content = :content, mood_score = :mood_score WHERE post_id = :post_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":content", $data['content']);
    $stmt->bindParam(":mood_score", $data['mood_score']);
    $stmt->bindParam(":post_id", $data['post_id']);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "Post updated successfully"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Error updating post"]);
    }
}

function deletePost($db) {
    $data = json_decode(file_get_contents("php://input"), true);
    $query = "DELETE FROM posts WHERE post_id = :post_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":post_id", $data['post_id']);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "Post deleted successfully"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Error deleting post"]);
    }
}
?>
