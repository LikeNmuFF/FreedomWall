<?php
session_start();
include("../includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_id'] = $id;
            header("Location: index.php");
            exit();
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "❌ No such user.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phoenix Admin Login</title>
    <link rel="stylesheet" href="../design/css/login_bootstrap.min.css">
    <link rel="stylesheet" href="../design/css/login_all.min.css">
    <link href="../design/css/login_css2.css" rel="stylesheet">
    <link rel="stylesheet" href="../design/css/login.css">
    <script src="../design/js/login.js"></script>
</head>
<body>
    <div class="phoenix-card" id="loginCard">
        <div class="phoenix-title">🔥 PHOENIX 🔥</div>
        <div class="typing">Admin Login Portal</div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label"></i>Username:</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label"></i> Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="phoenix-btn"><i></i>Login</button>
        </form>
    </div>

</body>
</html>
