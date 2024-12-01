<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        if (isset($_GET['user_id'])) {
            if (isset($_GET['query'])) {
                searchSuggestions($db, $_GET['query']);
            } else {
                getSuggestionsBySubMood($db, $_GET['user_id']);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "user_id is required"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
}

function getSuggestionsBySubMood($db, $user_id) {
    // Get the last sub mood of the user
    $subMoodQuery = "
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
    $subMoodStmt = $db->prepare($subMoodQuery);
    $subMoodStmt->bindParam(':user_id', $user_id);
    $subMoodStmt->execute();
    $subMoodResult = $subMoodStmt->fetch(PDO::FETCH_ASSOC);

    if ($subMoodResult) {
        $subMood = $subMoodResult['sub_mood'];

        // Get suggestions based on the last sub mood
        $suggestionQuery = "
            SELECT 
                suggestion_id, 
                suggestion_text, 
                description, 
                link_to_article 
            FROM 
                activity_suggestions
            WHERE 
                mood_category = :sub_mood
        ";
        $suggestionStmt = $db->prepare($suggestionQuery);
        $suggestionStmt->bindParam(':sub_mood', $subMood);
        $suggestionStmt->execute();

        $suggestions = $suggestionStmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode($suggestions);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'No sub mood found']);
    }
}

function searchSuggestions($db, $query) {
    $searchQuery = "
        SELECT 
            suggestion_id, 
            suggestion_text, 
            description, 
            link_to_article 
        FROM 
            activity_suggestions
        WHERE 
            suggestion_text LIKE :query OR description LIKE :query
    ";
    $stmt = $db->prepare($searchQuery);
    $searchTerm = '%' . $query . '%';
    $stmt->bindParam(':query', $searchTerm);
    $stmt->execute();

    $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode($suggestions);
}
?>