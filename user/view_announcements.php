<?php
include("../config.php");
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

/* FETCH ANNOUNCEMENTS */
$result = mysqli_query($conn,"
SELECT * FROM announcements
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Church Announcements | Sacred Heart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Premium Liturgical Typography Selection -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --cathedral-blue: #0A192F;      /* Deep reverent navy */
            --liturgical-gold: #D4AF37;    /* Classic rich gold */
            --soft-gold: #F4E8C1;          /* Warm parchment accent */
            --holy-white: #FCFBF9;         /* Off-white overlay tone */
            --font-serif: 'Cinzel', Georgia, serif;
            --font-sans: 'Inter', Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: 
                linear-gradient(rgba(10, 25, 47, 0.88), rgba(10, 25, 47, 0.88)),
                url('../images/church_background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            font-family: var(--font-sans);
            padding: 50px 20px;
            color: #E2E8F0;
        }

        /* --- PAGE TITLE SECTION --- */
        .page-title {
            text-align: center;
            margin-bottom: 50px;
        }

        /* Pure CSS Styled Mini Cross Emblem */
        .title-cross {
            width: 10px;
            height: 24px;
            background: var(--liturgical-gold);
            margin: 0 auto 15px auto;
            position: relative;
            border-radius: 1px;
        }

        .title-cross::after {
            content: '';
            position: absolute;
            top: 6px;
            left: -7px;
            width: 24px;
            height: 10px;
            background: var(--liturgical-gold);
            border-radius: 1px;
        }

        .page-title h1 {
            font-family: var(--font-serif);
            color: var(--liturgical-gold);
            font-size: 36px;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .page-title p {
            color: var(--soft-gold);
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 300;
        }

        /* --- CONTAINER --- */
        .container {
            max-width: 850px;
            margin: auto;
        }

        /* --- ANNOUNCEMENT BULLETINS --- */
        .announcement {
            background: rgba(253, 251, 249, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(214, 175, 55, 0.2);
            border-radius: 16px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .announcement:hover {
            transform: translateY(-4px);
            border-color: var(--liturgical-gold);
            box-shadow: 0 15px 30px rgba(214, 175, 55, 0.1);
        }

        /* Liturgical gold ribbon corner flag */
        .announcement::before {
            content: '';
            position: absolute;
            top: 0;
            left: 30px;
            width: 14px;
            height: 24px;
            background: var(--liturgical-gold);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);
            opacity: 0.8;
        }

        .announcement h2 {
            font-family: var(--font-serif);
            color: var(--liturgical-gold);
            font-size: 22px;
            margin-bottom: 18px;
            letter-spacing: 0.5px;
            padding-left: 20px;
        }

        .announcement p {
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
            font-size: 15px;
            font-weight: 300;
            letter-spacing: 0.3px;
        }

        /* --- LITURGICAL CALENDAR CALIBRATION --- */
        .date {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px dashed rgba(214, 175, 55, 0.2);
            font-size: 12px;
            color: var(--soft-gold);
            letter-spacing: 1px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Tiny calendar shape marker symbol */
        .date::before {
            content: '☩';
            color: var(--liturgical-gold);
            font-size: 14px;
        }

        /* --- EMPTY STATE BLOCK --- */
        .empty {
            background: rgba(253, 251, 249, 0.03);
            border: 1px dashed rgba(214, 175, 55, 0.15);
            padding: 50px 30px;
            border-radius: 16px;
            text-align: center;
        }

        .empty h2 {
            font-family: var(--font-serif);
            color: rgba(255, 255, 255, 0.4);
            font-size: 20px;
            letter-spacing: 1px;
        }

        /* --- ACTIONS FOOTER --- */
        .footer-actions {
            text-align: center;
            margin-top: 40px;
        }

        .back {
            display: inline-block;
            padding: 14px 28px;
            background: transparent;
            color: var(--soft-gold);
            text-decoration: none;
            border: 1px solid var(--liturgical-gold);
            border-radius: 8px;
            font-family: var(--font-serif);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .back:hover {
            background: var(--liturgical-gold);
            color: var(--cathedral-blue);
            box-shadow: 0 4px 15px rgba(214, 175, 55, 0.2);
        }

        /* Mobile Adjustments */
        @media(max-width:768px){
            body { padding: 30px 15px; }
            .page-title h1 { font-size: 28px; }
            .announcement { padding: 25px 20px; }
            .announcement h2 { font-size: 18px; padding-left: 15px; }
        }
    </style>
</head>
<body>

<div class="page-title">
    <!-- CATHEDRAL DESIGN SYMBOL -->
    <div class="title-cross"></div>
    <h1>Parish Announcements</h1>
    <p>Chronicles of Sacred Heart Community</p>
</div>

<div class="container">

<?php
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
?>

    <div class="announcement">
        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
        
        <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>

        <div class="date">
            Promulgated: <?php echo date("F d, Y", strtotime($row['created_at'])); ?>
        </div>
    </div>

<?php
    }
}else{
?>

    <div class="empty">
        <h2>No current notices are posted on the bulletin board.</h2>
    </div>

<?php } ?>

    <!-- BACK ACTION BUTTON -->
    <div class="footer-actions">
        <a class="back" href="dashboard.php">
            ← Return to Dashboard
        </a>
    </div>

</div>

</body>
</html>