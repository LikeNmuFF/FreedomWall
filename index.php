<?php
session_start();
include("includes/db.php");

// Set default values for form fields, or clear them after a success
$name = '';
$year = $_POST['year'] ?? '';
$course = $_POST['course'] ?? '';
$anonymous = isset($_POST['anonymous']) ? 1 : 0;
$message = '';

$success = '';
$error = '';

// Handle submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Spam protection: 60 seconds
    if (isset($_SESSION['last_post_time']) && (time() - $_SESSION['last_post_time'] < 60)) {
        $error = " Please wait 60 seconds before posting again.";
        // Retain form values on error
        $name = $_POST['name'] ?? '';
        $year = $_POST['year'] ?? '';
        $course = $_POST['course'] ?? '';
        $anonymous = isset($_POST['anonymous']) ? 1 : 0;
        $message = $_POST['message'] ?? '';
    } else {
        // Validate inputs
        $name = $_POST['name'] ?? ''; // Re-set name for validation
        $message = $_POST['message'] ?? ''; // Re-set message for validation
        $message_length = strlen($message);

        if ($anonymous && !empty($name)) {
            $error = "❌ You cannot enter a name if posting anonymously.";
        } elseif (!$anonymous && empty($name)) {
            $error = "❌ Please enter your name or select anonymous.";
        } elseif (strlen($name) > 10) {
            $error = "❌ Name must be 10 characters or less.";
        } elseif ($message_length > 50) {
            $error = "❌ Message must be 50 characters or less.";
        } else {
            // Insert into DB
            $stmt = $conn->prepare("INSERT INTO messages (student_name, year_level, course, message, is_anonymous, created_at, approved) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
            $stmt->bind_param("ssssi", $name, $year, $course, $message, $anonymous);

            if ($stmt->execute()) {
                $_SESSION['last_post_time'] = time();
                $_SESSION['success_message'] = "Message submitted! Pending admin approval.";
                // Redirect after successful submission
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                $error = "⚠️ Error saving message. Please try again.";
            }
            $stmt->close();
        }
    }
}


