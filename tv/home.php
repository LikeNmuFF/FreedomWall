<?php
include("../includes/db.php");

// Clean up messages paghuman sa 3 hours for cleanliness
$conn->query("DELETE FROM messages WHERE created_at < DATE_SUB(NOW(), INTERVAL 12 HOUR) AND (status='approved' OR approved=1)");

// Fetch all approved messages
$result = $conn->query("SELECT * FROM messages WHERE (status='approved' OR approved=1) AND created_at >= DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY created_at DESC LIMIT 1000");
$messages = [];
while ($row = $result->fetch_assoc()) {
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
    <script src="../design/js/lordicon.js"></script>
    <title>Phoenix Freedom Wall</title>
</head>
<body>
    <div class="container">
        <header class="header">
        <div class="header-left">
            <img src="../assets/cictt.png" alt="Phoenix Logo" class="phoenix-logo">
            <lord-icon
                src="../design/json/sgqurkre.json"
                trigger="loop"
                stroke="bold"
                colors="primary:#eeca66,secondary:#c71f16"
                style="width:70px;height:70px">
            </lord-icon>

            <div class="title-group">
                <h1 class="phoenix-title">Phoenix Freedom Wall</h1>
                    Share your thoughts anonymously or with your name.
                </p>
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
                    <lord-icon
                        src="https://cdn.lordicon.com/nhpxiumc.json"
                        trigger="loop"
                        delay="0"
                        state="hover-watch-talk"
                        colors="primary:#000000,secondary:#911710,tertiary:#c79816,quaternary:#3a3347"
                        style="width:50px;height:50px">
                    
                    </lord-icon>
                   
                </span>
                <span class="expiry-info">Auto-expire: 12h</span>
                <span class="footer-credit">
                    <lord-icon
                        src="../design/json/xowsaqcr.json"
                        trigger="loop"
                        state="hover-draw"
                        colors="primary:#000000,secondary:#c7c116,tertiary:#911710"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <img src="../assets/exe-coun.png" alt="Phoenix Logo" class="execoun-logo">
                    CICTT Executive Council - Basilan State College 
                    <img src="../assets/bitXus.png" alt="Phoenix Logo" class="bitxus-logo">
                    BitXus Publication
                </span>
                
            </div>
        </footer>
    </div>

    <script>


    /* ---------- Utilities ---------- */
    const htmlspecialchars = (str) => {
        if (typeof str !== 'string') return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.replace(/[&<>"']/g, m => map[m]);
    };
    
    // --- UPDATED: Set default truncation length to 200 characters ---
    const truncate = (s, n = 200) => {
        if (!s) return '';
        return s.length > n ? s.slice(0, n) + '…' : s;
    };

    /* Try to parse several possible timestamp formats and return Date or null */
    const parseMessageTimestamp = (m) => {
        if (!m) return null;
       
        if (m.created_at_utc) {
            const d = new Date(m.created_at_utc);
            if (!isNaN(d)) return d;
        }
        
        if (m.created_at) {
            // try as-is (browser may parse "YYYY-MM-DD HH:MM:SS")
            let d = new Date(m.created_at);
            if (!isNaN(d)) return d;
            // try replacing space with 'T' and append Z (assume UTC)
            d = new Date(m.created_at.replace(' ', 'T') + 'Z');
            if (!isNaN(d)) return d;
        }
        return null;
    };

    /* ---------- UPDATED FUNCTION START ---------- */
    const formatTimeAgoFromMs = (ms) => {
        if (!ms || isNaN(ms)) return '—';
        const seconds = Math.floor((Date.now() - ms) / 1000);

        if (seconds < 0) return 'just now'; // Future timestamps
        if (seconds < 60) return `${seconds}s ago`; // 0-59 seconds
        if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`; // 1-59 minutes
        if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`; // 1-23 hours
        return `${Math.floor(seconds / 86400)}d ago`; // 1+ days
    };
    const messagesContainer = document.getElementById('messages-container');
    let allMessages = <?php echo json_encode($messages); ?> || [];
    let currentIndex = 0;
    const MIN_CARD_WIDTH = 240; 

    const normalizeMessages = (arr) => {
        return arr.map(m => {
            if (!m.created_at_utc) {
                const d = parseMessageTimestamp(m);
                if (d) m.created_at_utc = d.toISOString();
                else m.created_at_utc = null;
            } else {
                const d = new Date(m.created_at_utc);
                if (isNaN(d)) {
                    const d2 = parseMessageTimestamp(m);
                    m.created_at_utc = d2 ? d2.toISOString() : null;
                }
            }
            return m;
        });
    };
    allMessages = normalizeMessages(allMessages);

    const getLayoutConfig = () => {
        const containerWidth = messagesContainer.offsetWidth;
        const containerHeight = messagesContainer.offsetHeight;
        const gap = 16;
        const columns = Math.max(1, Math.floor(containerWidth / (MIN_CARD_WIDTH + gap)));
        const cardWidth = `calc(${100 / columns}% - ${gap}px)`;
        
        // Note: The height for calculation is based on the increased content size
        const cardHeight = 250; 
        const rows = Math.max(1, Math.floor(containerHeight / (cardHeight + gap)));
        const messagesPerPage = rows * columns;
        
        return { messagesPerPage, cardWidth };
    };

   
    const renderMessages = (messagesToDisplay) => {
        if (!messagesToDisplay || messagesToDisplay.length === 0) {
            if (!allMessages || allMessages.length === 0) {
                messagesContainer.innerHTML = `
                    <div class="empty-state">
                    <lord-icon
                        src="../design/json/czcsywgo.json"
                        trigger="loop"
                        colors="primary:#911710,secondary:#eee966,tertiary:#30c9e8,quaternary:#ebe6ef,quinary:#ffc738,senary:#f9c9c0"

                        style="width:250px;height:250px">
                    </lord-icon>
                        <h2 class="empty-title">Awaiting Messages</h2>
                        <p class="empty-subtitle">Share your thoughts and watch them appear here.</p>
                    </div>`;
                return;
            }
        }

        messagesContainer.innerHTML = '';
        const { cardWidth } = getLayoutConfig();

        messagesToDisplay.forEach((message, index) => {
            const isAnonymous = message.is_anonymous == 1;
            const rawName = message.student_name || '';
            // Truncate name to 15 chars for badge, regardless of default (200)
            const displayName = isAnonymous ? 'Anonymous' : truncate(rawName, 15); 
            const nameTitle = htmlspecialchars(rawName);

            // timestamp
            const parsed = parseMessageTimestamp(message);
            const tsMs = parsed ? parsed.getTime() : null;
            const timeHtml = formatTimeAgoFromMs(tsMs);

            const card = document.createElement('article');
            card.className = 'message-card';
            card.style.animationDelay = `${index * 0.05}s`;
            card.style.flexBasis = cardWidth;

            // --- UPDATED: Truncate message content to 200 characters (default in truncate function) ---
            const safeMessage = htmlspecialchars(truncate(message.message || ''));
            
            const course = htmlspecialchars(message.course || '');
            const year = htmlspecialchars(message.year_level || '');

            card.innerHTML = `
                <div class="message-text">${safeMessage}</div>
                <div class="message-meta">
                    <div class="sender-info">
                        ${isAnonymous ? 
                            `<span class="anonymous-badge">${displayName}</span>` :
                            `<span class="name-badge" title="${nameTitle}">${htmlspecialchars(displayName)}</span>`
                        }
                        <span class="course-badge">${course}</span>
                        <span class="year-badge">${year}</span>
                    </div>
                    <span class="message-time" data-ts="${tsMs || ''}">${timeHtml}</span>
                </div>`;

            messagesContainer.appendChild(card);
        });

        updateTimeElements();
    };

    /* Update only the times (efficient) */
    const updateTimeElements = () => {
        document.querySelectorAll('.message-time').forEach(el => {
            const ts = el.getAttribute('data-ts');
            const ms = ts ? parseInt(ts, 10) : null;
            el.textContent = formatTimeAgoFromMs(ms);
        });
    };

    /* ---------- Rotation / Fetch ---------- */
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
        }, 450);
    };

    const fetchNewMessages = async () => {
        try {
            const response = await fetch('get_messages.php');
            const newMessages = await response.json();
            if (!Array.isArray(newMessages)) return;

            const normalized = normalizeMessages(newMessages);

            const hasChanged = (allMessages.length !== normalized.length) ||
                (allMessages.length > 0 && normalized.length > 0 && allMessages[0].id !== normalized[0].id);

            if (hasChanged) {
                allMessages = normalized;
                const mc = document.getElementById('message-count');
                if (mc) mc.textContent = `Displaying ${allMessages.length} Messages`;
                currentIndex = 0;
                rotateMessages();
            } else {
                // If same message set, just update timestamps on-screen
                updateTimeElements();
            }
        } catch (err) {
            console.error('Failed to fetch messages:', err);
        }
    };

    /* ---------- Start ---------- */
    document.addEventListener('DOMContentLoaded', () => {
        rotateMessages();                     // show initial page
        setInterval(fetchNewMessages, 5000);  // poll for new messages
        setInterval(rotateMessages, 10000);   // rotate pages
        setInterval(updateTimeElements, 5000); // update time ago every 5s for more responsive seconds display
        window.addEventListener('resize', () => {
            currentIndex = 0;
            rotateMessages();
        });
    });
    </script>

</body>
</html>