<?php
include("../includes/db.php");

// Clean up messages older than 3 hours to keep content fresh
$conn->query("DELETE FROM messages WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 HOUR) AND (status='approved' OR approved=1)");

// Fetch all approved messages for display
$result = $conn->query("SELECT * FROM messages WHERE (status='approved' OR approved=1) AND created_at >= DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY created_at DESC LIMIT 1000");
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$count = count($messages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="refresh" content="5">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../design/css/tv.css">
    <title>Phoenix Freedom Wall</title>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo-container">
                <img src="../assets/cictt.png" alt="Phoenix Logo" class="phoenix-logo">
            </div>
            <h1 class="phoenix-title">Phoenix Freedom Wall</h1>
            <p class="phoenix-subtitle">Live Message Display</p>
        </header>

        <main id="messages-container" class="messages-container">
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <div class="empty-icon"></div>
                    <h2 class="empty-title">Awaiting Messages</h2>
                    <p class="empty-subtitle">
                        The Phoenix Freedom Wall is ready to display your messages.
                        Share your thoughts and watch them appear here for everyone to see.
                        Messages automatically expire after 3 hours to keep content fresh.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <article class="message-card">
                        <div class="message-content">
                            <div class="message-text">
                                <?php echo htmlspecialchars($message['message']); ?>
                            </div>
                            <div class="message-meta">
                                <div class="sender-info">
                                    <?php if ($message['is_anonymous']): ?>
                                        <span class="anonymous-badge">
                                            <i class="fas fa-user-secret"></i> Anon
                                        </span>
                                    <?php else: ?>
                                        <span class="sender-name" title="<?php echo htmlspecialchars($message['student_name']); ?>">
                                            <?php echo htmlspecialchars($message['student_name']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($message['course']): ?>
                                        <span class="course-badge" title="<?php echo htmlspecialchars($message['course']); ?>">
                                            <?php echo htmlspecialchars($message['course']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="year-badge">
                                        <?php echo htmlspecialchars($message['year_level']); ?>
                                    </span>
                                </div>
                                <div class="message-time">
                                    <?php
                                        $time_diff = time() - strtotime($message['created_at']);
                                        if ($time_diff < 60) {
                                            echo 'now';
                                        } elseif ($time_diff < 3600) {
                                            echo floor($time_diff / 60) . 'm';
                                        } else {
                                            echo floor($time_diff / 3600) . 'h';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>

        <footer class="footer">
            <div class="footer-text">
                <span id="message-count">Displaying <?php echo $count; ?> Messages</span>
                <span class="live-indicator">
                    <span class="live-dot"></span>
                    LIVE
                </span>
                <span class="expiry-info">Auto-expire: 3h</span>
            </div>
        </footer>
    </div>
</body>
</html>