if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="design/js/lordicon.js"></script>
    <title>Phoenix Message Portal</title>
    
    <style>
       
        :root {
            --phoenix-primary: #FF6B35;
            --phoenix-secondary: #F7931E;
            --phoenix-accent: #FFD23F;
            --phoenix-dark: #2C1810;
            --phoenix-darker: #1A0F0A;
            --phoenix-light: #FFF8F0;
            --phoenix-shadow: rgba(255, 107, 53, 0.3);
            --phoenix-glow: rgba(255, 107, 53, 0.6);
        }

        /* Use system fonts for offline compatibility */
        @font-face {
            font-family: 'SystemFont';
            src: local('Segoe UI'), local('Arial'), local('Helvetica'), local('sans-serif');
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'SystemFont', sans-serif;
            background: linear-gradient(135deg, var(--phoenix-darker) 0%, var(--phoenix-dark) 50%, #8B2635 100%);
            min-height: 100vh;
            color: var(--phoenix-light);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse at center, rgba(255, 107, 53, 0.1) 0%, transparent 70%);
            pointer-events: none;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            padding-left: 1rem;
            padding-right: 1rem;
            max-width: 960px;
            margin: 0 auto;
        }

        .py-5 {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .phoenix-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 0;
        }

        .phoenix-title {
            font-family: 'SystemFont', monospace;
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--phoenix-primary), var(--phoenix-accent), var(--phoenix-secondary));
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: phoenixGlow 3s ease-in-out infinite alternate, gradientShift 4s ease-in-out infinite;
            text-shadow: 0 0 30px var(--phoenix-glow);
            margin-bottom: 0.5rem;
        }

        .phoenix-subtitle {
            font-size: 1.1rem;
            color: var(--phoenix-accent);
            opacity: 0.8;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .phoenix-icon {
            font-size: 3.5rem;
            color: var(--phoenix-primary);
            margin-bottom: 1rem;
            animation: phoenixRise 2s ease-in-out infinite alternate;
            filter: drop-shadow(0 0 20px var(--phoenix-glow));
        }

        .phoenix-card {
            background: rgba(44, 24, 16, 0.8);
            border-radius: 20px;
            border: 2px solid rgba(255, 107, 53, 0.3);
            backdrop-filter: blur(20px);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 107, 53, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            padding: 2.5rem;
        }

        .phoenix-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 107, 53, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .phoenix-card:hover::before {
            left: 100%;
        }

        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 2rem; }

        .form-label {
            color: var(--phoenix-accent);
            font-weight: 600;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control, .form-select {
            display: block;
            width: 100%;
            background: rgba(44, 24, 16, 0.6);
            border: 2px solid rgba(255, 107, 53, 0.3);
            border-radius: 15px;
            color: var(--phoenix-light);
            padding: 1rem 1.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(44, 24, 16, 0.8);
            border-color: var(--phoenix-primary);
            box-shadow: 0 0 0 0.25rem var(--phoenix-shadow);
            color: var(--phoenix-light);
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255, 248, 240, 0.5);
        }

        .form-select option {
            background: var(--phoenix-dark);
            color: var(--phoenix-light);
        }

        .form-check {
            margin: 1.5rem 0;
            padding: 1.2rem 1.5rem;
            background: rgba(255, 107, 53, 0.1);
            border-radius: 15px;
            border: 1px solid rgba(255, 107, 53, 0.2);
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check:hover {
            background: rgba(255, 107, 53, 0.15);
            border-color: rgba(255, 107, 53, 0.3);
        }

        /* Refactored Checkbox Design */
        .form-check-input {
            background-color: rgba(44, 24, 16, 0.6);
            border: 2px solid var(--phoenix-primary);
            border-radius: 6px;
            width: 20px;
            height: 20px;
            min-width: 20px;
            min-height: 20px;
            margin: 0;
            flex-shrink: 0;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .form-check-input:checked {
            background-color: var(--phoenix-primary);
            border-color: var(--phoenix-primary);
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 16 16' fill='%23FFF' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3E%3C/svg%3E");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
        }

        .form-check-input:focus {
            border-color: var(--phoenix-accent);
            box-shadow: 0 0 0 3px var(--phoenix-shadow);
            outline: none;
        }

        .form-check-label {
            color: var(--phoenix-light);
            font-weight: 500;
            margin: 0;
            cursor: pointer;
            flex: 1;
            user-select: none;
        }

        .phoenix-btn {
            background: linear-gradient(45deg, var(--phoenix-primary), var(--phoenix-secondary));
            border: none;
            border-radius: 25px;
            padding: 1rem 2rem;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-size: 1.1rem;
            width: 100%;
            cursor: pointer;
        }

        .phoenix-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.3s ease;
            transform: translate(-50%, -50%);
        }

        .phoenix-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .phoenix-btn:hover {
            background: linear-gradient(45deg, var(--phoenix-secondary), var(--phoenix-accent));
            box-shadow: 0 8px 25px var(--phoenix-shadow);
            transform: translateY(-3px);
        }

        .phoenix-alert {
            border-radius: 15px;
            padding: 1.5rem;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .phoenix-alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.2), rgba(32, 201, 151, 0.2));
            border: 2px solid #28a745;
            color: #d4edda;
        }

        .phoenix-alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2), rgba(253, 126, 20, 0.2));
            border: 2px solid #dc3545;
            color: #f8d7da;
        }

        .counter {
            color: var(--phoenix-accent);
            font-size: 0.9rem;
            text-align: right;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .counter.warning {
            color: var(--phoenix-primary);
            font-weight: 600;
        }

        .counter.danger {
            color: #dc3545;
            font-weight: 700;
            animation: pulse 1s infinite;
        }

        @keyframes phoenixGlow {
            0% { text-shadow: 0 0 20px var(--phoenix-glow); }
            100% { text-shadow: 0 0 30px var(--phoenix-glow), 0 0 40px var(--phoenix-primary); }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes phoenixRise {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-10px); }
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .disabled-field {
            opacity: 0.5;
            pointer-events: none;
        }

        /* Utility classes for spacing from Bootstrap replacement */
        .me-2 { margin-right: 0.5rem; }
        .row { display: flex; flex-wrap: wrap; }
        .justify-content-center { justify-content: center; }
        .col-lg-8 { 
            flex: 0 0 auto;
            width: 100%;
        }
        @media (min-width: 992px) {
            .col-lg-8 { width: 66.66666667%; }
        }
        @media (max-width: 768px) {
            .phoenix-title { font-size: 2rem; }
            .phoenix-icon { font-size: 2.5rem; }
            .phoenix-card { padding: 1.5rem !important; }
            .form-control, .form-select { 
                padding: 0.8rem 1rem; 
                font-size: 16px; 
            }
            .form-check { 
                padding: 1rem;
                flex-direction: row;
                text-align: left;
            }
        }
        .form-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23FF6B35' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 16px 12px;
            padding-right: 3rem;
        }
        .phoenix-logo {
            width: 30%;
            height: 30%;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="phoenix-header">
            <div class="phoenix-icon">
                <img src="assets/web.png" alt="Phoenix Logo" class="phoenix-logo">
            </div>
            <h1 class="phoenix-title">CICTT PHOENIX</h1>
            <p class="phoenix-subtitle">FREEDOM WALL</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="phoenix-card p-4">
                    <div class="text-center mb-4">
                        <h2 style="color: var(--phoenix-accent); font-weight: 600;">
                            <lord-icon
                                src="design/json/cbtlerlm.json"
                                trigger="loop"
                                delay="1500"
                                state="in-dynamic"
                                colors="primary:#c79816,secondary:#c71f16,tertiary:#911710,quaternary:#ee6d66,quinary:#e83a30"
                                style="width:50px;height:50px">
                            </lord-icon>
                            </lord-icon>
                            Share Your Voice
                        </h2>
                        <p style="color: rgba(255, 248, 240, 0.7); margin-top: 0.5rem;">
                            Open for any messages! shoutouts, feedback, ideas, or thoughts.
                        </p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="phoenix-alert phoenix-alert-danger mb-4">
                            <b class="me-2">⏳</b>
                            <?php echo $error; ?>
                        </div>
                    <?php elseif (!empty($success)): ?>
                        <div class="phoenix-alert phoenix-alert-success mb-4">
                            <b class="me-2">✅</b>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="postForm">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="me-2">
                                    <lord-icon
                                        src="design/json/mgfsulul.json"
                                        trigger="loop"
                                        colors="primary:#121331,secondary:#e8b730,tertiary:#92140c"
                                        style="width:50px;height:50px">
                                    </lord-icon>
                                </i>
                                Your Name (max 10 chars)
                            </label>
                            <input type="text" name="name" id="name" maxlength="10" 
                                class="form-control" 
                                placeholder="Enter your name or check anonymous below"
                                value="<?php echo htmlspecialchars($name); ?>">
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="anonymous" id="anonymous" class="form-check-input"
                                <?php echo $anonymous ? 'checked' : ''; ?>>
                            <label for="anonymous" class="form-check-label">
                                
                                Post as Anonymous
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="me-2">
                                <lord-icon
                                src= "design/json/mwhabkof.json"
                                trigger="loop"
                                state="hover-wave"
                                colors="primary:#121331,secondary:#f9c9c0,tertiary:#911710,quaternary:#b26836,quinary:#911710"
                                style="width:50px;height:50px">
                            </lord-icon>
                                </i>
                                Role / Year Level
                            </label>
                            <select name="year" class="form-select" required>
                                <option value="">-- Select Your Role / Year --</option>
                                <option <?php echo $year == '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                                <option <?php echo $year == '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                                <option <?php echo $year == '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                                <option <?php echo $year == '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                                <option <?php echo $year == 'Instructor' ? 'selected' : ''; ?>>Instructor</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="me-2">
                                    <lord-icon
                                    src="design/json/nsqneknp.json"
                                    trigger="loop"
                                    delay="1000"
                                    state="morph-code"
                                    colors="primary:#c79816,secondary:#c79816,tertiary:#c71f16,quaternary:#e83a30,quinary:#911710,senary:#f24c00"

                                    style="width:50px;height:50px">
                                </lord-icon>
                                </i>
                                Course
                            </label>
                            <select name="course" class="form-select" required>
                                <option value="">-- Select Your Course --</option>
                                <option <?php echo $course == 'BSIT' ? 'selected' : ''; ?>>BSCS</option>
                                <option <?php echo $course == 'BSCS' ? 'selected' : ''; ?>>BSIT</option>
                                <option <?php echo $course == 'BEED' ? 'selected' : ''; ?>>BEED</option>
                                <option <?php echo $course == 'BSED' ? 'selected' : ''; ?>>BSED</option>
                                <option <?php echo $course == 'BECED' ? 'selected' : ''; ?>>BECED</option>
                                <option <?php echo $course == 'BPA' ? 'selected' : ''; ?>>BPA</option>
                                <option <?php echo $course == 'BSSW' ? 'selected' : ''; ?>>BSSW</option>
                                <option <?php echo $course == 'BAPOS' ? 'selected' : ''; ?>>BAPOS</option>
                                <option <?php echo $course == 'BAELS' ? 'selected' : ''; ?>>BAELS</option>
                                <option <?php echo $course == 'BSND' ? 'selected' : ''; ?>>BSND</option>
                                <option <?php echo $course == 'BSN' ? 'selected' : ''; ?>>BSN</option>
                                <option <?php echo $course == 'BSHM' ? 'selected' : ''; ?>>BSHM</option>
                                <option <?php echo $course == 'AB ISLAMIC' ? 'selected' : ''; ?>>AB ISLAMIC</option>
                                <option <?php echo $course == 'BSA' ? 'selected' : ''; ?>>BSA</option>
                                <option <?php echo $course == 'BSCRIM' ? 'selected' : ''; ?>>BSCRIM</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="me-2">
                                    <lord-icon
                                        src="design/json/xpnmrymp.json"
                                        trigger="loop"
                                        state="morph-circle"
                                        colors="primary:#121331,secondary:#eee966,tertiary:#911710"
                                        style="width:50px;height:50px">
                                    </lord-icon>
                                </i>
                                Your Message (max 50 chars)
                            </label>
                            <textarea name="message" id="message" rows="4" maxlength="50" 
                                    class="form-control" required 
                                    placeholder="Share your thoughts, ideas, or feedback..."><?php echo htmlspecialchars($message); ?></textarea>
                            <div class="counter">
                                <span id="msgCount">0</span>/50 characters
                            </div>
                        </div>

                        <button type="submit" class="phoenix-btn">
                            <i class="me-2">

                            </i>
                            <lord-icon
                                src="design/json/gtvaxhwv.json"
                                trigger="loop"
                                delay="0"
                                colors="primary:#ffffff,secondary:#911710,tertiary:#911710,quaternary:#e83a30"
                                style="width:70px;height:70px">
                                SEND
                            </lord-icon>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // All JavaScript is now embedded here
        const nameField = document.getElementById("name");
        const anonCheck = document.getElementById("anonymous");
        const messageField = document.getElementById("message");
        const msgCount = document.getElementById("msgCount");
        const form = document.getElementById("postForm");
        const maxMessageLength = 50;

        // Initialize character count
        msgCount.textContent = messageField.value.length;

        // Toggle name & anonymous
        anonCheck.addEventListener("change", function() {
            if (this.checked) {
                nameField.value = "";
                nameField.classList.add("disabled-field");
                nameField.disabled = true;
            } else {
                nameField.classList.remove("disabled-field");
                nameField.disabled = false;
                nameField.focus();
            }
        });

        nameField.addEventListener("input", function() {
            if (this.value.length > 0) {
                anonCheck.checked = false;
            }
        });

        // Character counter with visual feedback
        messageField.addEventListener("input", function() {
            const length = this.value.length;
            msgCount.textContent = length;
            
            msgCount.parentElement.classList.remove("warning", "danger");
            
            if (length >= maxMessageLength - 10) {
                msgCount.parentElement.classList.add("danger");
            } else if (length >= maxMessageLength - 20) {
                msgCount.parentElement.classList.add("warning");
            }
            
            if (length > maxMessageLength) {
                this.value = this.value.substring(0, maxMessageLength);
                msgCount.textContent = maxMessageLength;
            }
        });

        // Add form validation feedback
        form.addEventListener("submit", function(e) {
            const name = nameField.value.trim();
            const anonymous = anonCheck.checked;
            const message = messageField.value.trim();

            let hasError = false;

            if (!anonymous && name.length === 0) {
                e.preventDefault();
                nameField.focus();
                nameField.style.borderColor = "#dc3545";
                setTimeout(() => {
                    nameField.style.borderColor = "";
                }, 2000);
                hasError = true;
            }

            if (message.length === 0) {
                e.preventDefault();
                messageField.focus();
                messageField.style.borderColor = "#dc3545";
                setTimeout(() => {
                    messageField.style.borderColor = "";
                }, 2000);
                hasError = true;
            }
            
            if (message.length > maxMessageLength) {
                e.preventDefault();
                messageField.focus();
                alert(`Message must be ${maxMessageLength} characters or less.`);
                hasError = true;
            }
            
            if (hasError) {
                // Prevent form submission if there are errors
                e.preventDefault();
            }
        });

        // Initialize state based on form values
        if (anonCheck.checked) {
            nameField.classList.add("disabled-field");
            nameField.disabled = true;
        }

    </script>
</body>
</html>