<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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

    if (empty($data['user_id']) || empty($data['mood_id']) || empty($data['mood_score']) || empty($data['content']) || empty($data['post_date'])) {
        http_response_code(400);
        echo json_encode(["message" => "All fields are required"]);
        return;
    }

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
    if (empty($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "user_id is required"]);
        return;
    }

    $user_id = $_GET['user_id'];

    $query = "
        SELECT 
            posts.post_id, 
            users.name, 
            users.profile_picture, 
            moods.mood_category, 
            posts.content AS description, 
            posts.post_date AS date, 
            posts.mood_score, 
            posts.created_at AS time 
        FROM 
            posts
        INNER JOIN users ON posts.user_id = users.user_id
        INNER JOIN moods ON posts.mood_id = moods.mood_id
        LEFT JOIN friends ON (friends.user_id = :user_id AND friends.friend_id = posts.user_id AND friends.status = 'accepted')
        WHERE 
            posts.is_posted = 1 AND (posts.user_id = :user_id OR friends.friend_id IS NOT NULL)
        ORDER BY 
            posts.updated_at DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
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