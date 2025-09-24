<?php
include("../includes/db.php");

// Clean up messages older than 3 hours to keep content fresh
$conn->query("DELETE FROM messages WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 HOUR) AND (status='approved' OR approved=1)");

// Fetch all approved messages
$result = $conn->query("SELECT * FROM messages WHERE (status='approved' OR approved=1) AND created_at >= DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY created_at DESC LIMIT 1000");
$messages = [];
while ($row = $result->fetch_assoc()) {
    // **CHANGED**: Add a universal (UTC) timestamp for the browser to use
    $row['created_at_utc'] = gmdate('Y-m-d\TH:i:s\Z', strtotime($row['created_at']));
    $messages[] = $row;
}

$count = count($messages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../design/css/tv.css">
    <title>Phoenix Freedom Wall</title>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header-left">
                <img src="../assets/cictt.png" alt="Phoenix Logo" class="phoenix-logo">
                <div class="title-group">
                    <h1 class="phoenix-title">Phoenix Freedom Wall</h1>
                    <p class="phoenix-subtitle">Live Message Display</p>
                </div>
            </div>
        </header>

        <main id="messages-container" class="messages-container">
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
        const MIN_CARD_WIDTH = 280; // Minimum width for each card in pixels

        // **CHANGED**: Function to calculate how many cards can fit on screen
        const getLayoutConfig = () => {
            const containerWidth = messagesContainer.offsetWidth;
            const gap = 16; // Gap between cards
            const columns = Math.max(1, Math.floor(containerWidth / (MIN_CARD_WIDTH + gap)));
            const cardWidth = `calc(${100 / columns}% - ${gap}px)`;
            const messagesPerPage = Math.floor(messagesContainer.offsetHeight / 120) * columns; // Estimate rows
            return { messagesPerPage, cardWidth };
        };
        
        const renderMessages = (messagesToDisplay) => {
            if (messagesToDisplay.length === 0 && allMessages.length === 0) {
                messagesContainer.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon"></div>
                        <h2 class="empty-title">Awaiting Messages</h2>
                        <p class="empty-subtitle">Share your thoughts and watch them appear here.</p>
                    </div>`;
                return;
            }

            messagesContainer.innerHTML = '';
            const { cardWidth } = getLayoutConfig();

            messagesToDisplay.forEach((message, index) => {
                const isAnonymous = message.is_anonymous == 1;
                const nameHtml = isAnonymous 
                    ? `<span class="anonymous-badge">Anon</span>` 
                    : `<span class="sender-name">${htmlspecialchars(message.student_name)}</span>`;
                
                // **CHANGED**: Use the new UTC timestamp for accurate time calculation
                const timeDiff = Math.floor((new Date().getTime() - new Date(message.created_at_utc).getTime()) / 1000);
                const timeHtml = timeDiff < 60 ? 'just now' : 
                                 (timeDiff < 3600 ? `${Math.floor(timeDiff / 60)}m ago` : 
                                 `${Math.floor(timeDiff / 3600)}h ago`);

                const card = document.createElement('article');
                card.className = 'message-card';
                card.style.animationDelay = `${index * 0.05}s`;
                card.style.flexBasis = cardWidth; // Set the calculated width

                card.innerHTML = `
                    <div class="message-text">${htmlspecialchars(message.message)}</div>
                    <div class="message-meta">
                        <div class="sender-info">
                            ${nameHtml}
                            <span class="course-badge">${htmlspecialchars(message.course)}</span>
                            <span class="year-badge">${htmlspecialchars(message.year_level)}</span>
                        </div>
                        <span class="message-time">${timeHtml}</span>
                    </div>`;
                messagesContainer.appendChild(card);
            });
        };

        const rotateMessages = () => {
            if (document.hidden) return;
            
            const numMessages = allMessages.length;
            const { messagesPerPage } = getLayoutConfig();

            if (numMessages <= messagesPerPage) {
                renderMessages(allMessages);
                currentIndex = 0;
                return;
            }

            messagesContainer.style.opacity = '0';
            setTimeout(() => {
                const end = currentIndex + messagesPerPage;
                const messagesToDisplay = allMessages.slice(currentIndex, end);
                renderMessages(messagesToDisplay);
                messagesContainer.style.opacity = '1';
                currentIndex = end >= numMessages ? 0 : end;
            }, 500);
        };

        const fetchNewMessages = async () => {
            try {
                const response = await fetch('get_messages.php');
                const newMessages = await response.json();
                
                const hasChanged = (allMessages.length !== newMessages.length) || 
                    (allMessages.length > 0 && newMessages.length > 0 && allMessages[0].id !== newMessages[0].id);

                if (hasChanged) {
                    allMessages = newMessages;
                    document.getElementById('message-count').textContent = `Displaying ${allMessages.length} Messages`;
                    currentIndex = 0;
                    rotateMessages();
                }
            } catch (error) {
                console.error("Failed to fetch messages:", error);
            }
        };
        
        const htmlspecialchars = (str) => {
            if (typeof str !== 'string') return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return str.replace(/[&<>"']/g, m => map[m]);
        };
        
        document.addEventListener('DOMContentLoaded', () => {
            rotateMessages();
            setInterval(fetchNewMessages, 5000); 
            setInterval(rotateMessages, 10000);
            window.addEventListener('resize', () => {
                currentIndex = 0;
                rotateMessages();
            });
        });
    </script>
</body>
</html>