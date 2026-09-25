<?php
include("../config.php");
session_start();

if($_SESSION['user']['role'] != 'admin'){
    die("Access Denied");
}

// Ensure the user is authenticated as an admin
if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Sacred Heart</title>
    
    <!-- Premium Google Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #090d16 0%, #141130 50%, #220b2e 100%);
            --card-bg: rgba(255, 255, 255, 0.06);
            --card-border: rgba(255, 255, 255, 0.08);
            --glass-blur: blur(20px);
            
            --primary-gold: #dfb76c;
            --light-gold: #f4e3b1;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            
            --action-edit: #10b981;
            --action-delete: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-main);
            padding: 40px;
            overflow-x: hidden;
        }

        /* Subtle Geometric Background Pattern */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(223, 183, 108, 0.03) 1px, transparent 0);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* BACK TO DASHBOARD LINK */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-gold);
        }

        /* HEADER WINDOW */
        .header-panel {
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            padding: 30px 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            border: 1px solid var(--card-border);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-panel h1 {
            font-family: 'Cinzel', serif;
            font-size: 28px;
            color: var(--text-main);
            letter-spacing: 0.5px;
        }

        .header-panel h1 span {
            color: var(--primary-gold);
        }

        .btn-create {
            background: linear-gradient(135deg, rgba(223, 183, 108, 0.2) 0%, rgba(223, 183, 108, 0.05) 100%);
            border: 1px solid var(--primary-gold);
            color: var(--light-gold);
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-create:hover {
            background: var(--primary-gold);
            color: #090d16;
            box-shadow: 0 0 15px rgba(223, 183, 108, 0.3);
            transform: translateY(-2px);
        }

        /* PREMIUM GLASS TABLE CONTAINER */
        .table-responsive {
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: rgba(0, 0, 0, 0.2);
            color: var(--primary-gold);
            font-family: 'Cinzel', serif;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 20px;
            border-bottom: 1px solid var(--card-border);
            font-weight: 600;
        }

        td {
            padding: 20px;
            font-size: 15px;
            color: var(--text-main);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            transition: background-color 0.3s ease;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .event-title {
            font-weight: 500;
            color: var(--light-gold);
        }

        .event-date {
            color: var(--text-muted);
            font-size: 14px;
        }

        /* ACTIONS ACTION CONTAINER */
        .actions-cell {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 8px 16px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn.edit {
            background: rgba(16, 185, 129, 0.1);
            color: var(--action-edit);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .action-btn.edit:hover {
            background: var(--action-edit);
            color: #fff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .action-btn.delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--action-delete);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .action-btn.delete:hover {
            background: var(--action-delete);
            color: #fff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        /* EMPTY STATE SYSTEM */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Back to Main Controls -->
    <a href="dashboard.php" class="back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back to Dashboard
    </a>

    <!-- HEADER PANEL -->
    <div class="header-panel">
        <h1>Liturgical & Parish <span>Events</span></h1>
        <a href="create_event.php" class="btn-create">+ Schedule New Event</a>
    </div>

    <!-- TABLE AREA -->
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Event Title</th>
                    <th style="width: 25%;">Scheduled Date</th>
                    <th style="width: 25%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)){ 
                ?>
                <tr>
                    <td class="event-title">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </td>
                    <td class="event-date">
                        <?php echo date('F d, Y', strtotime($row['event_date'])); ?>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a class="action-btn edit" href="edit_event.php?id=<?php echo $row['id']; ?>">
                                Edit
                            </a>
                            <a class="action-btn delete" href="delete_event.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this liturgical event?');">
                                Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    } 
                } else { 
                ?>
                <tr>
                    <td colspan="3" class="empty-state">No upcoming parish events currently scheduled.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>