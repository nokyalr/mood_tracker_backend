<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
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
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'register') {
                registerUser($db);
            } elseif ($_GET['action'] == 'login') {
                loginUser($db);
            } elseif ($_GET['action'] == 'updateProfile') {
                updateProfile($db);
            } elseif ($_GET['action'] == 'updateAvatar') {
                updateAvatar($db);
            }
        }
        break;
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] == 'get_user') {
            getUser($db);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

function registerUser($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['username']) || empty($data['name']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(["message" => "All fields are required"]);
        return;
    }

    $passwordHash = password_hash((string)$data['password'], PASSWORD_BCRYPT);

    $query = "INSERT INTO users (username, name, password) VALUES (:username, :name, :password)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $data['username']);
    $stmt->bindParam(":name", $data['name']);
    $stmt->bindParam(":password", $passwordHash);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "User registered successfully"]);
    } else {
        $error = $stmt->errorInfo();
        http_response_code(400);
        echo json_encode(["message" => "Error registering user", "error" => $error]);
    }
}

function loginUser($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['username']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(["message" => "Username and password are required"]);
        return;
    }

    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $data['username']);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $isPasswordValid = password_verify((string)$data['password'], $user['password']);
        if ($isPasswordValid) {
            http_response_code(200);
            echo json_encode([
                "message" => "Login successful",
                "user_id" => $user['user_id'],
                "username" => $user['username'],
                "name" => $user['name'],
                "profile_picture" => $user['profile_picture']
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid password"]);
        }
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Invalid username"]);
    }
}

function getUser($db) {
    if (empty($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "user_id is required"]);
        return;
    }

    $user_id = $_GET['user_id'];
    $query = "SELECT user_id, username, name, profile_picture FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        http_response_code(200);
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "User not found"]);
    }
}

function updateProfile($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['user_id']) || empty($data['name']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(["message" => "All fields are required"]);
        return;
    }

    $query = "UPDATE users SET name = :name, password = :password WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":name", $data['name']);
    $stmt->bindParam(":password", password_hash($data['password'], PASSWORD_BCRYPT));
    $stmt->bindParam(":user_id", $data['user_id']);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "Profile updated successfully"]);
    } else {
        $error = $stmt->errorInfo();
        http_response_code(400);
        echo json_encode(["message" => "Error updating profile", "error" => $error]);
    }
}

function updateAvatar($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['user_id']) || empty($data['profile_picture'])) {
        http_response_code(400);
        echo json_encode(["message" => "User ID and Profile Picture are required"]);
        return;
    }

    $query = "UPDATE users SET profile_picture = :profile_picture WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":profile_picture", $data['profile_picture']);
    $stmt->bindParam(":user_id", $data['user_id']);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "Avatar updated successfully"]);
    } else {
        $error = $stmt->errorInfo();
        http_response_code(400);
        echo json_encode(["message" => "Error updating avatar", "error" => $error]);
    }
}
?>
