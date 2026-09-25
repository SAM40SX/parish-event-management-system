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

// Force security fallback check if the administrator is not authenticated
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

// Target event ID parameter string (hardcoded to 1 as per your code structure)
$event_id = 1;

// Optional: Retrieve event title to display inside structural data cards
$event_title = "Sunday High Mass"; 
$query = mysqli_query($conn, "SELECT title FROM events WHERE id = '$event_id'");
if($query && mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    $event_title = $row['title'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Attendance Console</title>
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

        /* Balanced Centered Display Display Kiosk Card */
        .kiosk-container {
            background: var(--pure-white);
            width: 100%;
            max-width: 460px;
            padding: 45px 35px;
            border-radius: 20px;
            box-shadow: 0 15px 45px rgba(58, 27, 64, 0.08);
            border-top: 6px solid var(--papal-purple);
            text-align: center;
            position: relative;
        }

        /* Micro luxury gold border accent ribbon */
        .kiosk-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--cathedral-gold);
        }

        /* Structural Page Title Layout */
        .kiosk-header {
            margin-bottom: 30px;
        }

        .kiosk-header h1 {
            color: var(--papal-purple);
            font-size: 26px;
            font-weight: 600;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .kiosk-header p {
            font-size: 14px;
            color: var(--muted-gray);
            line-height: 1.4;
        }

        /* --- STYLISH QR DISPLAY CONTAINER --- */
        .qr-pass-wrapper {
            background: #FAFAF9;
            border: 2px dashed #E2DFE3;
            border-radius: 14px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
            width: 100%;
            transition: border-color 0.3s ease;
        }

        .qr-pass-wrapper:hover {
            border-color: var(--cathedral-gold);
        }

        .qr-image {
            display: block;
            margin: 0 auto 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            border: 4px solid var(--pure-white);
            max-width: 200px;
            width: 100%;
            height: auto;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .qr-pass-wrapper:hover .qr-image {
            transform: scale(1.03);
        }

        /* Metadata Badging Context Cards */
        .event-badge {
            background: rgba(58, 27, 64, 0.05);
            color: var(--papal-purple);
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 5px;
            border: 1px solid rgba(58, 27, 64, 0.05);
        }

        .instruction-text {
            color: var(--deep-onyx);
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        /* Safe Escaping Administrative Footer Workflow Link */
        .back-nav {
            text-align: center;
            border-top: 1px solid #ECE9EC;
            padding-top: 25px;
        }

        .back-nav a {
            color: var(--muted-gray);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .back-nav a:hover {
            color: var(--papal-purple);
            text-decoration: underline;
        }

        /* Kiosk Presentation Mobile Adjusters */
        @media (max-width: 480px) {
            .kiosk-container {
                padding: 35px 20px;
            }
            .qr-pass-wrapper {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="kiosk-container">

    <div class="kiosk-header">
        <h1>QR Attendance Kiosk</h1>
        <p>Display this barcode terminal at the entrance checkpoints for instant mobile verification.</p>
    </div>

    <!-- Interactive Card Module Pass layout container -->
    <div class="qr-pass-wrapper">
        
        <!-- Live API QR Render Engine Interface Point -->
        <img class="qr-image" 
             src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($event_id); ?>" 
             alt="Parish Event Attendance QR Code Check-In">
        
        <div class="event-badge">
            📌 <?php echo htmlspecialchars($event_title); ?>
        </div>
        
        <div class="instruction-text">
            Scan code to mark registration presence
        </div>

    </div>

    <!-- Administrative Workflow Escape Route Hook -->
    <div class="back-nav">
        <a href="dashboard.php">← Close Terminal & Return to Console</a>
    </div>

</div>

</body>
</html>