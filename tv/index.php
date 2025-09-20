<?php
include("../includes/db.php");

// Clean up messages older than 3 hours to keep content fresh
$conn->query("DELETE FROM messages WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 HOUR) AND (status='approved' OR approved=1)");

// Fetch all approved messages for rotation logic
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

    <script>
        const messagesContainer = document.getElementById('messages-container');
        let allMessages = <?php echo json_encode($messages); ?>;
        let currentIndex = 0;

        const messagesPerPage = () => {
            const containerWidth = messagesContainer.offsetWidth;
            const containerHeight = messagesContainer.offsetHeight;
            const gap = 16;
            const minCardWidth = 250; 
            const minCardHeight = 100;
            
            const numColumns = Math.max(1, Math.floor(containerWidth / (minCardWidth + gap)));
            const numRows = Math.max(1, Math.floor(containerHeight / (minCardHeight + gap)));
            
            return numColumns * numRows;
        };
        
        const renderMessages = (messagesToDisplay) => {
            if (messagesToDisplay.length === 0) {
                messagesContainer.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon"></div>
                        <h2 class="empty-title">Awaiting Messages</h2>
                        <p class="empty-subtitle">
                            The Phoenix Freedom Wall is ready to display your messages.
                            Share your thoughts and watch them appear here for everyone to see.
                            Messages automatically expire after 3 hours to keep content fresh.
                        </p>
                    </div>`;
                return;
            }

            messagesContainer.innerHTML = '';
            messagesToDisplay.forEach((message, index) => {
                const isAnonymous = message.is_anonymous == 1;
                const nameHtml = isAnonymous ?
                    `<span class="anonymous-badge"><i class="fas fa-user-secret"></i> Anon</span>` :
                    `<span class="sender-name" title="${htmlspecialchars(message.student_name)}">${htmlspecialchars(message.student_name)}</span>`;
                
                const courseHtml = message.course ?
                    `<span class="course-badge" title="${htmlspecialchars(message.course)}">${htmlspecialchars(message.course)}</span>` :
                    '';

                const timeDiff = Math.floor((new Date().getTime() - new Date(message.created_at).getTime()) / 1000);
                const timeHtml = timeDiff < 60 ? 'now' : 
                                 (timeDiff < 3600 ? `${Math.floor(timeDiff / 60)}m` : 
                                 `${Math.floor(timeDiff / 3600)}h`);

                const messageHtml = `
                    <article class="message-card" style="animation-delay: ${index * 0.05}s">
                        <div class="message-content">
                            <div class="message-text">
                                ${htmlspecialchars(message.message)}
                            </div>
                            <div class="message-meta">
                                <div class="sender-info">
                                    ${nameHtml}
                                    ${courseHtml}
                                    <span class="year-badge">
                                        ${htmlspecialchars(message.year_level)}
                                    </span>
                                </div>
                                <div class="message-time">
                                    ${timeHtml}
                                </div>
                            </div>
                        </div>
                    </article>`;
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            });
        };

        const rotateMessages = () => {
            const numMessages = allMessages.length;
            const limit = messagesPerPage();

            if (numMessages <= limit) {
                renderMessages(allMessages);
                return;
            }

            messagesContainer.style.opacity = '0';
            setTimeout(() => {
                const end = currentIndex + limit;
                let messagesToDisplay = allMessages.slice(currentIndex, end);
                if (messagesToDisplay.length < limit) {
                    messagesToDisplay = messagesToDisplay.concat(allMessages.slice(0, limit - messagesToDisplay.length));
                }
                
                renderMessages(messagesToDisplay);
                messagesContainer.style.opacity = '1';
                currentIndex = end >= numMessages ? 0 : end;
            }, 500);
        };

        const fetchNewMessages = async () => {
            try {
                const response = await fetch('api/messages.php');
                const newMessages = await response.json();
                allMessages = newMessages;
                document.getElementById('message-count').textContent = `Displaying ${allMessages.length} Messages`;
                rotateMessages();
            } catch (error) {
                console.error("Failed to fetch messages:", error);
            }
        };
        
        const htmlspecialchars = (str) => {
            if (typeof str !== 'string') {
                return '';
            }
            return str.replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
        };
        
        document.addEventListener('DOMContentLoaded', () => {
            renderMessages(allMessages.slice(0, messagesPerPage()));
            // Fetch messages more frequently to keep it live, but rotate slower for readability
            setInterval(fetchNewMessages, 20000); 
            setInterval(rotateMessages, 15000);
        });

        window.addEventListener('resize', () => {
            rotateMessages();
        });
    </script>
</body>
</html>