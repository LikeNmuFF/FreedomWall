<?php
// PHP SCRIPT START
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
// PHP SCRIPT END
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


    /* ---------- Utilities ---------- */
    const htmlspecialchars = (str) => {
        if (typeof str !== 'string') return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.replace(/[&<>"']/g, m => map[m]);
    };
    const truncate = (s, n) => {
        if (!s) return '';
        return s.length > n ? s.slice(0, n) + '…' : s;
    };

    /* Try to parse several possible timestamp formats and return Date or null */
    const parseMessageTimestamp = (m) => {
        if (!m) return null;
        // 1) created_at_utc (ISO with Z) — preferred
        if (m.created_at_utc) {
            const d = new Date(m.created_at_utc);
            if (!isNaN(d)) return d;
        }
        // 2) server `created_at` (e.g. "2025-09-20 12:34:56")
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

        if (seconds < 0) return 'just now'; // Handles clock skew (server time is ahead of client)
        if (seconds === 0) return 'just now'; // Only show "just now" for 0 seconds
        if (seconds < 60) return `${seconds}s ago`; // Show seconds from 1s onwards
        if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`; // Show minutes
        if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`; // Show hours
        return `${Math.floor(seconds / 86400)}d ago`; // Show days
    };
    /* ---------- UPDATED FUNCTION END ---------- */

    /* ---------- DOM + Data ---------- */
    const messagesContainer = document.getElementById('messages-container');
    let allMessages = <?php echo json_encode($messages); ?> || [];
    let currentIndex = 0;
    const MIN_CARD_WIDTH = 240; 

    /* Ensure initial server messages have created_at_utc (they do in your PHP, but we normalize anyway) */
    const normalizeMessages = (arr) => {
        return arr.map(m => {
            // keep as-is if created_at_utc present & valid
            if (!m.created_at_utc) {
                const d = parseMessageTimestamp(m);
                if (d) m.created_at_utc = d.toISOString();
                else m.created_at_utc = null;
            } else {
                // ensure it's parseable; if not, try fallback
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

    /* ---------- Layout helpers ---------- */
    const getLayoutConfig = () => {
        const containerWidth = messagesContainer.offsetWidth;
        const containerHeight = messagesContainer.offsetHeight;
        const gap = 16;
        const columns = Math.max(1, Math.floor(containerWidth / (MIN_CARD_WIDTH + gap)));
        const cardWidth = `calc(${100 / columns}% - ${gap}px)`;
        
        // Calculate rows that can fit in the visible area
        const cardHeight = 150; // Max height from CSS
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
            const displayName = isAnonymous ? 'Anonymous' : truncate(rawName, 15); // Increased to 15 chars for better display
            const nameTitle = htmlspecialchars(rawName);

            // timestamp
            const parsed = parseMessageTimestamp(message);
            const tsMs = parsed ? parsed.getTime() : null;
            const timeHtml = formatTimeAgoFromMs(tsMs);

            const card = document.createElement('article');
            card.className = 'message-card';
            card.style.animationDelay = `${index * 0.05}s`;
            card.style.flexBasis = cardWidth;

            const safeMessage = htmlspecialchars(message.message || '');
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

        // run a time update right away (ensures seconds tick)
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