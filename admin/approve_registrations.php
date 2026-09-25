<?php
include("../config.php");
session_start(); // <-- Typo fixed here with a proper semicolon

/* CHECK IF USER IS LOGGED IN */
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}
/* FETCH ALL EVENT REGISTRATIONS */
$query = mysqli_query($conn, "
    SELECT registrations.*,
    users.name AS user_name,
    events.title AS event_title,
    events.event_date
    FROM registrations
    JOIN users ON registrations.user_id = users.id
    JOIN events ON registrations.event_id = events.id
    ORDER BY registrations.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parish Registry Approvals | Sacred Heart</title>
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
            --muted-slate: #616F7C;
            
            /* Clean structural badge states */
            --state-pending: #D97706;  /* Deep amber */
            --state-approved: #15803D; /* Liturgical green */
            --state-rejected: #991B1B; /* Ecclesiastical crimson */
            
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
            min-height: 100vh;
            padding: 60px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* --- AMBIENT LITURGICAL BACKGROUND ANIMATION --- */
        .ambient-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 15%, #E6EEF8 0%, var(--marble-gray) 70%);
            z-index: -1;
        }

        .ember {
            position: absolute;
            background: radial-gradient(circle, rgba(212,175,55,0.12) 0%, rgba(214,175,55,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: floatUpDown 25s infinite ease-in-out;
        }

        .ember-1 { width: 350px; height: 350px; top: -80px; right: 10%; }
        .ember-2 { width: 250px; height: 250px; bottom: 5%; left: 5%; animation-delay: -4s; }

        @keyframes floatUpDown {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.4; }
            50% { transform: translateY(-40px) scale(1.1); opacity: 0.6; }
        }

        .container {
            max-width: 1140px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        /* --- LEDGER HEADER SECTOR --- */
        .title-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .title-section h1 {
            font-family: var(--font-serif);
            color: var(--cathedral-blue);
            font-size: 34px;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .title-section h1::after {
            content: "✦";
            display: block;
            font-size: 13px;
            color: var(--liturgical-gold);
            margin: 4px auto 0 auto;
        }

        .title-section p {
            font-size: 15px;
            color: var(--muted-slate);
            font-weight: 500;
        }

        /* --- LITURGICAL ALERT BOXES --- */
        .alert-banner {
            max-width: 100%;
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 25px;
            line-height: 1.4;
            display: flex;
            align-items: center;
        }

        .alert-banner.success {
            background-color: rgba(21, 128, 61, 0.1);
            color: var(--state-approved);
            border: 1px solid rgba(21, 128, 61, 0.25);
        }

        .alert-banner.warning {
            background-color: rgba(217, 119, 6, 0.1);
            color: var(--state-pending);
            border: 1px solid rgba(217, 119, 6, 0.25);
        }

        .alert-banner.error {
            background-color: rgba(153, 27, 27, 0.08);
            color: var(--state-rejected);
            border: 1px solid rgba(153, 27, 27, 0.2);
        }

        /* --- PARCHMENT LEDGER SHEET CONTAINER --- */
        .table-container {
            background: rgba(252, 251, 249, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(214, 175, 55, 0.2);
            border-radius: 14px;
            padding: 10px;
            box-shadow: 0 15px 35px rgba(10, 25, 47, 0.06);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        /* --- TABLE TYPOGRAPHY AND STRUCTURE --- */
        table th {
            font-family: var(--font-serif);
            background: var(--cathedral-blue);
            color: var(--soft-gold);
            padding: 18px 24px;
            font-size: 12px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 700;
            border-bottom: 2px solid var(--liturgical-gold);
        }

        table td {
            padding: 18px 24px;
            color: var(--text-dark);
            border-bottom: 1px solid rgba(10, 25, 47, 0.05);
            vertical-align: middle;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover td {
            background: rgba(212, 175, 55, 0.03);
        }

        .member-name {
            font-weight: 600;
            color: var(--cathedral-blue);
        }

        /* --- LITURGICAL STATUS TAGS --- */
        .status {
            display: inline-block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 4px;
        }

        .status.pending {
            background: rgba(217, 119, 6, 0.12);
            color: var(--state-pending);
            border: 1px solid rgba(217, 119, 6, 0.2);
        }

        .status.approved {
            background: rgba(21, 128, 61, 0.1);
            color: var(--state-approved);
            border: 1px solid rgba(21, 128, 61, 0.2);
        }

        .status.rejected {
            background: rgba(153, 27, 27, 0.08);
            color: var(--state-rejected);
            border: 1px solid rgba(153, 27, 27, 0.18);
        }

        /* --- MANAGEMENT CONTROL TRIGGERS --- */
        .actions-cell {
            white-space: nowrap;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-right: 6px;
        }

        .approve-btn {
            background: transparent;
            color: var(--state-approved);
            border: 1px solid rgba(21, 128, 61, 0.4);
        }

        .approve-btn:hover {
            background: var(--state-approved);
            color: white;
            border-color: var(--state-approved);
            box-shadow: 0 4px 10px rgba(21, 128, 61, 0.25);
        }

        .reject-btn {
            background: transparent;
            color: var(--state-rejected);
            border: 1px solid rgba(153, 27, 27, 0.3);
        }

        .reject-btn:hover {
            background: var(--state-rejected);
            color: white;
            border-color: var(--state-rejected);
            box-shadow: 0 4px 10px rgba(153, 27, 27, 0.25);
        }

        /* --- FOOTER PANELS --- */
        .footer-nav {
            text-align: center;
            margin-top: 40px;
        }

        .footer-nav a {
            font-family: var(--font-serif);
            color: var(--cathedral-blue);
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 1px;
            font-weight: bold;
            text-transform: uppercase;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .footer-nav a:hover {
            color: var(--liturgical-gold);
        }

        /* --- RESPONSIVE CARD DISPLAY CONVERSION --- */
        @media(max-width: 768px) {
            body {
                padding: 30px 12px;
            }
            
            .table-container {
                background: transparent;
                border: none;
                box-shadow: none;
                padding: 0;
            }

            table, tr, td, th { 
                display: block; 
            }
            
            table th { 
                display: none; 
            }

            table tr {
                background: rgba(252, 251, 249, 0.95);
                border: 1px solid rgba(214, 175, 55, 0.2);
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 16px;
                box-shadow: 0 4px 15px rgba(10, 25, 47, 0.03);
            }

            table td {
                padding: 10px 0;
                border-bottom: 1px dashed rgba(10, 25, 47, 0.05);
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
            }

            table td::before {
                content: attr(data-label);
                font-family: var(--font-serif);
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: var(--muted-slate);
                font-weight: bold;
                text-align: left;
            }

            table td:last-child {
                border-bottom: none;
                padding-top: 15px;
                justify-content: flex-end;
            }
            
            .action-btn {
                margin-right: 0;
                margin-left: 8px;
            }
        }
    </style>
</head>
<body>

<div class="ambient-background">
    <div class="ember ember-1"></div>
    <div class="ember ember-2"></div>
</div>

<div class="container">

    <div class="title-section">
        <h1>Parish Registry Approvals</h1>
        <p>Review and manage all incoming event registrations</p>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert-banner success">
            ✦ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['warning'])): ?>
        <div class="alert-banner warning">
            ✦ <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert-banner error">
            ✕ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query)){ ?>
                <tr>
                    <td data-label="Member">
                        <span class="member-name"><?php echo htmlspecialchars($row['user_name']); ?></span>
                    </td>
                    <td data-label="Event">
                        <?php echo htmlspecialchars($row['event_title']); ?>
                    </td>
                    <td data-label="Date">
                        <?php echo date("F d, Y", strtotime($row['event_date'])); ?>
                    </td>
                    <td data-label="Status">
                        <?php
                        $status = $row['status'];
                        if($status == "Pending"){
                            echo "<span class='status pending'>Pending</span>";
                        } elseif($status == "Approved") {
                            echo "<span class='status approved'>Approved</span>";
                        } else {
                            echo "<span class='status rejected'>Rejected</span>";
                        }
                        ?>
                    </td>
                    <td data-label="Action" class="actions-cell">
                        <a href="update_registration.php?id=<?php echo $row['id']; ?>&status=Approved" class="action-btn approve-btn">
                            Approve
                        </a>
                        <a href="update_registration.php?id=<?php echo $row['id']; ?>&status=Rejected" class="action-btn reject-btn">
                            Reject
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="footer-nav">
        <a href="dashboard.php">
            <svg style="width:14px; height:14px; margin-right:6px; fill:currentColor; transform:scaleX(-1);" viewBox="0 0 24 24"><path d="M5 13h11.86l-5.43 5.43 1.42 1.42L21.14 12l-8.29-8.29-1.42 1.42 5.43 5.43H5v2z"/></svg>
            Return to Dashboard
        </a>
    </div>

</div>

</body>
</html>

```