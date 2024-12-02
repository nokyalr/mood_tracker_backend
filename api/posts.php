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
        if (isset($_GET['action']) && $_GET['action'] == 'add_comment') {
            addComment($db);
        } else if (isset($_GET['action']) && $_GET['action'] == 'delete_post') {
            deletePost($db);
        } else {
            createPost($db);
        }
        break;
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] == 'get_comments') {
            getComments($db);
        } else if (isset($_GET['action']) && $_GET['action'] == 'get_comment_count') {
            getCommentCount($db);
        } else if (isset($_GET['action']) && $_GET['action'] == 'get_first_comment') {
            getFirstComment($db);
        } else if (isset($_GET['action']) && $_GET['action'] == 'get_last_sub_mood') {
            getLastSubMood($db);
        } elseif (isset($_GET['action']) && $_GET['action'] == 'search_posts') {
            searchPosts($db);
        } elseif (isset($_GET['action']) && $_GET['action'] == 'get_posts_by_status') {
            $user_id = $_GET['user_id'];
            $is_posted = $_GET['is_posted'];
            getPostsByStatus($db, $user_id, $is_posted);
        } else {
            getPosts($db);
        }
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
        break;
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
            posts.user_id,
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
  
    if (empty($data['post_id']) || empty($data['content']) || empty($data['mood_score'])) {
        http_response_code(400);
        echo json_encode(["message" => "All fields are required"]);
        return;
    }
  
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

    if (empty($data['post_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "post_id is required"]);
        return;
    }

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

function searchPosts($db) {
    $query = $_GET['query'] ?? '';
    $user_id = $_GET['user_id'] ?? '';

    $searchQuery = "
        SELECT 
            posts.post_id, 
            posts.user_id,
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
            posts.is_posted = 1 AND (posts.user_id = :user_id OR friends.friend_id IS NOT NULL) AND (
                moods.mood_level LIKE :query OR 
                moods.mood_category LIKE :query OR 
                posts.content LIKE :query
            )
        ORDER BY 
            posts.updated_at DESC
    ";

    $stmt = $db->prepare($searchQuery);
    $searchTerm = '%' . $query . '%';
    $stmt->bindParam(':query', $searchTerm);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($posts);
}

function getPostsByStatus($db, $user_id, $is_posted) {
    if ($is_posted == 0) {
        // Fetch saved posts (is_posted = 0) only for the logged-in user
        $query = "
            SELECT 
                posts.post_id, 
                posts.user_id,
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
            WHERE 
                posts.is_posted = 0 AND posts.user_id = :user_id
            ORDER BY 
                posts.updated_at DESC
        ";
    } else {
        // Fetch shared posts (is_posted = 1) for the logged-in user and friends
        $query = "
            SELECT 
                posts.post_id, 
                posts.user_id,
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
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($posts);
}

function getComments($db) {
    if (empty($_GET['post_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "post_id is required"]);
        return;
    }

    $post_id = $_GET['post_id'];

    $query = "
        SELECT 
            comments.comment_id, 
            comments.comment, 
            comments.created_at, 
            users.name, 
            users.profile_picture 
        FROM 
            comments
        INNER JOIN users ON comments.user_id = users.user_id
        WHERE 
            comments.post_id = :post_id
        ORDER BY 
            comments.created_at ASC
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":post_id", $post_id);
    $stmt->execute();

    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($comments);
}

function getCommentCount($db) {
    if (empty($_GET['post_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "post_id is required"]);
        return;
    }

    $post_id = $_GET['post_id'];

    $query = "
        SELECT COUNT(*) as comment_count
        FROM comments
        WHERE post_id = :post_id
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":post_id", $post_id);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($result);
}

function getFirstComment($db) {
    if (empty($_GET['post_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "post_id is required"]);
        return;
    }

    $post_id = $_GET['post_id'];

    $query = "
        SELECT 
            comments.comment, 
            users.name 
        FROM 
            comments
        INNER JOIN users ON comments.user_id = users.user_id
        WHERE 
            comments.post_id = :post_id
        ORDER BY 
            comments.created_at ASC
        LIMIT 1
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":post_id", $post_id);
    $stmt->execute();

    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($comment) {
        http_response_code(200);
        echo json_encode($comment);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "No comments found"]);
    }
}

function addComment($db) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['post_id']) || empty($data['user_id']) || empty($data['comment'])) {
        http_response_code(400);
        echo json_encode(["message" => "All fields are required"]);
        return;
    }

    $query = "INSERT INTO comments (post_id, user_id, comment) VALUES (:post_id, :user_id, :comment)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":post_id", $data['post_id']);
    $stmt->bindParam(":user_id", $data['user_id']);
    $stmt->bindParam(":comment", $data['comment']);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Comment added successfully"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Error adding comment"]);
    }
}

function getLastSubMood($db) {
    $userId = $_GET['user_id'];

    $query = "
        SELECT 
            moods.mood_category AS sub_mood
        FROM 
            posts
        INNER JOIN 
            moods ON posts.mood_id = moods.mood_id
        WHERE 
            posts.user_id = :user_id
        ORDER BY 
            posts.created_at DESC
        LIMIT 1
    ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'No sub mood found']);
    }
}
?>