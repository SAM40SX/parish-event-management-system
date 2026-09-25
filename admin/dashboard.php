<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Core | Palace Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ==========================================================================
           PREMIUM DESIGN SYSTEM VARIABLES (IMPERIAL VELVET & COUTURE GOLD)
           ========================================================================== */
        :root[data-theme="dark"] {
            --bg-base: #0a0709;                 
            --panel-bg: rgba(18, 13, 16, 0.75); 
            --panel-border: rgba(212, 175, 55, 0.2); 
            --accent-gold: #d4af37;             
            --accent-gold-glow: rgba(212, 175, 55, 0.35);
            --text-main: #fcfbfa;               
            --text-muted: #a39e93;              
            --surface-overlay: rgba(255, 255, 255, 0.02);
            --shadow-premium: 0 30px 60px rgba(0, 0, 0, 0.8), 0 0 40px rgba(212, 175, 55, 0.03);
            --gradient-accent: linear-gradient(135deg, #d4af37 0%, #aa7c11 100%);
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        :root[data-theme="light"] {
            --bg-base: #f4f1ea;                 
            --panel-bg: rgba(255, 255, 255, 0.9);
            --panel-border: rgba(170, 124, 17, 0.25); 
            --accent-gold: #8e6810;             
            --accent-gold-glow: rgba(142, 104, 16, 0.15);
            --text-main: #1c1813;               
            --text-muted: #665f53;              
            --surface-overlay: rgba(0, 0, 0, 0.02);
            --shadow-premium: 0 20px 40px rgba(142, 104, 16, 0.06);
            --gradient-accent: linear-gradient(135deg, #a8801c 0%, #6d4f0b 100%);
            --error-color: #dc2626;
            --success-color: #059669;
        }

        /* ==========================================================================
           GLOBAL RESETS, ACCESSIBILITY, & FONTS
           ========================================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background 0.25s ease, border 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
        }

        body {
            background-color: var(--bg-base);
            min-height: 100vh;
            color: var(--text-main);
            overflow-x: hidden;
            position: relative;
        }

        *:focus-visible {
            outline: 2px dashed var(--accent-gold);
            outline-offset: 4px;
        }

        /* Ambient Layer & Animation Elements */
        .premium-ambience {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .premium-ambience::before {
            content: '';
            position: absolute;
            top: -25%; left: -25%; width: 80%; height: 80%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, transparent 60%);
            filter: blur(120px);
        }

        #doveAnimationCanvas {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none;
            z-index: 1;
            mix-blend-mode: screen;
        }

        /* ==========================================================================
           SACRED SANCTUARY SIGN & SCRIPTURE SCROLLER COMPONENTS
           ========================================================================== */
        .sanctuary-altar-sign {
            background: linear-gradient(180deg, rgba(212, 175, 55, 0.05) 0%, rgba(0, 0, 0, 0.2) 100%);
            border: 1px solid var(--panel-border);
            border-top: 2px solid var(--accent-gold);
            border-radius: 16px;
            padding: 24px 30px;
            box-shadow: var(--shadow-premium), inset 0 1px 0 rgba(212, 175, 55, 0.1);
            position: relative;
            display: flex;
            align-items: center;
            gap: 24px;
            overflow: hidden;
        }

        .altar-sign-crest {
            color: var(--accent-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            width: 54px;
            height: 54px;
            border-radius: 12px;
            box-shadow: 0 0 15px var(--accent-gold-glow);
        }

        .altar-sign-crest svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.5;
        }

        .scripture-display-box {
            flex: 1;
        }

        .scripture-meta {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--accent-gold);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .scripture-text-element {
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            font-weight: 500;
            color: var(--text-main);
            line-height: 1.4;
            font-style: italic;
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .scripture-text-element.fade-transition {
            opacity: 0;
            transform: translateY(-4px);
        }

        /* ==========================================================================
           MAIN STRUCTURAL LAYOUT
           ========================================================================== */
        .executive-container {
            display: flex;
            max-width: 1600px;
            margin: 0 auto;
            padding: 40px;
            gap: 32px;
            position: relative;
            z-index: 5;
        }

        /* ==========================================================================
           PREMIUM NAVIGATION ARCHITECTURE (COLLAPSIBLE SIDEBAR)
           ========================================================================== */
        .executive-sidebar {
            width: 280px;
            background: var(--panel-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--panel-border);
            border-radius: 20px;
            padding: 30px 20px;
            box-shadow: var(--shadow-premium);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 80px);
            position: sticky;
            top: 40px;
            flex-shrink: 0;
            will-change: width;
            transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .executive-sidebar.collapsed { width: 88px; }

        .luxury-frame {
            position: absolute;
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            border: 1px solid rgba(212, 175, 55, 0.06);
            border-radius: 14px;
            pointer-events: none;
        }

        .sidebar-brand {
            text-align: center;
            margin-bottom: 30px;
            white-space: nowrap;
            overflow: hidden;
        }

        .executive-sidebar.collapsed .sidebar-brand h2,
        .executive-sidebar.collapsed .sidebar-brand p {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar-brand h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 2px;
            color: var(--accent-gold);
            text-transform: uppercase;
            transition: opacity 0.2s;
        }

        .sidebar-brand p {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--text-muted);
            margin-top: 4px;
            transition: opacity 0.2s;
        }

        .navigation-links {
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .navigation-links::-webkit-scrollbar { width: 3px; }
        .navigation-links::-webkit-scrollbar-thumb { background: rgba(212, 175, 55, 0.1); border-radius: 4px; }

        .navigation-links a {
            display: flex;
            align-items: center;
            gap: 16px;
            color: var(--text-muted);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
        }

        .navigation-links a svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.75;
            flex-shrink: 0;
        }

        .navigation-links a:hover {
            color: var(--text-main);
            background: rgba(212, 175, 55, 0.04);
            transform: translateX(4px);
        }

        .navigation-links a.active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.12) 0%, rgba(212, 175, 55, 0.02) 100%);
            color: var(--accent-gold);
            font-weight: 600;
            border-left: 2px solid var(--accent-gold);
        }

        .executive-sidebar.collapsed .link-text { display: none; }

        /* ==========================================================================
           MAIN CONTENT SYSTEM INTERFACE & GRID SYSTEMS
           ========================================================================== */
        .executive-viewport {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 32px;
            min-width: 0;
        }

        .executive-hero-banner {
            background: var(--panel-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--panel-border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow-premium);
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hero-welcome h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 500;
        }

        .hero-welcome h1 span {
            color: var(--accent-gold);
            font-style: italic;
        }

        .hero-welcome p {
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 4px;
        }

        .hero-actions { display: flex; align-items: center; gap: 12px; }

        .action-circle-btn {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--surface-overlay);
            border: 1px solid var(--panel-border);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .action-circle-btn:hover {
            border-color: var(--accent-gold);
            color: var(--accent-gold);
            box-shadow: 0 0 15px var(--accent-gold-glow);
            transform: translateY(-2px);
        }

        .action-circle-btn svg { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 1.75; }

        /* Metric Dashboard Grid System */
        .executive-grid-suite {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
        }

        .executive-metric-card {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-premium);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 160px;
            overflow: hidden;
        }

        .executive-metric-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-gold);
        }

        .metric-header { display: flex; justify-content: space-between; align-items: center; }
        .metric-header h3 { font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); }
        .metric-icon-wrapper { color: var(--accent-gold); }
        .metric-icon-wrapper svg { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 1.75; }
        .metric-value { font-family: 'Cormorant Garamond', serif; font-size: 38px; font-weight: 600; color: var(--accent-gold); }
        .metric-trend { font-size: 11px; color: var(--success-color); background: rgba(16, 185, 129, 0.08); padding: 2px 8px; border-radius: 12px; }

        .executive-card-canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; opacity: 0.12; }

        /* ==========================================================================
           ADVANCED SPLIT INTERFACE GRID (FORMS, TABLES, ANALYTICS)
           ========================================================================== */
        .executive-split-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }

        @media (max-width: 1024px) {
            .executive-split-grid { grid-template-columns: 1fr; }
        }

        .executive-panel-card {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 18px;
            padding: 30px;
            box-shadow: var(--shadow-premium);
            position: relative;
        }

        .panel-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px;
            font-weight: 600;
            color: var(--accent-gold);
            margin-bottom: 24px;
            border-bottom: 1px solid var(--panel-border);
            padding-bottom: 10px;
        }

        /* Form Controls Styling */
        .executive-form-group { margin-bottom: 20px; position: relative; }
        .executive-form-group label {
            display: block; font-size: 12px; text-transform: uppercase;
            letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px; font-weight: 600;
        }

        .executive-input {
            width: 100%; background: rgba(0, 0, 0, 0.15); border: 1px solid var(--panel-border);
            border-radius: 8px; padding: 12px 16px; color: var(--text-main); font-size: 14px;
        }

        .executive-input:focus {
            outline: none; border-color: var(--accent-gold);
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.15); background: rgba(0, 0, 0, 0.3);
        }

        .input-error-msg { font-size: 11px; color: var(--error-color); margin-top: 6px; display: none; }
        .executive-input.invalid { border-color: var(--error-color) !important; }

        .btn-premium {
            background: var(--gradient-accent); border: none; color: #000; font-weight: 700;
            font-size: 13px; text-transform: uppercase; letter-spacing: 1px; padding: 14px 28px;
            border-radius: 8px; cursor: pointer; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        }

        .btn-premium:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(212, 175, 55, 0.35); }
        .btn-premium:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }

        /* Data Tables Styling */
        .table-responsive-wrapper { width: 100%; overflow-x: auto; border-radius: 8px; border: 1px solid var(--panel-border); }
        .executive-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        .executive-table th {
            background: rgba(212, 175, 55, 0.06); color: var(--accent-gold); font-weight: 600;
            text-transform: uppercase; font-size: 11px; letter-spacing: 1px; padding: 16px; position: sticky; top: 0; z-index: 2;
        }
        .executive-table td { padding: 14px 16px; border-bottom: 1px solid rgba(212, 175, 55, 0.08); color: var(--text-main); }
        .executive-table tbody tr:nth-child(even) { background: rgba(255, 255, 255, 0.01); }
        .executive-table tbody tr:hover { background: rgba(212, 175, 55, 0.04); }

        /* Compact Operations Calendar Widget */
        .executive-calendar-widget { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; text-align: center; }
        .calendar-day-label { font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600; padding-bottom: 8px; }
        .calendar-cell {
            background: rgba(0, 0, 0, 0.1); border: 1px solid var(--panel-border); border-radius: 6px;
            aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; position: relative;
        }
        .calendar-cell:hover { border-color: var(--accent-gold); background: rgba(212, 175, 55, 0.05); }
        .calendar-cell.has-event { color: var(--accent-gold); font-weight: 700; border-color: rgba(212, 175, 55, 0.5); box-shadow: inset 0 0 8px rgba(212, 175, 55, 0.1); }
        .calendar-cell.has-event::after { content: ''; position: absolute; bottom: 4px; width: 4px; height: 4px; background: var(--accent-gold); border-radius: 50%; }

        /* Analytics Interactive Chart Controls */
        .executive-chart-container { height: 220px; display: flex; align-items: flex-end; gap: 20px; padding: 20px 10px 0 10px; border-left: 1px solid var(--panel-border); border-bottom: 1px solid var(--panel-border); position: relative; }
        .chart-bar-wrapper { flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end; position: relative; }
        .chart-bar { width: 100%; max-width: 32px; background: var(--gradient-accent); border-radius: 4px 4px 0 0; position: relative; cursor: pointer; }
        .chart-bar:hover { filter: brightness(1.2); }
        .chart-tooltip { position: absolute; top: -35px; background: #1c1813; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; border: 1px solid var(--accent-gold); opacity: 0; pointer-events: none; transform: translateY(5px); transition: opacity 0.2s, transform 0.2s; }
        .chart-bar:hover + .chart-tooltip { opacity: 1; transform: translateY(0); }
        .chart-label { font-size: 10px; color: var(--text-muted); margin-top: 8px; text-transform: uppercase; }

        /* Modal Overlays Layout System */
        .executive-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(5, 3, 4, 0.8); backdrop-filter: blur(10px); z-index: 100; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.3s cubic-bezier(0.25, 1, 0.5, 1); }
        .executive-modal-overlay.active { opacity: 1; pointer-events: auto; }
        .executive-modal { background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 20px; width: 100%; max-width: 500px; padding: 32px; box-shadow: var(--shadow-premium); position: relative; transform: scale(0.95) translateY(10px); transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1); }
        .executive-modal-overlay.active .executive-modal { transform: scale(1) translateY(0); }
        .modal-close-btn { position: absolute; top: 24px; right: 24px; background: none; border: none; color: var(--text-muted); cursor: pointer; }
        .modal-close-btn:hover { color: var(--accent-gold); }

        @media (max-width: 1200px) {
            .executive-container { flex-direction: column; padding: 20px; }
            .executive-sidebar { width: 100% !important; height: auto; position: static; }
            .executive-sidebar.collapsed { width: 100%; }
            .executive-sidebar.collapsed .link-text { display: inline; }
            .sidebar-brand { text-align: left; }
        }

        @media (max-width: 768px) {
            .executive-hero-banner { padding: 24px; flex-direction: column; text-align: center; gap: 20px; }
            .executive-grid-suite { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="premium-ambience"></div>
    <canvas id="doveAnimationCanvas"></canvas>

    <div class="executive-container">
        
        <!-- Sidebar Navigation Menu Container -->
        <div class="executive-sidebar" id="mainSidebar" aria-label="Main Navigation">
            <div class="luxury-frame"></div>
            
            <div class="sidebar-brand">
                <h2>Palace Core</h2>
                <p>System Workspace</p>
            </div>

            <div class="navigation-links">
                <a href="dashboard.php" class="active">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                    <span class="link-text">Dashboard Apex</span>
                </a>
                <a href="create_event.php">
                    <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    <span class="link-text">Create Production</span>
                </a>
                <a href="manage_events.php">
                    <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span class="link-text">Manage Registry</span>
                </a>
                <a href="approve_registrations.php">
                    <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    <span class="link-text">Registrations</span>
                </a>
                <a href="qr_attendance.php">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h3v3H7zm7 0h3v3h-7zm0 7h3v3h-3zm-7 0h3v3H7z"/></svg>
                    <span class="link-text">QR Gate Pass</span>
                </a>
                <a href="attendance.php">
                    <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m-2-8h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                    <span class="link-text">Attendance Ledger</span>
                </a>
                <a href="announcements.php">
                    <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    <span class="link-text">Dispatches</span>
                </a>
                <a href="reports.php">
                    <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m16-10a4 4 0 100-8 4 4 0 000 8zm1 14v-2a4 4 0 00-3-3.87m0-7.75a4 4 0 010 7.75"/></svg>
                    <span class="link-text">Analytical Intelligence</span>
                </a>
                <a href="../auth/logout.php" style="color: var(--error-color); margin-top: auto; border-top: 1px solid rgba(239,68,68,0.15); border-radius: 0; padding-top: 20px;">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4m7 14l5-5-5-5m5 5H9"/></svg>
                    <span class="link-text">Terminate Session</span>
                </a>
            </div>
        </div>

        <!-- Main Body Workspace Pane -->
        <div class="executive-viewport">
            
            <div class="executive-hero-banner">
                <div class="luxury-frame"></div>
                
                <div class="hero-welcome">
                    <h1>Welcome, <span><?php echo htmlspecialchars($user['name'] ?? 'Executive'); ?></span></h1>
                    <p>System Administration Mandate</p>
                </div>

                <div class="hero-actions">
                    <button class="action-circle-btn" id="collapseSidebarBtn" title="Toggle Navigation View" aria-label="Collapse Navigation Menu">
                        <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <button class="action-circle-btn" id="openModalExampleBtn" title="Dispatch Live Creation Dialog" aria-label="Open Event Dialog">
                        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    </button>
                    <button class="action-circle-btn" id="premiumThemeEngine" title="Toggle Illuminance" aria-label="Change Color Theme">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    </button>
                </div>
            </div>

            <!-- Sanctuary Altar Liturgical Message Display Board -->
            <div class="sanctuary-altar-sign">
                <div class="luxury-frame"></div>
                <div class="altar-sign-crest" aria-hidden="true">
                    <!-- Premium Styled Latin/Orthodox Sanctuary Cross -->
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2v18M8 7h8M6 11h12" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="scripture-display-box">
                    <div class="scripture-meta" id="scriptureCitation">Proverbs 16:3</div>
                    <div class="scripture-text-element" id="scriptureBody">"Commit your actions to the Eternal, and your plans will succeed."</div>
                </div>
            </div>

            <!-- Metric Analytics Panel Grid -->
            <div class="executive-grid-suite">
                <div class="executive-metric-card">
                    <div class="luxury-frame"></div>
                    <canvas class="executive-card-canvas"></canvas>
                    <div class="metric-header">
                        <h3>Total Engagements</h3>
                        <div class="metric-icon-wrapper"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                    </div>
                    <div class="metric-body">
                        <p class="metric-value">12</p>
                        <span class="metric-trend">+8%</span>
                    </div>
                </div>

                <div class="executive-metric-card">
                    <div class="luxury-frame"></div>
                    <canvas class="executive-card-canvas"></canvas>
                    <div class="metric-header">
                        <h3>Registered Patrons</h3>
                        <div class="metric-icon-wrapper"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                    </div>
                    <div class="metric-body">
                        <p class="metric-value">48</p>
                        <span class="metric-trend">+24%</span>
                    </div>
                </div>

                <div class="executive-metric-card">
                    <div class="luxury-frame"></div>
                    <canvas class="executive-card-canvas"></canvas>
                    <div class="metric-header">
                        <h3>Gate Check-ins</h3>
                        <div class="metric-icon-wrapper"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                    </div>
                    <div class="metric-body">
                        <p class="metric-value">32</p>
                        <span class="metric-trend">+14%</span>
                    </div>
                </div>

                <div class="executive-metric-card">
                    <div class="luxury-frame"></div>
                    <canvas class="executive-card-canvas"></canvas>
                    <div class="metric-header">
                        <h3>Financial Track</h3>
                        <div class="metric-icon-wrapper"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                    </div>
                    <div class="metric-body">
                        <p class="metric-value">$4,250</p>
                        <span class="metric-trend">Stable</span>
                    </div>
                </div>
            </div>

            <!-- Advanced Split Core Content Workspace Layout Grid -->
            <div class="executive-split-grid">
                
                <div class="executive-panel-card">
                    <div class="luxury-frame"></div>
                    <h2 class="panel-title">Production Dispatch Configuration</h2>
                    <form id="productionConfigForm" onsubmit="return validateExecutiveForm(event)">
                        <div class="executive-form-group">
                            <label for="prodTitle">Production Title</label>
                            <input type="text" id="prodTitle" class="executive-input" placeholder="e.g., Autumn Parish Gala Banquet">
                            <div class="input-error-msg" id="prodTitleError">A valid production designation string is required.</div>
                        </div>
                        <div class="executive-form-group">
                            <label for="prodAllocation">Target Financial Allocation Limit ($)</label>
                            <input type="number" id="prodAllocation" class="executive-input" placeholder="e.g., 5000">
                            <div class="input-error-msg" id="prodAllocationError">Allocation configuration metrics must look positive.</div>
                        </div>
                        <button type="submit" class="btn-premium" id="submitFormBtn">Commit Configuration</button>
                    </form>
                </div>

                <div class="executive-panel-card">
                    <div class="luxury-frame"></div>
                    <h2 class="panel-title">Analytical Attendance Track</h2>
                    <div class="executive-chart-container">
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 40%;"></div>
                            <div class="chart-tooltip">W1: 40 Metrics</div>
                            <div class="chart-label">W1</div>
                        </div>
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 75%;"></div>
                            <div class="chart-tooltip">W2: 75 Metrics</div>
                            <div class="chart-label">W2</div>
                        </div>
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 60%;"></div>
                            <div class="chart-tooltip">W3: 60 Metrics</div>
                            <div class="chart-label">W3</div>
                        </div>
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar" style="height: 95%;"></div>
                            <div class="chart-tooltip">W4: 95 Metrics</div>
                            <div class="chart-label">W4</div>
                        </div>
                    </div>
                </div>

                <div class="executive-panel-card" style="grid-column: 1 / -1;">
                    <div class="luxury-frame"></div>
                    <h2 class="panel-title">Patron Gate Ledger & Registry Log</h2>
                    <div class="table-responsive-wrapper">
                        <table class="executive-table">
                            <thead>
                                <tr>
                                    <th>Patron Core Profile Identifier</th>
                                    <th>Registry Station Group</th>
                                    <th>Assigned Gate Manifest Time</th>
                                    <th>Status Verification</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Samuel Ng'ang'a</td>
                                    <td>System Administration Lead</td>
                                    <td>14:02:11 EAT</td>
                                    <td style="color: var(--success-color); font-weight:600;">Verified Access</td>
                                </tr>
                                <tr>
                                    <td>David Mwangi</td>
                                    <td>Project Supervision Directive</td>
                                    <td>14:15:43 EAT</td>
                                    <td style="color: var(--success-color); font-weight:600;">Verified Access</td>
                                </tr>
                                <tr>
                                    <td>High-Value Parish Donor</td>
                                    <td>External Auxiliary Body</td>
                                    <td>15:00:22 EAT</td>
                                    <td style="color: var(--text-muted);">Awaiting Gate Pass</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="executive-panel-card" style="grid-column: 1 / -1;">
                    <div class="luxury-frame"></div>
                    <h2 class="panel-title">Palace Operations Framework Calendar</h2>
                    <div class="executive-calendar-widget">
                        <div class="calendar-day-label">Sun</div><div class="calendar-day-label">Mon</div>
                        <div class="calendar-day-label">Tue</div><div class="calendar-day-label">Wed</div>
                        <div class="calendar-day-label">Thu</div><div class="calendar-day-label">Fri</div>
                        <div class="calendar-day-label">Sat</div>
                        
                        <div class="calendar-cell">1</div><div class="calendar-cell">2</div>
                        <div class="calendar-cell has-event">3</div><div class="calendar-cell">4</div>
                        <div class="calendar-cell">5</div><div class="calendar-cell has-event">6</div>
                        <div class="calendar-cell">7</div><div class="calendar-cell">8</div>
                        <div class="calendar-cell">9</div><div class="calendar-cell">10</div>
                        <div class="calendar-cell has-event">11</div><div class="calendar-cell">12</div>
                        <div class="calendar-cell">13</div><div class="calendar-cell">14</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modals Overlay System Module -->
    <div class="executive-modal-overlay" id="eventCreationModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="executive-modal">
            <button class="modal-close-btn" id="closeModalBtn" aria-label="Dismiss Dialog Box">✕</button>
            <h2 class="panel-title" id="modalTitle">Initialize Premium Event Window</h2>
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">
                You are about to instantiate a secure tracking sequence on the server core system.
            </p>
            <button class="btn-premium" onclick="toggleModal(false)">Acknowledge Directive</button>
        </div>
    </div>

    <script>
        // SCRIPTURAL EXEGESIS PARSER & CROSS-FADE ROTATOR
        const liturgicalRepository = [
            { citation: "Proverbs 16:3", body: '"Commit your actions to the Eternal, and your plans will succeed."' },
            { citation: "Psalm 119:105", body: '"Your word is a lamp for my feet, a light on my path."' },
            { citation: "Philippians 4:13", body: '"I can do all things through Him who strengthens me."' },
            { citation: "Isaiah 40:31", body: '"But those who hope in the Lord will renew their strength. They will soar on wings like eagles."' },
            { citation: "Hebrews 11:1", body: '"Now faith is confidence in what we hope for and assurance about what we do not see."' }
        ];

        let indexLiturgical = 0;
        const citationEl = document.getElementById('scriptureCitation');
        const bodyEl = document.getElementById('scriptureBody');

        function alternateScriptureText() {
            bodyEl.classList.add('fade-transition');
            citationEl.classList.add('fade-transition');
            
            setTimeout(() => {
                indexLiturgical = (indexLiturgical + 1) % liturgicalRepository.length;
                const dynamicVerse = liturgicalRepository[indexLiturgical];
                
                citationEl.textContent = dynamicVerse.citation;
                bodyEl.textContent = dynamicVerse.body;
                
                bodyEl.classList.remove('fade-transition');
                citationEl.classList.remove('fade-transition');
            }, 400); // Transitions synchronously inside CSS layout variables
        }
        setInterval(alternateScriptureText, 9000); // Timed cycle rotation window

        // CINEMATIC GOLDEN DOVE ANIMATION CONTROLLER
        const animationCanvas = document.getElementById('doveAnimationCanvas');
        const animCtx = animationCanvas.getContext('2d');
        let elegantDovesArray = [];

        function setCanvasContextBounds() {
            animationCanvas.width = window.innerWidth;
            animationCanvas.height = window.innerHeight;
        }
        window.addEventListener('resize', setCanvasContextBounds);
        setCanvasContextBounds();

        class GoldenDoveParticle {
            constructor() { this.resetDoveState(true); }

            resetDoveState(isInitialSpawn = false) {
                this.x = isInitialSpawn ? Math.random() * animationCanvas.width : -60;
                this.y = Math.random() * (animationCanvas.height * 0.7); 
                this.speedX = 0.8 + Math.random() * 1.4;
                this.speedY = (Math.random() - 0.5) * 0.4;
                this.scale = 0.3 + Math.random() * 0.5;
                this.alpha = 0.05 + Math.random() * 0.35;
                this.wingPhase = Math.random() * Math.PI * 2;
                this.wingSpeed = 0.06 + Math.random() * 0.06;
            }

            renderAndProgress(goldColorHex) {
                this.x += this.speedX;
                this.y += this.speedY;
                this.wingPhase += this.wingSpeed;

                if (this.x > animationCanvas.width + 100) { this.resetDoveState(false); }

                animCtx.save();
                animCtx.translate(this.x, this.y);
                animCtx.scale(this.scale, this.scale);
                animCtx.fillStyle = goldColorHex;
                animCtx.globalAlpha = this.alpha;

                const wingFlapOffset = Math.sin(this.wingPhase) * 22;

                animCtx.beginPath();
                animCtx.moveTo(-35, 5);
                animCtx.lineTo(-45, 12);
                animCtx.lineTo(-42, -2);
                animCtx.lineTo(-48, -10);
                animCtx.lineTo(-32, -3);
                animCtx.quadraticCurveTo(-15, -18, -2, -8);
                animCtx.bezierCurveTo(8, -28 + wingFlapOffset, 22, -45 + wingFlapOffset, 12, -8);
                animCtx.quadraticCurveTo(28, -6, 38, -2);
                animCtx.quadraticCurveTo(26, 4, 18, 6);
                animCtx.bezierCurveTo(25, 26 - wingFlapOffset, 10, 42 - wingFlapOffset, -2, 8);
                animCtx.quadraticCurveTo(-18, 12, -35, 5);
                animCtx.closePath();
                animCtx.fill();
                animCtx.restore();
            }
        }

        for (let i = 0; i < 8; i++) { elegantDovesArray.push(new GoldenDoveParticle()); }

        function executeCinematicLoop() {
            animCtx.clearRect(0, 0, animationCanvas.width, animationCanvas.height);
            const computationalGoldColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-gold').trim();
            elegantDovesArray.forEach(dove => { dove.renderAndProgress(computationalGoldColor); });
            requestAnimationFrame(executeCinematicLoop);
        }
        executeCinematicLoop();

        // THEME ENGINE CONTROLLER
        const premiumToggle = document.getElementById('premiumThemeEngine');
        const coreHTML = document.documentElement;

        premiumToggle.addEventListener('click', () => {
            const activeTheme = coreHTML.getAttribute('data-theme');
            const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
            coreHTML.setAttribute('data-theme', newTheme);
            recalculateVectorCanvases();
        });

        // COLLAPSIBLE NAVIGATION SIDEBAR
        const collapseSidebarBtn = document.getElementById('collapseSidebarBtn');
        const mainSidebar = document.getElementById('mainSidebar');

        collapseSidebarBtn.addEventListener('click', () => {
            mainSidebar.classList.toggle('collapsed');
            setTimeout(recalculateVectorCanvases, 450); 
        });

        // MODAL INTERACTION OVERLAYS
        const openModalExampleBtn = document.getElementById('openModalExampleBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const eventCreationModal = document.getElementById('eventCreationModal');

        function toggleModal(open) {
            if (open) eventCreationModal.classList.add('active');
            else eventCreationModal.classList.remove('active');
        }

        openModalExampleBtn.addEventListener('click', () => toggleModal(true));
        closeModalBtn.addEventListener('click', () => toggleModal(false));

        // FORM REALTIME VALIDATION ACTIONS
        function validateExecutiveForm(event) {
            event.preventDefault();
            let isFormValid = true;

            const prodTitle = document.getElementById('prodTitle');
            const prodAllocation = document.getElementById('prodAllocation');
            const prodTitleError = document.getElementById('prodTitleError');
            const prodAllocationError = document.getElementById('prodAllocationError');

            if (prodTitle.value.trim() === "") {
                prodTitle.classList.add('invalid');
                prodTitleError.style.display = "block";
                isFormValid = false;
            } else {
                prodTitle.classList.remove('invalid');
                prodTitleError.style.display = "none";
            }

            if (Number(prodAllocation.value) <= 0) {
                prodAllocation.classList.add('invalid');
                prodAllocationError.style.display = "block";
                isFormValid = false;
            } else {
                prodAllocation.classList.remove('invalid');
                prodAllocationError.style.display = "none";
            }

            if (isFormValid) {
                alert("🔒 Configuration verified. Transferring tokenized parameters to core architecture.");
            }
            return isFormValid;
        }

        // GEOMETRIC VECTOR LINES RENDERING
        function recalculateVectorCanvases() {
            const canvases = document.querySelectorAll('.executive-card-canvas');
            canvases.forEach(canvas => {
                const ctx = canvas.getContext('2d');
                canvas.width = canvas.parentElement.offsetWidth;
                canvas.height = canvas.parentElement.offsetHeight;

                ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue('--accent-gold').trim();
                ctx.lineWidth = 0.25;
                ctx.beginPath();
                
                for (let i = 0; i < canvas.width; i += 40) {
                    ctx.moveTo(i, 0);
                    ctx.lineTo(i + 30, canvas.height);
                }
                ctx.stroke();
            });
        }

        window.addEventListener('load', recalculateVectorCanvases);
        window.addEventListener('resize', recalculateVectorCanvases);
    </script>
</body>
</html>