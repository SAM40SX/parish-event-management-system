<?php
include("../config.php");
session_start();

// Fetch upcoming parish events
$result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Parish Events</title>

    <style>
        :root {
            --liturgical-red: #800020;
            --sacred-gold: #D4AF37;
            --soft-white: #FCFBFA;
            --pure-white: #FFFFFF;
            --text-dark: #2B2B2B;
            --text-muted: #666666;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI', Arial, sans-serif;
        }

        body{
            background-color:var(--soft-white);
            color:var(--text-dark);
            padding:40px 20px;
            min-height:100vh;
        }

        .container{
            max-width:1200px;
            margin:0 auto;
        }

        /* PAGE HEADER */

        .page-header{
            text-align:center;
            margin-bottom:45px;
        }

        .page-header h1{
            font-size:36px;
            color:var(--liturgical-red);
            margin-bottom:8px;
        }

        .page-header p{
            color:var(--text-muted);
            font-size:16px;
        }

        /* EVENTS GRID */

        .events-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(340px,1fr));
            gap:30px;
            margin-bottom:40px;
        }

        /* EVENT CARD */

        .event-card{
            background:var(--pure-white);
            border-radius:12px;
            padding:30px;
            box-shadow:0 8px 24px rgba(0,0,0,0.03);
            border:1px solid rgba(0,0,0,0.04);
            transition:0.3s ease;
        }

        .event-card:hover{
            transform:translateY(-4px);
            box-shadow:0 12px 30px rgba(128,0,32,0.06);
            border-color:rgba(214,175,55,0.25);
        }

        .event-card h2{
            font-size:22px;
            color:var(--liturgical-red);
            margin-bottom:12px;
        }

        .event-description{
            color:var(--text-muted);
            font-size:14px;
            line-height:1.6;
            margin-bottom:20px;
        }

        /* EVENT DETAILS */

        .event-meta{
            border-top:1px solid #F0ECE7;
            padding-top:15px;
            margin-bottom:25px;
        }

        .meta-item{
            font-size:14px;
            margin-bottom:8px;
        }

        .meta-item strong{
            color:var(--liturgical-red);
        }

        /* REGISTER BUTTON */

        .join-btn{

            display:block;

            text-align:center;

            background: var(--liturgical-red);

            color: white;

            padding: 12px 20px;

            border-radius: 6px;

            text-decoration:none;

            font-size:15px;

            font-weight:600;

            transition:0.3s ease;

            border:1px solid var(--liturgical-red);
        }

        .join-btn:hover{

            background: var(--sacred-gold);

            color: var(--text-dark);

            border-color: var(--sacred-gold);

            transform:translateY(-2px);

            box-shadow:0 4px 15px rgba(214,175,55,0.2);
        }

        /* EMPTY STATE */

        .no-events{
            grid-column:1 / -1;
            background:var(--pure-white);
            text-align:center;
            padding:40px;
            border-radius:10px;
            color:var(--text-muted);
            border:1px dashed #DCD8D3;
        }

        /* BACK BUTTON */

        .back-home{
            text-align:center;
            margin-top:20px;
        }

        .back-home a{
            color:var(--text-muted);
            text-decoration:none;
            font-size:14px;
            font-weight:500;
        }

        .back-home a:hover{
            color:var(--liturgical-red);
            text-decoration:underline;
        }

        /* RESPONSIVE */

        @media(max-width:576px){

            body{
                padding:25px 15px;
            }

            .page-header h1{
                font-size:28px;
            }

            .event-card{
                padding:20px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- HEADER -->

    <div class="page-header">
        <h1>Upcoming Parish Events</h1>
        <p>Join and grow in faith with our community fellowship calendars.</p>
    </div>

    <!-- EVENTS -->

    <div class="events-grid">

        <?php
        if(mysqli_num_rows($result) > 0){

            while($row = mysqli_fetch_assoc($result)){
        ?>

        <div class="event-card">

            <h2>
                <?php echo htmlspecialchars($row['title']); ?>
            </h2>

            <p class="event-description">
                <?php echo htmlspecialchars($row['description']); ?>
            </p>

            <div class="event-meta">

                <div class="meta-item">
                    <strong>📅 Date:</strong>
                    <?php echo htmlspecialchars(date("F d, Y", strtotime($row['event_date']))); ?>
                </div>

                <div class="meta-item">
                    <strong>🕒 Time:</strong>
                    <?php echo htmlspecialchars(date("g:i A", strtotime($row['event_time']))); ?>
                </div>

                <div class="meta-item">
                    <strong>📍 Venue:</strong>
                    <?php echo htmlspecialchars($row['location']); ?>
                </div>

            </div>

            <!-- REGISTER BUTTON -->

            <a href="register_event.php?id=<?php echo $row['id']; ?>" class="join-btn">
                Register Event
            </a>

        </div>

        <?php
            }

        } else {
        ?>

        <div class="no-events">

            <p style="font-size:16px;">
                There are no scheduled church events listed at this moment.
            </p>

            <p style="font-size:13px; margin-top:5px;">
                Please check back shortly for upcoming community changes.
            </p>

        </div>

        <?php } ?>

    </div>

    <!-- BACK BUTTON -->

    <div class="back-home">
        <a href="dashboard.php">← Return to User Dashboard</a>
    </div>

</div>

</body>
</html>