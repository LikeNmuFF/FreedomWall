<?php
// Include the database connection file
include("../includes/db.php");

// Set the number of messages to add
$num_messages = 400;
$messages_added = 0;

echo "<h2>Adding $num_messages new messages...</h2>";

// Start a transaction for efficiency
$conn->begin_transaction();

try {
    // Loop to insert 50 messages
    for ($i = 1; $i <= $num_messages; $i++) {
        $student_name = "Test User " . $i;
        $year_level = "Year " . ($i % 4 + 1); // Cycle through years 1 to 4
        $course = "BSIT";
        $message_text = "This is a test message to fill up the freedom wall. Message number: " . $i;
        $is_anonymous = ($i % 5 == 0) ? 1 : 0; // Make every 5th message anonymous

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO messages (student_name, year_level, course, message, is_anonymous, approved, status, created_at) VALUES (?, ?, ?, ?, ?, 1, 'approved', NOW())");
        
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters and execute
        $stmt->bind_param("ssssi", $student_name, $year_level, $course, $message_text, $is_anonymous);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $messages_added++;
        }
        $stmt->close();
    }

    // Commit the transaction
    $conn->commit();
    echo "<p>✅ Successfully added $messages_added messages to the database.</p>";
    echo "<p>Now, open your <a href='index.php'>home</a> page to see the messages appear on the TV display.</p>";

} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    echo "<p style='color:red;'>❌ Error adding messages: " . $e->getMessage() . "</p>";
}

// Close the connection
$conn->close();
?>