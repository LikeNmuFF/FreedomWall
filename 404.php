<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        :root {
            --phoenix-primary: #ff4500;
            --phoenix-secondary: #ff6b35;
            --phoenix-accent: #f7931e;
            --phoenix-gold: #ffd700;
            --phoenix-ember: #ff8c42;
            --text-primary: #ffffff;
            --text-secondary: #ffd6cc;
            --background: #0a0a0a;
            --card-bg: rgba(20, 20, 20, 0.9);
            --border: rgba(255, 107, 53, 0.2);
            --glow: rgba(255, 69, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(ellipse at center, #1a0f0a 0%, #0a0a0a 70%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated ember particles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(255, 69, 0, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 140, 66, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 40% 60%, rgba(247, 147, 30, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 70% 30%, rgba(255, 215, 0, 0.05) 0%, transparent 40%);
            animation: emberFloat 15s ease-in-out infinite;
            z-index: -1;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            backdrop-filter: blur(10px);
        }

        .header {
            padding: 1.5rem 2rem;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 69, 0, 0.1), transparent);
            animation: fireShimmer 4s infinite;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 2;
        }

        .phoenix-logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--phoenix-primary), var(--phoenix-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px var(--glow);
            animation: logoGlow 3s ease-in-out infinite;
            font-size: 1.5rem;
        }

        .phoenix-title {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--phoenix-primary), var(--phoenix-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .phoenix-subtitle {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .footer {
            padding: 1.5rem 2rem;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--border);
        }

        .live-indicator {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--phoenix-accent);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .live-dot {
            width: 10px;
            height: 10px;
            background: var(--phoenix-primary);
            border-radius: 50%;
            animation: phoenixPulse 2s infinite;
            box-shadow: 0 0 15px var(--phoenix-primary);
        }

        /* --- 404 Page Specific Styles --- */
        .404-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: 2.5rem;
            padding: 3rem 2rem;
            position: relative;
            z-index: 2;
        }

        /* Phoenix-themed abstract elements */
        .phoenix-elements {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 600px;
            z-index: 1;
            pointer-events: none;
        }

        .flame-ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid;
            animation: flameRotate 20s linear infinite;
        }

        .flame-ring:nth-child(1) {
            top: 20%;
            left: 15%;
            width: 120px;
            height: 120px;
            border-color: rgba(255, 69, 0, 0.3);
            animation-duration: 15s;
        }

        .flame-ring:nth-child(2) {
            top: 60%;
            right: 20%;
            width: 80px;
            height: 80px;
            border-color: rgba(255, 140, 66, 0.4);
            animation-duration: 12s;
            animation-direction: reverse;
        }

        .flame-ring:nth-child(3) {
            bottom: 25%;
            left: 25%;
            width: 100px;
            height: 100px;
            border-color: rgba(247, 147, 30, 0.3);
            animation-duration: 18s;
        }

        .ember-trail {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--phoenix-ember);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--phoenix-ember);
        }

        .ember-trail:nth-child(4) { top: 30%; left: 20%; animation: emberRise 8s infinite; }
        .ember-trail:nth-child(5) { top: 70%; right: 30%; animation: emberRise 6s infinite 1s; }
        .ember-trail:nth-child(6) { bottom: 40%; left: 60%; animation: emberRise 7s infinite 2s; }
        .ember-trail:nth-child(7) { top: 50%; right: 15%; animation: emberRise 9s infinite 3s; }

        .error-code {
            font-size: 10rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--phoenix-primary) 0%, var(--phoenix-accent) 50%, var(--phoenix-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: phoenixGlow 3s ease-in-out infinite alternate;
            position: relative;
            z-index: 10;
            text-shadow: 0 0 80px rgba(255, 69, 0, 0.5);
            filter: drop-shadow(0 0 20px rgba(255, 69, 0, 0.3));
        }

        .error-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--text-primary);
            animation: riseFromAshes 1.5s ease-out;
            position: relative;
            z-index: 10;
            margin-bottom: 1rem;
            text-shadow: 0 0 30px rgba(255, 107, 53, 0.3);
        }

        .error-message {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 600px;
            line-height: 1.7;
            animation: riseFromAshes 1.8s ease-out;
            position: relative;
            z-index: 10;
            margin-bottom: 2rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.2rem 2.5rem;
            background: linear-gradient(135deg, var(--phoenix-primary), var(--phoenix-accent));
            color: white;
            text-decoration: none;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 30px rgba(255, 69, 0, 0.4);
            position: relative;
            overflow: hidden;
            z-index: 10;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .back-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .back-link:hover::before {
            left: 100%;
        }

        .back-link:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 69, 0, 0.6);
            background: linear-gradient(135deg, var(--phoenix-accent), var(--phoenix-gold));
        }

        .phoenix-icon {
            font-size: 1.2rem;
            transition: transform 0.4s ease;
        }

        .back-link:hover .phoenix-icon {
            transform: translateX(-5px) rotate(-10deg);
        }

        /* Subtle flame pattern overlay */
        .flame-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 25% 25%, rgba(255, 69, 0, 0.02) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255, 140, 66, 0.02) 0%, transparent 50%);
            z-index: 1;
            animation: flamePattern 25s ease-in-out infinite;
        }

        /* Animations */
        @keyframes emberFloat {
            0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            33% { transform: scale(1.1) rotate(120deg); opacity: 1; }
            66% { transform: scale(0.9) rotate(240deg); opacity: 0.9; }
        }

        @keyframes fireShimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        @keyframes logoGlow {
            0%, 100% { box-shadow: 0 8px 32px var(--glow); }
            50% { box-shadow: 0 8px 40px rgba(255, 69, 0, 0.6); }
        }

        @keyframes phoenixPulse {
            0%, 100% { 
                opacity: 1; 
                transform: scale(1); 
                box-shadow: 0 0 15px var(--phoenix-primary);
            }
            50% { 
                opacity: 0.7; 
                transform: scale(1.2); 
                box-shadow: 0 0 25px var(--phoenix-primary);
            }
        }

        @keyframes flameRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes emberRise {
            0% { 
                opacity: 0; 
                transform: translateY(0px) scale(1); 
            }
            20% { 
                opacity: 1; 
                transform: translateY(-20px) scale(1.2); 
            }
            80% { 
                opacity: 0.8; 
                transform: translateY(-60px) scale(0.8); 
            }
            100% { 
                opacity: 0; 
                transform: translateY(-100px) scale(0.5); 
            }
        }

        @keyframes phoenixGlow {
            0% { 
                filter: drop-shadow(0 0 20px rgba(255, 69, 0, 0.3)) brightness(1); 
            }
            100% { 
                filter: drop-shadow(0 0 40px rgba(255, 69, 0, 0.5)) brightness(1.2); 
            }
        }

        @keyframes riseFromAshes {
            0% { 
                opacity: 0; 
                transform: translateY(50px) scale(0.8); 
            }
            70% { 
                opacity: 0.8; 
                transform: translateY(-10px) scale(1.05); 
            }
            100% { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }

        @keyframes flamePattern {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.1); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .error-code { font-size: 6rem; }
            .error-title { font-size: 2.5rem; }
            .error-message { font-size: 1.1rem; }
            .404-container { gap: 2rem; padding: 2rem 1rem; }
            .phoenix-elements { width: 400px; height: 400px; }
            .header { padding: 1rem; }
            .phoenix-title { font-size: 1.5rem; }
            .back-link { padding: 1rem 2rem; }
        }

        @media (max-width: 480px) {
            .error-code { font-size: 4rem; }
            .error-title { font-size: 2rem; }
            .back-link { padding: 0.75rem 1.5rem; font-size: 0.8rem; }
        }
    </style>
