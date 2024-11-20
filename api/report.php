<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($request_method === 'GET') {
    if ($action === 'getMoodSummary') {
        getMoodSummary($db);
    } elseif ($action === 'getMoodDates') {
        getMoodDates($db);
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid action']);
    }
}

function getMoodSummary($db) {
    $userId = $_GET['user_id'];
    $month = $_GET['month'];
    $year = $_GET['year'];

    $query = "
        SELECT 
            COUNT(CASE WHEN mood_score = 1 THEN 1 END) AS score_1,
            COUNT(CASE WHEN mood_score = 2 THEN 1 END) AS score_2,
            COUNT(CASE WHEN mood_score = 3 THEN 1 END) AS score_3,
            COUNT(CASE WHEN mood_score = 4 THEN 1 END) AS score_4,
            COUNT(CASE WHEN mood_score = 5 THEN 1 END) AS score_5
        FROM posts
        WHERE user_id = :user_id
          AND MONTH(post_date) = :month
          AND YEAR(post_date) = :year
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);

    if ($stmt->execute()) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to fetch data']);
    }
}

function getMoodDates($db) {
    $userId = $_GET['user_id'];
    $month = $_GET['month'];
    $year = $_GET['year'];

    $query = "
        SELECT DISTINCT post_date 
        FROM posts 
        WHERE user_id = :user_id 
          AND MONTH(post_date) = :month 
          AND YEAR(post_date) = :year
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);

    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode($result);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to fetch data']);
    }
}
?>
