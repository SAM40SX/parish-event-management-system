<?php
include("../config.php");
session_start();

/* CHECK IF USER IS LOGGED IN */
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

/* CHECK IF USER IS ADMIN */
if($_SESSION['user']['role'] != 'admin'){
    die("Access Denied");
}

/* CREATE EVENT via PREPARED STATEMENTS */
if(isset($_POST['create'])){
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date  = trim($_POST['event_date']);
    $event_time  = trim($_POST['event_time']);
    $location    = trim($_POST['location']);

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, event_time, location) VALUES (?, ?, ?, ?, ?)");
    
    if($stmt) {
        $stmt->bind_param("sssss", $title, $description, $event_date, $event_time, $location);
        if($stmt->execute()){
            $success = "Parish event has been successfully published to the registry.";
        } else {
            $error = "Failed to store event entry within the archival database.";
        }
        $stmt->close();
    } else {
        $error = "Database execution pipeline initialization failed.";
    }
} // <--- Properly closed the conditional post logic here!
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publish Parish Event | Sacred Heart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #dfb76c;
            --primary-glow: rgba(223, 183, 108, 0.15);
            --dark: #070c14;
            --card-bg: rgba(10, 18, 32, 0.75);
            --border: rgba(223, 183, 108, 0.15);
            --input-bg: rgba(255, 255, 255, 0.03);
            --input-border: rgba(255, 255, 255, 0.08);
            --text: #f8fafc;
            --muted: #94a3b8;
            --state-success: #4ade80;
            --state-error: #f87171;
            
            --font-serif: 'Cinzel', Georgia, serif;
            --font-sans: 'Inter', Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-sans);
        }

        body {
            min-height: 100vh;
            background: linear-gradient(rgba(7, 12, 20, 0.85), rgba(7, 12, 20, 0.9)), url('../images/church.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
            color: var(--text);
        }

        .form-container {
            width: 100%;
            max-width: 680px;
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 50px 45px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h1 {
            font-family: var(--font-serif);
            color: var(--primary);
            font-size: 32px;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .form-header h1::after {
            content: "✦";
            display: block;
            font-size: 12px;
            margin-top: 6px;
            opacity: 0.7;
        }

        .form-header p {
            color: var(--muted);
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            line-height: 1.5;
        }

        .alert.success {
            background: rgba(34, 197, 94, 0.08);
            color: var(--state-success);
            border: 1px solid rgba(34, 197, 94, 0.2);
            box-shadow: 0 4px 20px rgba(34, 197, 94, 0.05);
        }

        .alert.error {
            background: rgba(239, 68, 68, 0.08);
            color: var(--state-error);
            border: 1px solid rgba(239, 68, 68, 0.2);
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.05);
        }

        .form-group {
            margin-bottom: 26px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--primary);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            opacity: 0.9;
        }

        input, textarea {
            width: 100%;
            padding: 16px 18px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            color: var(--text);
            font-size: 15px;
            font-weight: 400;
            outline: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        textarea {
            resize: none;
            height: 140px;
            line-height: 1.6;
        }

        input::placeholder, textarea::placeholder {
            color: rgba(248, 250, 252, 0.3);
        }

        input:hover, textarea:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(223, 183, 108, 0.3);
        }

        input:focus, textarea:focus {
            background: rgba(7, 12, 20, 0.6);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(85%) sepia(20%) saturate(1000%) hue-rotate(330deg);
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator:hover,
        input[type="time"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        button {
            width: 100%;
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: linear-gradient(135deg, #ebd19b, var(--primary));
            color: #070c14;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(223, 183, 108, 0.15);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px var(--primary-glow);
            filter: brightness(1.05);
        }

        button:active {
            transform: translateY(0);
        }

        .back {
            margin-top: 35px;
            text-align: center;
        }

        .back a {
            font-family: var(--font-serif);
            font-size: 12px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            opacity: 0.8;
        }

        .back a:hover {
            opacity: 1;
            color: #fff;
        }

        @media(max-width: 768px) {
            body {
                padding: 40px 16px;
            }

            .form-container {
                padding: 40px 24px;
                border-radius: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-header h1 {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>

<div class="form-container">

    <div class="form-header">
        <h1>Create Parish Event</h1>
        <p>Publish activities, masses, and special parish liturgical events</p>
    </div>

    <?php if(isset($success)): ?>
        <div class="alert success">
            ✦ <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert error">
            ✕ <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Event Title</label>
            <input type="text" name="title" placeholder="e.g., Solemnity Feast Mass" required autocomplete="off">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" placeholder="Describe the order of service or details regarding the schedule..." required></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Event Date</label>
                <input type="date" name="event_date" required>
            </div>

            <div class="form-group">
                <label>Event Time</label>
                <input type="time" name="event_time" required>
            </div>
        </div>

        <div class="form-group">
            <label>Location / Venue</label>
            <input type="text" name="location" placeholder="e.g., Main Sanctuary, Parish Hall" required autocomplete="off">
        </div>

        <button type="submit" name="create">
            Publish Event
        </button>

    </form>

    <div class="back">
        <a href="dashboard.php">
            <svg style="width:12px; height:12px; margin-right:8px; fill:currentColor; transform: scaleX(-1);" viewBox="0 0 24 24">
                <path d="M5 13h11.86l-5.43 5.43 1.42 1.42L21.14 12l-8.29-8.29-1.42 1.42 5.43 5.43H5v2z"/>
            </svg>
            Return to Dashboard
        </a>
    </div>

</div>

</body>
</html>