</head>
<body class="404-page">
    <div class="flame-overlay"></div>
    
    <div class="container">
        <header class="header">
            <div class="header-left">
                <img src="../assets/web.png" alt="Phoenix Logo" class="phoenix-logo">
                <div class="title-group">
                    <h1 class="phoenix-title">Phoenix Freedom Wall</h1>
                    <p class="phoenix-subtitle">Live Message Display</p>
                </div>
            </div>
        </header>

        <main class="404-container">
            <div class="phoenix-elements">
                <div class="flame-ring"></div>
                <div class="flame-ring"></div>
                <div class="flame-ring"></div>
                <div class="ember-trail"></div>
                <div class="ember-trail"></div>
                <div class="ember-trail"></div>
                <div class="ember-trail"></div>
            </div>

            <h1 class="error-code">404</h1>
            <h2 class="error-title">Rising from the Ashes</h2>
            <p class="error-message">
                This page has been consumed by flames, but like a phoenix, we'll help you rise again. 
                Navigate back to where the fire burns bright and messages soar free.
            </p>
            <a href="index.php" class="back-link">
                <span class="phoenix-icon">🔥</span>
                WHERE YA'H FROM?? MARS?
            </a>
        </main>

        <footer class="footer">
            <div class="footer-text">
                <span class="live-indicator">
                    <span class="live-dot"></span>
                    LIVE
                </span>
            </div>
        </footer>
    </div>
</body>
</html>