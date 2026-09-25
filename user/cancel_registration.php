<?php
include("../config.php");
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

if(!isset($_GET['id'])){
    die("Invalid Request");
}

$event_id = intval($_GET['id']);

$status = "";
$message = "";
$subtitle = "";

/* SECURED CANCELLATION ENGINE (Prepared Statement) */
$stmt = $conn->prepare("DELETE FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->bind_param("ii", $user_id, $event_id);

if($stmt->execute()){
    // Check if a row was actually deleted
    if($stmt->affected_rows > 0) {
        $status = "success";
        $message = "Registration Cancelled";
        $subtitle = "Your reservation has been removed from this parish ledger.";
    } else {
        $status = "error";
        $message = "Record Not Found";
        $subtitle = "We could not locate an active registration matching this event profile.";
    }
} else {
    $status = "error";
    $message = "Cancellation Failed";
    $subtitle = "An unexpected error occurred while processing this registry request.";
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registry Update | Sacred Heart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --cathedral-blue: #0A192F;
            --liturgical-gold: #D4AF37;
            --soft-gold: #F4E8C1;
            --holy-white: #FCFBF9;
            --marble-gray: #F0F2F5;
            --text-dark: #2C3E50;
            --ecclesia-charcoal: #5C6266; /* Ash Wednesday slate gray */
            --accent-crimson: #8B0000;    /* Traditional deep church red */
            --font-serif: 'Cinzel', Georgia, serif;
            --font-sans: 'Inter', Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--marble-gray);
            font-family: var(--font-sans);
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* --- AMBIENT LITURGICAL BACKGROUND ANIMATION --- */
        .ambient-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 50%, #E8EEF5 0%, var(--marble-gray) 100%);
            z-index: 1;
            overflow: hidden;
        }

        .ember {
            position: absolute;
            background: radial-gradient(circle, rgba(212,175,55,0.18) 0%, rgba(214,175,55,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: floatUpDown 24s infinite ease-in-out;
        }

        .ember-1 { width: 160px; height: 160px; top: 15%; left: 75%; animation-delay: 0s; }
        .ember-2 { width: 200px; height: 200px; top: 60%; left: 20%; animation-direction: reverse; animation-duration: 22s; }

        @keyframes floatUpDown {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.3; }
            50% { transform: translateY(-40px) scale(1.15); opacity: 0.6; }
        }

        /* --- PREMIUM TRANSACTIONAL RECEIPT CARD --- */
        .receipt-card {
            background: rgba(252, 251, 249, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(10, 25, 47, 0.08);
            max-width: 440px;
            width: 90%;
            padding: 45px 35px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(10, 25, 47, 0.1);
            z-index: 2;
            position: relative;
            transform: translateY(15px);
            opacity: 0;
            animation: cardEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardEntrance {
            to { transform: translateY(0); opacity: 1; }
        }

        /* Ash/Slate Top Accent Border to represent cancellation/withdrawal */
        .receipt-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--ecclesia-charcoal);
            border-radius: 16px 16px 0 0;
        }

        /* --- LITURGICAL SYMBOLS --- */
        .status-icon {
            width: 75px;
            height: 75px;
            margin: 0 auto 25px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .status-icon svg {
            width: 100%;
            height: 100%;
        }

        .icon-cancellation { fill: var(--ecclesia-charcoal); filter: drop-shadow(0 4px 8px rgba(92,98,102,0.25)); }
        .icon-error { fill: var(--accent-crimson); }

        /* --- TYPOGRAPHY --- */
        .receipt-card h1 {
            font-family: var(--font-serif);
            color: var(--cathedral-blue);
            font-size: 23px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .receipt-card p {
            font-size: 14px;
            color: #616F7C;
            line-height: 1.6;
            margin-bottom: 35px;
        }

        /* --- RETURN ACTION BUTTON --- */
        .btn-return {
            display: inline-block;
            background: var(--cathedral-blue);
            color: var(--soft-gold);
            font-family: var(--font-serif);
            text-decoration: none;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 2px;
            padding: 14px 35px;
            border-radius: 6px;
            border: 1px solid var(--liturgical-gold);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(10, 25, 47, 0.15);
        }

        .btn-return:hover {
            background: var(--ecclesia-charcoal);
            color: white;
            border-color: var(--ecclesia-charcoal);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(92, 98, 102, 0.3);
        }
    </style>
</head>
<body>

<!-- ANIMATED BACKGROUND -->
<div class="ambient-background">
    <div class="ember ember-1"></div>
    <div class="ember ember-2"></div>
</div>

<!-- MAIN STATUS DISPLAY CARD -->
<div class="receipt-card">

    <div class="status-icon">
        <?php if($status === "success"): ?>
            <!-- EXCLUSIVE CATHOLIC ASHEN MONUMENT CROSS FOR COMPLETED WITHDRAWAL -->
            <svg class="icon-cancellation" viewBox="0 0 64 64">
                <path d="M30 14h4v12h-4zm-12 12h28v4H18zm12 4h4v20h-4z"/>
                <!-- Subtle structural diagonal indicators hinting at removal/alteration -->
                <path d="M21 43l22-22m0 22L21 21" stroke="var(--ecclesia-charcoal)" stroke-width="2" stroke-linecap="round" opacity="0.3"/>
            </svg>
        <?php else: ?>
            <!-- WARNING CHRISM/SHIELD BADGE FOR ERROR ENCOUNTERS -->
            <svg class="icon-error" viewBox="0 0 64 64">
                <path d="M32 4S14 8 14 28c0 15 18 32 18 32s18-17 18-32c0-20-18-24-18-24zm0 46V14c5 2 11 6 11 14 0 9-11 18-11 18z"/>
            </svg>
        <?php endif; ?>
    </div>

    <h1><?php echo htmlspecialchars($message); ?></h1>
    <p><?php echo htmlspecialchars($subtitle); ?></p>

    <a href="my_events.php" class="btn-return">Return to My Events</a>

</div>

</body>
</html>