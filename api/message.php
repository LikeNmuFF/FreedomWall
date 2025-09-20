<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Include your database connection file
include("../includes/db.php");

// Fetch approved messages
$result = $conn->query("SELECT * FROM messages WHERE (status='approved' OR approved=1) AND created_at >= DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY created_at DESC LIMIT 500");
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Return the messages as a JSON object
echo json_encode($messages);

$conn->close();
?>