<?php
include("../config.php");
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

if(!isset($_GET['id'])){
    die("Event not found");
}

$event_id = intval($_GET['id']);

// Initialize status variables
$status = "";
$message = "";
$subtitle = "";

/* =========================================================
   CHECK IF USER ALREADY REGISTERED FOR EVENT
========================================================= */
$stmt = $conn->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $status = "warning";
    $message = "Already Registered";
    $subtitle = "You have already submitted a registration request for this event.";

} else {

    /* =========================================================
       REGISTER USER WITH PENDING STATUS
    ========================================================= */

    $query = mysqli_query($conn,"
        INSERT INTO registrations(user_id,event_id,status)
        VALUES('$user_id','$event_id','Pending')
    ");

    if($query){

        $status = "success";
        $message = "Registration Submitted";
        $subtitle = "Your registration is pending admin approval.";

    } else {

        $status = "error";
        $message = "Registration Failed";
        $subtitle = "An error occurred while processing your registration.";

    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registry Status | Sacred Heart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>

        :root{
            --cathedral-blue:#0A192F;
            --liturgical-gold:#D4AF37;
            --soft-gold:#F4E8C1;
            --holy-white:#FCFBF9;
            --marble-gray:#F0F2F5;
            --text-dark:#2C3E50;
            --accent-crimson:#8B0000;

            --font-serif:'Cinzel', serif;
            --font-sans:'Inter', sans-serif;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background:var(--marble-gray);
            font-family:var(--font-sans);
            color:var(--text-dark);
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            overflow:hidden;
            position:relative;
        }

        /* ==============================
           ANIMATED BACKGROUND
        ============================== */

        .ambient-background{
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:
            radial-gradient(circle at center,
            #E6EEF8 0%,
            var(--marble-gray) 100%);
            overflow:hidden;
            z-index:1;
        }

        .ember{
            position:absolute;
            border-radius:50%;
            background:
            radial-gradient(circle,
            rgba(212,175,55,0.25) 0%,
            rgba(212,175,55,0) 70%);
            animation:floatUpDown 20s infinite ease-in-out;
        }

        .ember-1{
            width:150px;
            height:150px;
            top:15%;
            left:15%;
        }

        .ember-2{
            width:220px;
            height:220px;
            top:60%;
            right:10%;
            animation-duration:25s;
        }

        @keyframes floatUpDown{
            0%,100%{
                transform:translateY(0px) scale(1);
                opacity:0.4;
            }

            50%{
                transform:translateY(-40px) scale(1.15);
                opacity:0.8;
            }
        }

        /* ==============================
           RECEIPT CARD
        ============================== */

        .receipt-card{
            width:90%;
            max-width:450px;

            background:rgba(252,251,249,0.92);

            backdrop-filter:blur(12px);
            -webkit-backdrop-filter:blur(12px);

            border-radius:18px;

            padding:45px 35px;

            text-align:center;

            position:relative;
            z-index:2;

            border:1px solid rgba(212,175,55,0.25);

            box-shadow:
            0 15px 35px rgba(10,25,47,0.12);

            transform:translateY(20px);
            opacity:0;

            animation:cardEntrance 0.7s ease forwards;
        }

        @keyframes cardEntrance{
            to{
                transform:translateY(0);
                opacity:1;
            }
        }

        .receipt-card::before{
            content:'';
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:5px;
            background:var(--liturgical-gold);
            border-radius:18px 18px 0 0;
        }

        /* ==============================
           ICONS
        ============================== */

        .status-icon{
            width:80px;
            height:80px;
            margin:0 auto 25px;
        }

        .status-icon svg{
            width:100%;
            height:100%;
        }

        .icon-success{
            fill:var(--liturgical-gold);
        }

        .icon-warning{
            fill:#555;
        }

        .icon-error{
            fill:var(--accent-crimson);
        }

        /* ==============================
           TYPOGRAPHY
        ============================== */

        .receipt-card h1{
            font-family:var(--font-serif);
            font-size:28px;
            margin-bottom:15px;
            color:var(--cathedral-blue);
        }

        .receipt-card p{
            font-size:15px;
            line-height:1.7;
            color:#5F6B78;
            margin-bottom:30px;
        }

        /* ==============================
           BUTTON
        ============================== */

        .btn-return{
            display:inline-block;

            background:var(--cathedral-blue);
            color:var(--soft-gold);

            text-decoration:none;

            padding:14px 35px;

            border-radius:8px;

            border:1px solid var(--liturgical-gold);

            font-family:var(--font-serif);

            letter-spacing:1px;

            transition:0.3s ease;
        }

        .btn-return:hover{
            background:var(--liturgical-gold);
            color:var(--cathedral-blue);

            transform:translateY(-2px);

            box-shadow:
            0 6px 18px rgba(212,175,55,0.35);
        }

        /* ==============================
           MOBILE
        ============================== */

        @media(max-width:600px){

            .receipt-card{
                padding:35px 25px;
            }

            .receipt-card h1{
                font-size:24px;
            }
        }

    </style>
</head>
<body>

<!-- Animated Background -->
<div class="ambient-background">
    <div class="ember ember-1"></div>
    <div class="ember ember-2"></div>
</div>

<!-- Registration Status Card -->
<div class="receipt-card">

    <div class="status-icon">

        <?php if($status === "success"): ?>

            <!-- Success Icon -->
            <svg class="icon-success" viewBox="0 0 64 64">
                <path d="M32 4a28 28 0 1 0 28 28A28 28 0 0 0 32 4zm-4 40L16 32l4-4 8 8 16-16 4 4z"/>
            </svg>

        <?php elseif($status === "warning"): ?>

            <!-- Warning Icon -->
            <svg class="icon-warning" viewBox="0 0 64 64">
                <path d="M32 6L4 58h56L32 6zm0 40a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm-3-22h6v16h-6z"/>
            </svg>

        <?php else: ?>

            <!-- Error Icon -->
            <svg class="icon-error" viewBox="0 0 64 64">
                <path d="M32 4a28 28 0 1 0 28 28A28 28 0 0 0 32 4zm12 36l-4 4-8-8-8 8-4-4 8-8-8-8 4-4 8 8 8-8 4 4-8 8z"/>
            </svg>

        <?php endif; ?>

    </div>

    <h1><?php echo $message; ?></h1>

    <p><?php echo $subtitle; ?></p>

    <a href="view_events.php" class="btn-return">
        Return to Events
    </a>

</div>

</body>
</html>