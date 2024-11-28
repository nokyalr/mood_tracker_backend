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
        } elseif ($_GET['action'] === 'fetch_pending_requests') {
            getPendingRequests($db);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Invalid action parameter"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Action parameter is required"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_GET['action']) && $_GET['action'] === 'add_friend') {
        addFriend($db);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'remove_friend') {
        removeFriend($db);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'accept_friend_request') {
        acceptFriendRequest($db);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'reject_friend_request') {
        rejectFriendRequest($db);
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

function addFriend($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['user_id']) && isset($data['friend_id'])) {
        $userId = $data['user_id'];
        $friendId = $data['friend_id'];

        $query = "SELECT * FROM friends WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':friend_id', $friendId);
        $stmt->execute();

        $existingFriend = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingFriend) {
            if ($existingFriend['status'] === 'accepted') {
                echo json_encode(['status' => 'already_friend']);
            } else {
                echo json_encode(['status' => 'pending', 'message' => 'Friend request is pending']);
            }
        } else {
            $query = "INSERT INTO friends (user_id, friend_id, status) VALUES (:user_id, :friend_id, 'pending')";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':friend_id', $friendId);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Friend request sent']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send friend request']);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    }
}

function removeFriend($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['user_id']) && isset($data['friend_id'])) {
        $userId = $data['user_id'];
        $friendId = $data['friend_id'];

        $query = "DELETE FROM friends WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':friend_id', $friendId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Friend removed successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove friend']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    }
}

function getPendingRequests($db) {
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
            users ON friends.user_id = users.user_id
        WHERE 
            friends.friend_id = :user_id 
            AND friends.status = 'pending'
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);

    if ($stmt->execute()) {
        $pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($pendingRequests)) {
            http_response_code(200);
            echo json_encode(["status" => "success", "pending_requests" => $pendingRequests]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No pending requests"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error fetching pending requests"]);
    }
}

// Accept friend request
function acceptFriendRequest($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['user_id']) && isset($data['friend_id'])) {
        $userId = $data['user_id'];
        $friendId = $data['friend_id'];

        $query = "UPDATE friends SET status = 'accepted' WHERE user_id = :friend_id AND friend_id = :user_id AND status = 'pending'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':friend_id', $friendId);

        if ($stmt->execute()) {
            $query2 = "INSERT INTO friends (user_id, friend_id, status) VALUES (:user_id, :friend_id, 'accepted')";
            $stmt2 = $db->prepare($query2);
            $stmt2->bindParam(':user_id', $userId);
            $stmt2->bindParam(':friend_id', $friendId);

            if ($stmt2->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Friend request accepted']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add reverse friend entry']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to accept friend request']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    }
}

function rejectFriendRequest($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['user_id']) && isset($data['friend_id'])) {
        $userId = $data['user_id'];
        $friendId = $data['friend_id'];

        $query = "DELETE FROM friends WHERE user_id = :friend_id AND friend_id = :user_id AND status = 'pending'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':friend_id', $friendId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Friend request rejected']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to reject friend request']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    }
}

?>