<?php
// PHP SCRIPT START
include("../includes/auth.php"); // protect page
include("../includes/db.php");

// Fetch pending messages
$result = $conn->query("SELECT * FROM messages WHERE status='pending' ORDER BY created_at DESC");

// Fetch the number of rows for the dynamic count in the header
$pending_count = $result->num_rows;
// PHP SCRIPT END
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phoenix Admin Dashboard</title>
    <link rel="stylesheet" href="../design/css/admin.index.all.min.css">
    <link rel="stylesheet" href="../design/css/admin.index.bootstrap.min.css">
    <link href="../design/css/admin.index.css2.css" rel="stylesheet">
    <link rel="stylesheet" href="../design/css/_admin.css">
    <script src="../design/js/admin.js"> </script>
    <script src="../design/js/_admin.lordicon.js"></script>
    <style>
        .phoenix-logo {
            width: 10%;
            height: 10%;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="phoenix-header">
            <div class="phoenix-icon">
                <img src="../assets/admin.cictt.png" alt="Phoenix Logo" class="phoenix-logo">
            </div>
            <h1 class="phoenix-title">CICTT PHOENIX</h1>
            <p class="phoenix-subtitle">Admin Approval Center</p>
        </div>

        <div id="refreshable-content" class="phoenix-card p-4">
            <?php if ($pending_count > 0): ?>
                <div class="d-flex align-items-center mb-4">
                    <span class="status-indicator"></span>
                    <h3 class="mb-0" style="color: var(--phoenix-accent); font-weight: 600;">
                    <lord-icon
                        src="../design/json/hwfggmas.json"
                        trigger="loop"
                        colors="primary:#c71f16,secondary:#e8b730"
                        style="width:50px;height:50px">
                    </lord-icon>
                        Pending Messages (<?php echo $pending_count; ?>)
                    </h3>
                </div>
                
                <div class="table-responsive">
                    <table class="table phoenix-table">
                        <thead>
                            <tr>
                                <th></i>Sender</th>
                                <th></i>Course</th>
                                <th></i>Year</th>
                                <th></i>Message</th>
                                <th></i>Submitted</th>
                                <th></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if ($row['is_anonymous']): ?>
                                        <span class="anonymous-badge">
                                            </i>Anonymous
                                        </span>
                                    <?php else: ?>
                                        <strong><?php echo htmlspecialchars($row['student_name']); ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="course-badge">
                                        <?php echo htmlspecialchars($row['course']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="year-badge">
                                        <?php echo htmlspecialchars($row['year_level']); ?>
                                    </span>
                                </td>
                                <td class="message-cell">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </td>
                                <td>
                                    <small style="color: var(--phoenix-accent);">
                                        <?php echo date('M j, Y', strtotime($row['created_at'])); ?><br>
                                        <?php echo date('g:i A', strtotime($row['created_at'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="approve?id=<?php echo $row['id']; ?>" 
                                           class="phoenix-btn phoenix-btn-success">
                                            </i>Approve
                                        </a>
                                        <a href="delete?id=<?php echo $row['id']; ?>" 
                                           class="phoenix-btn phoenix-btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this message?')">
                                            </i>Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="phoenix-alert">
                    <div class="phoenix-alert-content">
                        <b class="" style="font-size: 3rem; color: var(--phoenix-accent);">                   
                            <lord-icon
                                src="../design/json/abhwievu.json"
                                trigger="loop"
                                state="hover-conversation-alt"
                                colors="primary:#911710,secondary:#e8e230,tertiary:#e4e4e4,quaternary:#545454"
                                style="width:250px;height:250px">
                            </lord-icon>
                        </b>
                        <h4 style="color: var(--phoenix-light); margin-bottom: 1rem;">All Clear!</h4>
                        <p class="mb-0">No pending messages at this time. The Phoenix watches over a peaceful realm.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <small style="color: var(--phoenix-accent); opacity: 0.7;">
                </i>Auto-refreshing every 5 seconds
            </small>
        </div>
    </div>

    <script src="../design/js/admin.bootstrap.bundle.min.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>

    <script>
        const refreshableContent = document.getElementById('refreshable-content');
        const FETCH_INTERVAL = 5000; // 5 seconds

        const fetchAndUpdateMessages = async () => {
            try {
                // Fetch the current page content from the server
                const response = await fetch(window.location.href);
                const html = await response.text();

                // 1. Parse the new HTML content
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 2. Extract the content of the refreshable-content div from the new document
                const newContentElement = doc.getElementById('refreshable-content');

                if (newContentElement && refreshableContent) {
                    // 3. Smoothly replace the current content with the new content
                    refreshableContent.style.opacity = 0;
                    setTimeout(() => {
                        refreshableContent.innerHTML = newContentElement.innerHTML;
                        refreshableContent.style.opacity = 1;
                    }, 200); // Wait for a brief fade-out before changing content
                }

            } catch (error) {
                console.error("Error fetching new messages:", error);
                // Optionally show an error to the user
            }
        };

        // Start the polling interval
        document.addEventListener('DOMContentLoaded', () => {
            setInterval(fetchAndUpdateMessages, FETCH_INTERVAL);
        });
    </script>
</body>
</html>