<?php
header('Content-Type: application/json');
include("../includes/db.php");

// Fetch approved messages within last 5 hours
$sql = "SELECT student_name, year_level, course, message, is_anonymous, created_at 
        FROM messages 
        WHERE status = 'approved' 
        AND created_at >= NOW() - INTERVAL 3 HOUR
        ORDER BY created_at DESC";
$result = $conn->query($sql);

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>