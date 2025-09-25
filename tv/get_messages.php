<?php
include("../includes/db.php");

header('Content-Type: application/json');

// Fetch the last 1000 approved messages
$sql = "SELECT id, student_name, year_level, course, message, is_anonymous, created_at 
        FROM messages 
        WHERE status = 'approved' 
        ORDER BY created_at DESC 
        LIMIT 1000";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // Add a guaranteed UTC ISO 8601 field for JS parsing
    $row['created_at_utc'] = gmdate('Y-m-d\TH:i:s\Z', strtotime($row['created_at']));
    $messages[] = $row;
}

echo json_encode($messages);

$stmt->close();
$conn->close();
?>
