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

$feedback = "";

/* PROCESS ATTENDANCE */
if(isset($_POST['mark'])){

    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);

    // Prevent duplicate attendance records
    $check_dup = mysqli_query(
        $conn,
        "SELECT * FROM attendance
         WHERE user_id='$user_id'
         AND event_id='$event_id'"
    );

    if(mysqli_num_rows($check_dup) > 0){

        $feedback = "Attendance was already marked for this parishioner.";

    } else {

        $sql = "INSERT INTO attendance(user_id,event_id,attendance_status)
                VALUES('$user_id','$event_id','Present')";

        if(mysqli_query($conn, $sql)){
            $feedback = "Parishioner status updated to Present successfully!";
        } else {
            $feedback = "Failed to record attendance.";
        }
    }
}

/* FETCH REGISTRATIONS */
$registrations = mysqli_query($conn, "
    SELECT registrations.*, users.name, events.title
    FROM registrations
    JOIN users ON registrations.user_id = users.id
    JOIN events ON registrations.event_id = events.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Control Matrix</title>
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
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .workspace-card {
            background: var(--pure-white);
            width: 100%;
            max-width: 1000px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(58, 27, 64, 0.05);
            border-top: 5px solid var(--papal-purple);
        }

        /* Module Title Headers */
        .workspace-header {
            margin-bottom: 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .workspace-header h1 {
            font-size: 28px;
            color: var(--papal-purple);
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .btn-back {
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            color: var(--muted-gray);
            padding: 10px 18px;
            border: 1px solid #E2DFE3;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: #FAFAF9;
            color: var(--papal-purple);
            border-color: var(--cathedral-gold);
        }

        /* Interactive Notification Alert banner */
        .alert-banner {
            background: rgba(58, 27, 64, 0.05);
            color: var(--papal-purple);
            padding: 12px 16px;
            border-left: 4px solid var(--cathedral-gold);
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 25px;
        }

        /* --- PREMIUM TABLE STYLING --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #E8E6E9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 15px;
        }

        th {
            background-color: #FAFAF9;
            color: var(--papal-purple);
            font-weight: 600;
            padding: 16px 20px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.8px;
            border-bottom: 2px solid #E8E6E9;
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid #ECE9EC;
            color: var(--deep-onyx);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #FAFAF9;
        }

        /* Row Specific Labeling */
        .event-title-cell {
            font-weight: 500;
            color: #5A535C;
        }

        /* Action Marking Buttons */
        .btn-mark {
            background: var(--papal-purple);
            color: var(--pure-white);
            border: 1px solid var(--papal-purple);
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-mark:hover {
            background: var(--cathedral-gold);
            border-color: var(--cathedral-gold);
            color: var(--papal-purple);
            box-shadow: 0 4px 12px rgba(214, 175, 55, 0.2);
        }

        /* Empty Database Feedback Component */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--muted-gray);
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .workspace-card { padding: 20px; }
            .workspace-header { flex-direction: column; align-items: flex-start; }
            .btn-back { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

<div class="workspace-card">

    <div class="workspace-header">
        <h1>Attendance Tracking</h1>
        <a href="dashboard.php" class="btn-back">← Back to Console</a>
    </div>

    <!-- Alert Panel Notifications injection point -->
    <?php if(!empty($feedback)): ?>
        <div class="alert-banner">
            🔔 <?php echo $feedback; ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Parishioner Name</th>
                    <th>Target Event Title</th>
                    <th style="text-align: right;">Action Control</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($registrations) > 0) {
                    while($row = mysqli_fetch_assoc($registrations)){ 
                ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td class="event-title-cell"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td style="text-align: right;">
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                <input type="hidden" name="event_id" value="<?php echo $row['event_id']; ?>">
                                <button type="submit" name="mark" class="btn-mark">Mark Present</button>
                            </form>
                        </td>
                    </tr>
                <?php 
                    } 
                } else { 
                ?>
                    <tr>
                        <td colspan="3" class="empty-state">
                            No event registrations have been filed in the directory yet.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>