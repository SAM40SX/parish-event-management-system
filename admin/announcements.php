<?php
include("../config.php");
session_start();

if($_SESSION['user']['role'] != 'admin'){
    die("Access Denied");
}


/* CHECK IF USER IS LOGGED IN */
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}
$feedback = "";

if(isset($_POST['post'])){
    // Sanitize user inputs to protect database against malicious injection breaks
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO announcements(title, message) VALUES('$title', '$message')";

    if(mysqli_query($conn, $sql)){
        $feedback = "Parish announcement has been broadcast successfully!";
    } else {
        $feedback = "Error processing request. Please check connections.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broadcast Announcement — Admin Console</title>
    <style>
        :root {
            --papal-purple: #3A1B40;      /* Premium Royal/Ecclesiastical Purple */
            --cathedral-gold: #D4AF37;     /* Rich Burnished Gold Accent */
            --deep-onyx: #1E1B1F;          /* Dark Slate Text */
            --premium-bg: #F7F5F8;         /* Warm Soft Chalk White Background */
            --pure-white: #FFFFFF;
            --muted-gray: #8A858C;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        body {
            background-color: var(--premium-bg);
            color: var(--deep-onyx);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        /* Balanced Centered Interface Container Card */
        .broadcast-container {
            background: var(--pure-white);
            width: 100%;
            max-width: 600px;
            padding: 45px 40px;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(58, 27, 64, 0.06);
            border-top: 5px solid var(--papal-purple);
            position: relative;
        }

        /* Micro gold border accent line for high-end polish aesthetic */
        .broadcast-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--cathedral-gold);
        }

        /* Header Alignment Module */
        .panel-header {
            margin-bottom: 35px;
            text-align: center;
        }

        .panel-header h2 {
            color: var(--papal-purple);
            font-size: 26px;
            font-weight: 600;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .panel-header p {
            font-size: 14px;
            color: var(--muted-gray);
        }

        /* Form Fields Group Arrays */
        .field-group {
            margin-bottom: 24px;
        }

        .field-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--papal-purple);
            margin-bottom: 8px;
        }

        input[type="text"], 
        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E2DFE3;
            background-color: #FAFAF9;
            border-radius: 8px;
            font-size: 15px;
            color: var(--deep-onyx);
            outline: none;
            transition: all 0.25s ease;
        }

        textarea {
            height: 160px;
            resize: vertical;
            line-height: 1.5;
        }

        /* Interactive Focus Enhancements */
        input:focus, textarea:focus {
            border-color: var(--cathedral-gold);
            background-color: var(--pure-white);
            box-shadow: 0 4px 15px rgba(214, 175, 55, 0.12);
        }

        /* Submission Action Trigger Button */
        .btn-broadcast {
            width: 100%;
            padding: 16px;
            background: var(--papal-purple);
            color: var(--pure-white);
            border: 1px solid var(--papal-purple);
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.25s ease;
            margin-top: 5px;
        }

        .btn-broadcast:hover {
            background: var(--cathedral-gold);
            border-color: var(--cathedral-gold);
            color: var(--papal-purple);
            box-shadow: 0 6px 20px rgba(214, 175, 55, 0.25);
            transform: translateY(-1px);
        }

        /* Success Alert Strip Design */
        .status-strip {
            background: #EDF7ED;
            color: #2e7d32;
            padding: 14px 18px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            border-left: 4px solid #2e7d32;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Safe Workflow Escape Button Link */
        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: var(--muted-gray);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .back-link a:hover {
            color: var(--papal-purple);
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .broadcast-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="broadcast-container">

    <div class="panel-header">
        <h2>Post Parish Announcement</h2>
        <p>Publish critical updates, holy mass notifications, or general community notices.</p>
    </div>

    <!-- Injecting modern clean alerts within form workflow feedback loops -->
    <?php if(!empty($feedback)): ?>
        <div class="status-strip">
            <span>📢</span> <?php echo $feedback; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="field-group">
            <label for="title">Announcement Title</label>
            <input type="text" id="title" name="title" placeholder="e.g., Schedule Update: Feast of the Assumption" required>
        </div>

        <div class="field-group">
            <label for="message">Detailed Message</label>
            <textarea id="message" name="message" placeholder="Write full pastoral notifications, instructions, or agenda items here for parishioners to read..." required></textarea>
        </div>

        <button type="submit" name="post" class="btn-broadcast">Post Announcement</button>

    </form>

    <!-- Administrative Workflow Fallback Link -->
    <div class="back-link">
        <a href="dashboard.php">← Back to Admin Control Console</a>
    </div>

</div>

</body>
</html>