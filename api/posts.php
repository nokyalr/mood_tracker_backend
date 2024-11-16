<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT 
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
INNER JOIN moods ON posts.mood_id = moods.mood_id;
";

$stmt = $db->prepare($query);
$stmt->execute();

$posts = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $posts[] = $row;
}

echo json_encode($posts);
