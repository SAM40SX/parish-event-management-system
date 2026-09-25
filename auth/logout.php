<?php
session_start();

// 1. Instantly wipe out all user session variables on the server
$_SESSION = array();

// 2. Destroy the physical session file
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Automatic redirect to login.php after 4 seconds -->
    <meta http-equiv="refresh" content="4;url=login.php">
    
    <title>Logged Out - Sacred Heart Parish</title>
    <style>
        :root {
            --gold-accent: #E5C158;
            --dark-navy: #0A192F;
            --white-pure: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /* Full viewport dark background layout with a warm church image mask overlay */
            background: linear-gradient(rgba(10, 25, 71, 0.65), rgba(10, 25, 47, 0.85)), 
                        url('images/church-interior.jpg') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-attachment: fixed;
        }

        /* Glassmorphism Card Design */
        .logout-container {
            background: rgba(255, 255, 255, 0.12);
            width: 100%;
            max-width: 460px;
            padding: 50px 40px;
            border-radius: 16px;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            text-align: center;
            color: var(--white-pure);
            animation: fadeIn 0.8s ease-out;
        }

        /* Elegant Religious Icon Element */
        .sacred-cross {
            font-size: 48px;
            color: var(--gold-accent);
            margin-bottom: 15px;
            line-height: 1;
            text-shadow: 0 0 10px rgba(229, 193, 88, 0.4);
        }

        .logout-container h2 {
            font-size: 30px;
            font-weight: 500;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        .logout-container p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Manual Redirection Button */
        .btn-redirect {
            display: inline-block;
            width: 100%;
            padding: 14px;
            background: var(--gold-accent);
            color: var(--dark-navy);
            text-decoration: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(229, 193, 88, 0.2);
        }

        .btn-redirect:hover {
            background: #F0D276;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(229, 193, 88, 0.3);
        }

        /* Micro-loader to visually tell the user they are being redirected */
        .redirect-loader {
            margin-top: 25px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.5px;
        }

        .dots::after {
            content: '';
            animation: loadingDots 1.5s infinite;
        }

        /* Clean entry animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes loadingDots {
            0% { content: ''; }
            25% { content: '.'; }
            50% { content: '..'; }
            75% { content: '...'; }
            100% { content: ''; }
        }
    </style>
</head>
<body>

    <div class="logout-container">
        <!-- Traditional Liturgical Cross Character Decorator -->
        <div class="sacred-cross">†</div>
        
        <h2>Go In Peace</h2>
        <p>You have successfully logged out of your secure parish account. May you have a blessed day ahead.</p>

        <!-- Fallback if user doesn't want to wait for the automatic metadata timer redirect -->
        <a href="login.php" class="btn-redirect">Return to Login</a>

        <div class="redirect-loader">
            Redirecting to portal page<span class="dots"></span>
        </div>
    </div>

</body>
</html>