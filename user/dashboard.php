<?php
include("../config.php");
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user = $_SESSION['user'];

/* COUNT REGISTERED EVENTS */
$stmt = $conn->prepare("SELECT COUNT(*) as total_events FROM registrations WHERE user_id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$events = $row['total_events'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sacred Heart Portal | Devotional Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700;900&family=Cinzel:wght@500;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            /* PREMIUM SACRED HEART COLOR PALETTE */
            --sacred-crimson: #030508;
            --heart-burgundy: #0A1424;
            --devotional-red: #7a1c1c;
            --metallic-gold: #D4AF37;
            --bright-gold: #F3E5AB;
            --heavenly-white: #FFFFFF;
            --ivory-cream: #FDFBF7;
            --platinum-silver: #E5E9F0;
            --candle-amber: #FF9F43;
            --fire-glow: #ff4d4d;
            
            /* NEURAL GLASS SURFACES */
            --glass-card: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(212, 175, 55, 0.15);
            --glow-gold: drop-shadow(0 0 15px rgba(212, 175, 55, 0.6));
            --glow-fire: drop-shadow(0 0 15px rgba(255, 77, 77, 0.5));
            
            --font-display: 'Cinzel Decorative', serif;
            --font-serif: 'Cinzel', serif;
            --font-sans: 'Plus Jakarta Sans', sans-serif;
            
            --transition-cinematic: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .light-heavenly-mode {
            --sacred-crimson: #F4F6F9;
            --heart-burgundy: #E2E8F0;
            --devotional-red: #FFFFFF;
            --metallic-gold: #B38F1D;
            --bright-gold: #D4AF37;
            --heavenly-white: #0A1424;
            --ivory-cream: #1E293B;
            --platinum-silver: #334155;
            --glass-card: rgba(10, 20, 36, 0.03);
            --glass-border: rgba(179, 143, 29, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            user-select: none;
        }

        body {
            background-color: var(--sacred-crimson);
            font-family: var(--font-sans);
            color: var(--platinum-silver);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            transition: background 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* --- IMMERSIVE BACKGROUND ENGINE --- */
        #cathedralAtmosphereEngine {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
        }

        .heavenly-fog-layer {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% -20%, rgba(255, 77, 77, 0.08) 0%, rgba(10, 20, 36, 0) 50%),
                        radial-gradient(circle at 10% 90%, rgba(212, 175, 55, 0.05) 0%, rgba(3, 5, 8, 0) 40%);
            z-index: 1;
            pointer-events: none;
        }

        /* --- PREMIUM FRAMEWORK BORDERS & ACCENTS --- */
        .vatican-ornament-frame {
            position: fixed;
            top: 12px; left: 12px; right: 12px; bottom: 12px;
            border: 1px solid var(--glass-border);
            pointer-events: none;
            z-index: 999;
            border-radius: 20px;
        }
        .vatican-ornament-frame::before {
            content: '';
            position: absolute;
            top: 4px; left: 4px; right: 4px; bottom: 4px;
            border: 1px solid rgba(212, 175, 55, 0.03);
            border-radius: 16px;
        }

        /* --- SIDEBAR NAVIGATION --- */
        .sidebar {
            width: 320px;
            height: 100vh;
            background: linear-gradient(180deg, rgba(10, 20, 36, 0.85) 0%, rgba(3, 5, 8, 0.95) 100%);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-right: 1px solid var(--glass-border);
            padding: 50px 30px;
            display: flex;
            flex-direction: column;
            position: fixed;
            z-index: 100;
            transition: var(--transition-cinematic);
        }

        .sidebar-brand {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }

        .sacred-heart-pulse-node {
            width: 70px; height: 70px;
            margin: 0 auto 15px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.2) 0%, transparent 70%);
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            position: relative;
            animation: divineHeartbeat 2.5s infinite cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sacred-heart-pulse-node svg {
            width: 38px; height: 38px;
            fill: var(--metallic-gold);
            filter: var(--glow-gold);
        }

        @keyframes divineHeartbeat {
            0% { transform: scale(1); opacity: 0.9; }
            15% { transform: scale(1.12); opacity: 1; filter: drop-shadow(0 0 25px rgba(212, 175, 55, 0.8)); }
            30% { transform: scale(0.96); opacity: 0.9; }
            45% { transform: scale(1.08); opacity: 1; }
            100% { transform: scale(1); opacity: 0.9; }
        }

        .sidebar h2 {
            font-family: var(--font-serif);
            color: var(--bright-gold);
            font-size: 18px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .sidebar p {
            font-size: 10px;
            letter-spacing: 2px;
            color: var(--fire-glow);
            text-transform: uppercase;
            margin-top: 5px;
            font-weight: 700;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 20px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            padding: 16px 20px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 1px;
            border: 1px solid transparent;
            transition: var(--transition-cinematic);
            position: relative;
            overflow: hidden;
        }

        .sidebar a svg {
            width: 20px; height: 20px; fill: currentColor; transition: var(--transition-cinematic);
        }

        .sidebar a:hover, .sidebar a.active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.08) 0%, rgba(255, 77, 77, 0.02) 100%);
            border-color: var(--glass-border);
            color: var(--bright-gold);
            padding-left: 28px;
            box-shadow: inset 0 0 15px rgba(212, 175, 55, 0.05);
        }

        .sidebar a:hover svg, .sidebar a.active svg {
            color: var(--fire-glow);
            filter: var(--glow-fire);
            transform: scale(1.1);
        }

        /* --- MAIN WORKSPACE INTERFACE --- */
        .main-sanctuary {
            margin-left: 320px;
            width: calc(100% - 320px);
            padding: 60px;
            position: relative;
            z-index: 10;
        }

        .sanctuary-centerpiece-header {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 50px;
            position: relative;
        }

        .glowing-centerpiece-rood {
            width: 80px; height: 110px;
            filter: drop-shadow(0 0 20px rgba(212, 175, 55, 0.6)) var(--glow-fire);
            animation: roodGloryOscillation 5s infinite ease-in-out;
        }

        .glowing-centerpiece-rood svg {
            width: 100%; height: 100%;
            fill: url(#goldGradientLuna);
        }

        @keyframes roodGloryOscillation {
            0%, 100% { transform: scale(1) translateY(0); filter: drop-shadow(0 0 20px rgba(212, 175, 55, 0.6)); }
            50% { transform: scale(1.06) translateY(-5px); filter: drop-shadow(0 0 40px rgba(255, 77, 77, 0.9)); }
        }

        /* --- HERO HEADER GRID --- */
        .sanctuary-hero-banner {
            width: 100%;
            background: linear-gradient(135deg, rgba(122, 28, 28, 0.3) 0%, rgba(10, 20, 36, 0.7) 100%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 60px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.4);
            display: grid;
            grid-template-columns: 1.3fr 1.1fr;
            gap: 50px;
            align-items: center;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            margin-bottom: 40px;
        }

        .gothic-arch-overlay-path {
            position: absolute;
            right: 0; top: 0; bottom: 0;
            width: 45%;
            opacity: 0.08;
            stroke: var(--metallic-gold);
            stroke-width: 1.5;
            fill: none;
            pointer-events: none;
        }

        .hero-welcome-meta h1 {
            font-family: var(--font-display);
            font-size: 46px;
            color: #FFFFFF;
            line-height: 1.1;
            margin-bottom: 15px;
            text-shadow: 0 0 30px rgba(255,255,255,0.2);
        }

        .hero-welcome-meta h1 span {
            color: var(--bright-gold);
            font-family: var(--font-serif);
        }

        .hero-welcome-meta p {
            font-size: 14px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--fire-glow);
            font-weight: 600;
            margin-bottom: 30px;
        }

        .altar-votive-bench {
            display: flex;
            gap: 20px;
            align-items: flex-end;
            margin-bottom: 15px;
        }

        .sanctuary-candle-votive {
            width: 14px;
            background: linear-gradient(180deg, rgba(255,255,255,0.8) 0%, rgba(212,175,55,0.4) 100%);
            border-radius: 4px 4px 2px 2px;
            position: relative;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .votive-flame-core {
            position: absolute;
            top: -16px; left: 50%; transform: translateX(-50%);
            width: 12px; height: 20px;
            background: radial-gradient(circle at bottom, #FFF 20%, var(--candle-amber) 60%, transparent 90%);
            border-radius: 50% 50% 35% 35%;
            filter: drop-shadow(0 0 12px var(--candle-amber));
            animation: dynamicVotiveFlicker 0.15s infinite alternate ease-in-out;
            transform-origin: bottom center;
        }

        @keyframes dynamicVotiveFlicker {
            0% { transform: translateX(-50%) scale(1) rotate(-2deg); opacity: 0.9; }
            100% { transform: translateX(-50%) scale(1.15) rotate(3deg); opacity: 1; }
        }

        /* --- SCRIPTURE MANUSCRIPT SHRINE --- */
        .manuscript-scripture-shrine {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            box-shadow: inset 0 0 30px rgba(212, 175, 55, 0.03);
            position: relative;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .manuscript-scripture-shrine::before {
            content: '†';
            position: absolute;
            top: -15px; left: 50%; transform: translateX(-50%);
            background: var(--heart-burgundy);
            width: 30px; height: 30px;
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: var(--bright-gold);
            font-family: var(--font-serif);
        }

        .manuscript-text {
            font-family: var(--font-sans);
            font-weight: 300;
            font-style: italic;
            line-height: 1.8;
            font-size: 16px;
            color: #FFFFFF;
            transition: opacity 0.5s ease-in-out;
            opacity: 1;
        }

        .manuscript-reference {
            display: block;
            margin-top: 20px;
            font-family: var(--font-serif);
            color: var(--bright-gold);
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            transition: opacity 0.5s ease-in-out;
            opacity: 1;
        }

        .fade-out {
            opacity: 0;
        }

        /* --- DASHBOARD MATRIX GRID --- */
        .sanctuary-dashboard-matrix {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 35px;
            margin-bottom: 40px;
        }

        .premium-shrine-panel {
            background: var(--glass-card);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 35px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.15);
            transition: var(--transition-cinematic);
        }

        .premium-shrine-panel:hover {
            transform: translateY(-8px);
            border-color: var(--bright-gold);
            box-shadow: 0 30px 60px rgba(212, 175, 55, 0.12);
        }

        .panel-corner-leaf-tl {
            position: absolute; top: 12px; left: 12px;
            width: 10px; height: 10px;
            border-top: 1px solid var(--glass-border);
            border-left: 1px solid var(--glass-border);
        }
        .panel-corner-leaf-br {
            position: absolute; bottom: 12px; right: 12px;
            width: 10px; height: 10px;
            border-bottom: 1px solid var(--glass-border);
            border-right: 1px solid var(--glass-border);
        }

        .panel-header-cluster {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .panel-header-cluster h3 {
            font-family: var(--font-serif);
            font-size: 16px;
            letter-spacing: 2px;
            color: #FFFFFF;
            text-transform: uppercase;
        }

        .panel-header-icon {
            width: 45px; height: 45px;
            background: rgba(212, 175, 55, 0.05);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--metallic-gold);
            border: 1px solid rgba(212, 175, 55, 0.15);
        }
        .panel-header-icon svg { width: 22px; height: 22px; fill: currentColor; }

        /* --- SACRAMENT FLOW SYSTEM --- */
        .sacrament-flow-line {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
        }
        .sacrament-flow-line::before {
            content: '';
            position: absolute;
            left: 17px; top: 0; bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, var(--metallic-gold) 0%, rgba(255,255,255,0.05) 100%);
        }

        .sacrament-step-node {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
        }

        .step-indicator-dot {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--heart-burgundy);
            border: 2px solid var(--metallic-gold);
            display: flex; align-items: center; justify-content: center;
            z-index: 2;
            font-size: 11px;
            color: var(--bright-gold);
        }
        .step-indicator-dot.completed {
            background: var(--metallic-gold);
            color: #000;
            box-shadow: 0 0 10px var(--metallic-gold);
        }

        .step-meta h4 {
            font-size: 14px;
            font-weight: 600;
            color: #FFFFFF;
        }
        .step-meta p {
            font-size: 11px;
            color: var(--fire-glow);
            font-weight: 500;
        }

        /* --- BROADCAST MODULE --- */
        .live-stream-window {
            width: 100%;
            height: 170px;
            border-radius: 14px;
            background: linear-gradient(0deg, rgba(0,0,0,0.6) 0%, transparent 100%), url('https://images.unsplash.com/photo-1548625361-155deea22300?auto=format&fit=crop&q=80&w=600') center/cover;
            position: relative;
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: flex-end;
            padding: 20px;
        }
        
        .stream-broadcast-badge {
            position: absolute;
            top: 15px; left: 15px;
            background: #FF7675;
            color: white;
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            animation: pulseStreamBadge 1.5s infinite alternate;
        }

        @keyframes pulseStreamBadge {
            0% { opacity: 0.6; box-shadow: 0 0 0 0 rgba(255,118,117,0.7); }
            100% { opacity: 1; box-shadow: 0 0 15px 4px rgba(255,118,117,0.3); }
        }

        .stream-trigger-button {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: var(--bright-gold);
            color: #000;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            border: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            margin-right: 15px;
            transition: var(--transition-cinematic);
        }
        .stream-trigger-button:hover { transform: scale(1.1); background: #FFF; }

        .insight-metric-giant-p {
            font-size: 56px;
            font-family: var(--font-serif);
            font-weight: 700;
            color: var(--bright-gold);
            line-height: 1;
            text-shadow: 0 0 20px rgba(212,175,55,0.3);
            margin-top: 10px;
        }

        /* --- FORMS INTERFACE --- */
        .prayer-shrine-input-cluster {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .prayer-shrine-input-cluster textarea {
            width: 100%; height: 90px;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 15px;
            color: white;
            font-family: var(--font-sans);
            resize: none;
            outline: none;
            transition: var(--transition-cinematic);
        }
        .prayer-shrine-input-cluster textarea:focus {
            border-color: var(--fire-glow);
            box-shadow: 0 0 15px rgba(255, 77, 77, 0.15);
        }
        .liturgical-submit-btn {
            background: linear-gradient(90deg, var(--metallic-gold) 0%, var(--bright-gold) 100%);
            border: none; border-radius: 10px;
            padding: 12px; color: #000;
            font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
            cursor: pointer; font-size: 12px; transition: var(--transition-cinematic);
        }
        .liturgical-submit-btn:hover {
            transform: translateY(-2px); box-shadow: 0 10px 20px rgba(212,175,55,0.3);
        }

        /* --- RESPONSIVE LAYOUT SYSTEMS --- */
        @media (max-width: 1400px) {
            .sanctuary-dashboard-matrix { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 1024px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: relative; padding: 30px; }
            .main-sanctuary { margin-left: 0; width: 100%; padding: 30px 20px; }
            .sanctuary-hero-banner { grid-template-columns: 1fr; padding: 40px; }
            .sanctuary-dashboard-matrix { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

<div class="heavenly-fog-layer"></div>
<canvas id="cathedralAtmosphereEngine"></canvas>

<div class="vatican-ornament-frame"></div>

<div class="sidebar">
    <div class="sidebar-brand">
        <div class="sacred-heart-pulse-node">
            <svg viewBox="0 0 64 64">
                <path d="M32 16c-4.5-6-13.5-6-18 0-6.3 7.2-3.6 19.8 18 34.2 21.6-14.4 24.3-27 18-34.2-4.5-6-13.5-6-18 0z"/>
                <path d="M32 6v6M27 9h10" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>
        <h2>Sacred Heart Portal</h2>
        <p>Devotional Network Infrastructure</p>
    </div>

    <div class="sidebar-menu">
        <a href="dashboard.php" class="active">
            <svg viewBox="0 0 24 24"><path d="M4 13h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v8c0 .55.45 1 1 1zm0 8h6c.55 0 1-.45 1-1v-4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1zm10 0h6c.55 0 1-.45 1-1v-8c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1v8c0 .55.45 1 1 1zM14 4v4c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1z"/></svg>
            Devotion Hub
        </a>
        <a href="view_events.php">
            <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
            Liturgical Events
        </a>
        <a href="my_events.php">
            <svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
            My Sacred Mass
            <span style="margin-left:auto; background:var(--metallic-gold); color:#000; padding:2px 6px; border-radius:6px; font-size:10px; font-weight:800;"><?php echo $events; ?></span>
        </a>
        <a href="view_announcements.php">
            <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            Devotional Bulletins
        </a>
        <a href="profile.php">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
            Member Profile
        </a>
        <a href="../auth/logout.php" style="color: #FF7675; margin-top: 40px; border-top: 1px solid rgba(255,118,117,0.1); border-radius: 0; padding-top: 25px;">
            <svg viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
            Disconnect Portal
        </a>
    </div>
</div>

<div class="main-sanctuary">

    <svg style="width:0; height:0; position:absolute;">
        <defs>
            <linearGradient id="goldGradientLuna" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#D4AF37" />
                <stop offset="50%" stop-color="#F3E5AB" />
                <stop offset="100%" stop-color="#AA7C11" />
            </linearGradient>
        </defs>
    </svg>

    <div class="sanctuary-centerpiece-header">
        <div class="glowing-centerpiece-rood">
            <svg viewBox="0 0 24 24">
                <path d="M11 2h2v4h4v2h-4v6h6v2h-6v6h-2v-6H5v-2h6V6H7V4h4V2z"/>
            </svg>
        </div>
    </div>

    <div class="sanctuary-hero-banner">
        <svg class="gothic-arch-overlay-path" viewBox="0 0 100 100" preserveAspectRatio="none">
            <path d="M0,100 V40 C0,15 50,0 50,0 C50,0 100,15 100,40 V100 M50,0 V100 M0,30 Q50,50 100,30" />
        </svg>

        <div class="hero-welcome-meta">
            <div class="altar-votive-bench">
                <div class="sanctuary-candle-votive" style="height: 30px;"><div class="votive-flame-core"></div></div>
                <div class="sanctuary-candle-votive" style="height: 45px;"><div class="votive-flame-core"></div></div>
                <div class="sanctuary-candle-votive" style="height: 25px;"><div class="votive-flame-core"></div></div>
            </div>
            <h1>Peace be with you,<br><span><?php echo htmlspecialchars($user['name']); ?></span></h1>
            <p>Parish Communion Tier III • Devotion Verified</p>
            
            <button class="liturgical-submit-btn" style="padding: 12px 25px;" onclick="toggleHeavenlyTheme()">
                ⚡ Toggle Devotional Lighting Atmosphere
            </button>
        </div>

        <div class="manuscript-scripture-shrine">
            <p class="manuscript-text" id="scriptureText">
                "In the beginning was the Word, and the Word was with God, and the Word was God."
            </p>
            <span class="manuscript-reference" id="scriptureRef">
                John 1:1
            </span>
        </div>
    </div>

    <div class="sanctuary-dashboard-matrix">
        
        <div class="premium-shrine-panel">
            <div class="panel-corner-leaf-tl"></div><div class="panel-corner-leaf-br"></div>
            <div class="panel-header-cluster">
                <h3>Sacrament Journey</h3>
                <div class="panel-header-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2c-5.52 0-10 4.48-10 10s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-4H9V9h2V7h2v2h2v3h-2v4z"/></svg>
                </div>
            </div>
            <div class="sacrament-flow-line">
                <div class="sacrament-step-node">
                    <div class="step-indicator-dot completed">I</div>
                    <div class="step-meta"><h4>Holy Baptism</h4><p>Completed Status</p></div>
                </div>
                <div class="sacrament-step-node">
                    <div class="step-indicator-dot completed">II</div>
                    <div class="step-meta"><h4>First Communion</h4><p>Eucharist Access Unlocked</p></div>
                </div>
                <div class="sacrament-step-node">
                    <div class="step-indicator-dot">III</div>
                    <div class="step-meta"><h4>Confirmation</h4><p>Scheduled Pentecost 2026</p></div>
                </div>
            </div>
        </div>

        <div class="premium-shrine-panel">
            <div class="panel-corner-leaf-tl"></div><div class="panel-corner-leaf-br"></div>
            <div class="panel-header-cluster">
                <h3>Mass Engagements</h3>
                <div class="panel-header-icon">
                    <svg viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                </div>
            </div>
            <div class="insight-metric-giant-p"><?php echo $events; ?></div>
            <p style="font-size:12px; opacity:0.6; margin-top:5px; text-transform:uppercase; letter-spacing:1px;">Active Liturgical Encounters Joined</p>
            <div style="width:100%; height:110px; margin-top:15px;">
                <canvas id="shrineTelemetryMiniChart"></canvas>
            </div>
        </div>

        <div class="premium-shrine-panel">
            <div class="panel-corner-leaf-tl"></div><div class="panel-corner-leaf-br"></div>
            <div class="panel-header-cluster">
                <h3>Sacred Heart Live Stream</h3>
                <div class="panel-header-icon">
                    <svg viewBox="0 0 24 24"><path d="M21 6h-7.59l3.29-3.29L16 2l-4 4-4-4-.71.71L10.59 6H3c-1.1 0-2 .89-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.11-.9-2-2-2zm0 14H3V8h18v12z"/></svg>
                </div>
            </div>
            <div class="live-stream-window">
                <div class="stream-broadcast-badge">Live Broadcast</div>
                <button class="stream-trigger-button" onclick="alert('Initializing Secure Devotional Stream Feed...')">
                    <svg style="width:20px; height:20px; fill:currentColor;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </button>
                <div>
                    <h4 style="font-size:14px; color:#fff; font-weight:700;">Solemn High Mass</h4>
                    <p style="font-size:11px; color:var(--bright-gold);">Sacred Heart Chapel</p>
                </div>
            </div>
        </div>

    </div>

    <div class="sanctuary-dashboard-matrix" style="grid-template-columns: 2fr 1fr;">
        <div class="premium-shrine-panel">
            <div class="panel-corner-leaf-tl"></div><div class="panel-corner-leaf-br"></div>
            <div class="panel-header-cluster">
                <h3>Oratory Intentions Request Center</h3>
            </div>
            <div class="prayer-shrine-input-cluster">
                <textarea placeholder="Transmit your prayer intentions to the devotional altar..."></textarea>
                <button class="liturgical-submit-btn">Send Intention to High Altar</button>
            </div>
        </div>
        
        <div class="premium-shrine-panel">
            <div class="panel-corner-leaf-tl"></div><div class="panel-corner-leaf-br"></div>
            <div class="panel-header-cluster">
                <h3>Devotional Notifications</h3>
                <span style="background:rgba(212, 175, 55, 0.15); color:var(--bright-gold); border:1px solid var(--metallic-gold); padding:2px 8px; border-radius:20px; font-size:10px; font-weight:800;">2 NEW</span>
            </div>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <p style="font-size:13px;">• Novena begins tomorrow morning.</p>
                <p style="font-size:13px;">• First Friday adoration scheduling is open.</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleHeavenlyTheme() {
    document.body.classList.toggle('light-heavenly-mode');
}

/* --- DYNAMIC BIBLE VERSE ENGINE --- */
const scriptureLibrary = [
    { text: '"In the beginning was the Word, and the Word was with God, and the Word was God."', ref: "John 1:1" },
    { text: '"The Lord is my shepherd; I shall not want. He makes me lie down in green pastures."', ref: "Psalm 23:1-2" },
    { text: '"For I know the plans I have for you, declares the Lord, plans for welfare and not for evil."', ref: "Jeremiah 29:11" },
    { text: '"I can do all things through him who strengthens me."', ref: "Philippians 4:13" },
    { text: '"But they who wait for the Lord shall renew their strength; they shall mount up with wings like eagles."', ref: "Isaiah 40:31" },
    { text: '"Be strong and courageous. Do not be frightened, and do not be dismayed, for the Lord your God is with you."', ref: "Joshua 1:9" }
];

let currentVerseIndex = 0;
const textNode = document.getElementById("scriptureText");
const refNode = document.getElementById("scriptureRef");

function cycleScriptureManuscript() {
    // Graceful fade transition out
    textNode.classList.add("fade-out");
    refNode.classList.add("fade-out");
    
    setTimeout(() => {
        currentVerseIndex = (currentVerseIndex + 1) % scriptureLibrary.length;
        textNode.innerText = scriptureLibrary[currentVerseIndex].text;
        refNode.innerText = scriptureLibrary[currentVerseIndex].ref;
        
        // Graceful fade transition back in
        textNode.classList.remove("fade-out");
        refNode.classList.remove("fade-out");
    }, 500); 
}
// Automatically cycle the database of scriptures every 7 seconds
setInterval(cycleScriptureManuscript, 7000);


/* --- CATHEDRAL ATMOSPHERE PARTICLE ENGINE (INCENSE PARTICLES) --- */
const canvas = document.getElementById('cathedralAtmosphereEngine');
const ctx = canvas.getContext('2d');

let particlesArray = [];

function resizeAtmosphereCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
window.addEventListener('resize', resizeAtmosphereCanvas);
resizeAtmosphereCanvas();

class IncenseParticle {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = canvas.height + Math.random() * 100;
        this.size = Math.random() * 2.5 + 0.5;
        this.speedX = Math.sin(Math.random() * 2) * 0.4;
        this.speedY = -(Math.random() * 0.7 + 0.3);
        this.opacity = Math.random() * 0.5 + 0.1;
        this.glowingPulse = Math.random() * 0.02;
    }
    update() {
        this.x += this.speedX;
        this.y += this.speedY;
        
        // Create an organic swaying back and forth movement vector
        this.speedX += Math.sin(this.y * 0.01) * 0.01;

        // Animate the gold glow opacity pulsing dynamically
        this.opacity += this.glowingPulse;
        if (this.opacity > 0.6 || this.opacity < 0.1) {
            this.glowingPulse = -this.glowingPulse;
        }

        // Recycle particles once they exit the cathedral roof boundaries
        if (this.y < -10) {
            this.y = canvas.height + 10;
            this.x = Math.random() * canvas.width;
            this.speedY = -(Math.random() * 0.7 + 0.3);
        }
    }
    draw() {
        ctx.save();
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        
        // Use gold amber tones for dark mode, subtle platinum nodes for light heavenly mode
        const isLightMode = document.body.classList.contains('light-heavenly-mode');
        ctx.fillStyle = isLightMode ? `rgba(179, 143, 29, ${this.opacity})` : `rgba(243, 229, 171, ${this.opacity})`;
        
        // Emulate soft real-time candle illumination shadows
        if(!isLightMode) {
            ctx.shadowBlur = this.size * 4;
            ctx.shadowColor = "rgba(214, 175, 55, 0.4)";
        }
        ctx.fill();
        ctx.restore();
    }
}

function initAtmosphereEngine() {
    particlesArray = [];
    // Adjust density based on screen configuration real estate
    const particleDensityCount = Math.floor((canvas.width * canvas.height) / 12000);
    for (let i = 0; i < Math.min(particleDensityCount, 120); i++) {
        particlesArray.push(new IncenseParticle());
    }
}

function runAtmosphereLoop() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (let i = 0; i < particlesArray.length; i++) {
        particlesArray[i].update();
        particlesArray[i].draw();
    }
    requestAnimationFrame(runAtmosphereLoop);
}

initAtmosphereEngine();
runAtmosphereLoop();
</script>

</body>
</html>