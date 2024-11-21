<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'get_friends') {
            getFriends($db);
        } elseif ($_GET['action'] === 'get_all_users') {
            getAllUsers($db);
        } elseif ($_GET['action'] === 'get_friend_ids') {
            getFriendIds($db);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid action parameter"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}

function getFriends($db) {
    if (empty($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "user_id is required"]);
        return;
    }

    $user_id = $_GET['user_id'];
    $query = "
        SELECT 
            users.user_id, 
            users.username, 
            users.name, 
            users.profile_picture
        FROM 
            friends
        INNER JOIN 
            users ON friends.friend_id = users.user_id
        WHERE 
            friends.user_id = :user_id 
            AND friends.status = 'accepted'
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);

    if ($stmt->execute()) {
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($friends)) {
            http_response_code(200);
            echo json_encode(["status" => "success", "friends" => $friends]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No friends found"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error fetching friends"]);
    }
}

function getAllUsers($db) {
    $query = "
        SELECT 
            user_id, 
            username, 
            name, 
            profile_picture
        FROM 
            users
    ";

    $stmt = $db->prepare($query);

    if ($stmt->execute()) {
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($users)) {
            http_response_code(200);
            echo json_encode(["status" => "success", "users" => $users]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No users found"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error fetching users"]);
    }
}

function getFriendIds($db) {
    if (empty($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "user_id is required"]);
        return;
    }

    $user_id = $_GET['user_id'];
    $query = "
        SELECT friend_id
        FROM friends
        WHERE user_id = :user_id AND status = 'accepted'
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);

    if ($stmt->execute()) {
        $friendIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        http_response_code(200);
        echo json_encode(["status" => "success", "friend_ids" => $friendIds]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error fetching friend IDs"]);
    }
}
?>
