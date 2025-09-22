<?php
include("../includes/auth.php"); // protect page
include("../includes/db.php");

// Fetch pending messages
$result = $conn->query("SELECT * FROM messages WHERE status='pending' ORDER BY created_at DESC");
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
    <meta http-equiv="refresh" content="5">
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
            <h1 class="phoenix-title">PHOENIX</h1>
            <p class="phoenix-subtitle">Admin Approval Center</p>
        </div>

        <div class="phoenix-card p-4">
            <?php if ($result->num_rows > 0): ?>
                <div class="d-flex align-items-center mb-4">
                    <span class="status-indicator"></span>
                    <h3 class="mb-0" style="color: var(--phoenix-accent); font-weight: 600;">
                        <b>📩</b>
                        Pending Messages (<?php echo $result->num_rows; ?>)
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
                                        <a href="approve.php?id=<?php echo $row['id']; ?>" 
                                           class="phoenix-btn phoenix-btn-success">
                                            </i>Approve
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" 
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
                        <b class="" style="font-size: 3rem; color: var(--phoenix-accent);">📩</b>
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

</body>
</html>