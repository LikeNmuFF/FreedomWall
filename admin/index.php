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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
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
                <img src="../assets/cictt.png" alt="Phoenix Logo" class="phoenix-logo">
            </div>
            <h1 class="phoenix-title">PHOENIX</h1>
            <p class="phoenix-subtitle">Admin Approval Center</p>
        </div>

        <div class="phoenix-card p-4">
            <?php if ($result->num_rows > 0): ?>
                <div class="d-flex align-items-center mb-4">
                    <span class="status-indicator"></span>
                    <h3 class="mb-0" style="color: var(--phoenix-accent); font-weight: 600;">
                        <i class="fas fa-envelope-open-text me-2"></i>
                        Pending Messages (<?php echo $result->num_rows; ?>)
                    </h3>
                </div>
                
                <div class="table-responsive">
                    <table class="table phoenix-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>Sender</th>
                                <th><i class="fas fa-book me-2"></i>Course</th>
                                <th><i class="fas fa-graduation-cap me-2"></i>Year</th>
                                <th><i class="fas fa-comment-alt me-2"></i>Message</th>
                                <th><i class="fas fa-clock me-2"></i>Submitted</th>
                                <th><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if ($row['is_anonymous']): ?>
                                        <span class="anonymous-badge">
                                            <i class="fas fa-user-secret me-1"></i>Anonymous
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
                                            <i class="fas fa-check me-1"></i>Approve
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                           class="phoenix-btn phoenix-btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this message?')">
                                            <i class="fas fa-trash me-1"></i>Delete
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
                        <i class="fas fa-inbox mb-3" style="font-size: 3rem; color: var(--phoenix-accent);"></i>
                        <h4 style="color: var(--phoenix-light); margin-bottom: 1rem;">All Clear!</h4>
                        <p class="mb-0">No pending messages at this time. The Phoenix watches over a peaceful realm.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <small style="color: var(--phoenix-accent); opacity: 0.7;">
                <i class="fas fa-sync-alt me-1"></i>Auto-refreshing every 5 seconds
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>