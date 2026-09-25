<?php
include("../config.php");
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

/* SECURED REVERENT REGISTRY FETCH */
$stmt = $conn->prepare("
    SELECT events.*, registrations.id AS reg_id, registrations.status
    FROM registrations
    JOIN events ON registrations.event_id = events.id
    WHERE registrations.user_id = ?
    ORDER BY events.event_date ASC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$query_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Parish Ledger | Sacred Heart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin">

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>

        :root{
            --cathedral-blue:#0A192F;
            --liturgical-gold:#D4AF37;
            --soft-gold:#F4E8C1;
            --holy-white:#FCFBF9;
            --marble-gray:#F0F2F5;
            --text-dark:#2C3E50;
            --muted-slate:#616F7C;
            --ecclesia-charcoal:#5C6266;

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
            min-height:100vh;
            padding:60px 20px;
            position:relative;
            overflow-x:hidden;
        }

        /* ===================================
           AMBIENT BACKGROUND
        =================================== */

        .ambient-background{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:
            radial-gradient(circle at 50% 15%,
            #E6EEF8 0%,
            var(--marble-gray) 70%);
            z-index:-1;
        }

        .ember{
            position:absolute;
            border-radius:50%;
            background:
            radial-gradient(circle,
            rgba(212,175,55,0.15) 0%,
            rgba(214,175,55,0) 70%);
            animation:floatUpDown 22s infinite ease-in-out;
        }

        .ember-1{
            width:300px;
            height:300px;
            top:-50px;
            left:10%;
        }

        .ember-2{
            width:400px;
            height:400px;
            bottom:-100px;
            right:5%;
            animation-delay:-5s;
            animation-duration:28s;
        }

        @keyframes floatUpDown{

            0%,100%{
                transform:translateY(0) scale(1);
                opacity:0.4;
            }

            50%{
                transform:translateY(-60px) scale(1.1);
                opacity:0.7;
            }
        }

        /* ===================================
           MAIN CONTAINER
        =================================== */

        .container{
            max-width:1140px;
            margin:0 auto;
            position:relative;
            z-index:2;
        }

        /* ===================================
           PAGE TITLE
        =================================== */

        .title-section{
            text-align:center;
            margin-bottom:50px;
        }

        .title-section h1{
            font-family:var(--font-serif);
            color:var(--cathedral-blue);
            font-size:36px;
            letter-spacing:1px;
            margin-bottom:8px;
        }

        .title-section h1::after{
            content:"✦";
            display:block;
            font-size:14px;
            color:var(--liturgical-gold);
            margin-top:5px;
        }

        .title-section p{
            font-size:15px;
            color:var(--muted-slate);
        }

        /* ===================================
           GRID
        =================================== */

        .events-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(340px,1fr));
            gap:30px;
        }

        /* ===================================
           CARD
        =================================== */

        .card{
            background:rgba(252,251,249,0.88);

            backdrop-filter:blur(10px);
            -webkit-backdrop-filter:blur(10px);

            border:1px solid rgba(214,175,55,0.18);

            border-radius:12px;

            padding:30px;

            display:flex;
            flex-direction:column;
            justify-content:space-between;

            box-shadow:0 10px 25px rgba(10,25,47,0.05);

            transition:0.3s ease;
        }

        .card:hover{
            transform:translateY(-4px);

            box-shadow:
            0 15px 35px rgba(212,175,55,0.12);

            border-color:rgba(214,175,55,0.35);
        }

        .card h2{
            font-family:var(--font-serif);
            color:var(--cathedral-blue);
            font-size:20px;
            margin-bottom:12px;
            line-height:1.4;
        }

        .description{
            font-size:14px;
            color:var(--text-dark);
            line-height:1.6;
            margin-bottom:20px;
            opacity:0.85;
        }

        /* ===================================
           META INFORMATION
        =================================== */

        .meta-list{
            border-top:1px dashed rgba(10,25,47,0.1);
            padding-top:18px;
            margin-bottom:25px;
        }

        .meta-item{
            display:flex;
            align-items:center;
            font-size:13.5px;
            color:var(--text-dark);
            margin-bottom:12px;
        }

        .meta-item svg{
            width:16px;
            height:16px;
            margin-right:10px;
            fill:var(--liturgical-gold);
            flex-shrink:0;
        }

        .meta-item strong{
            margin-right:5px;
            color:var(--cathedral-blue);
        }

        /* ===================================
           STATUS BADGES
        =================================== */

        .status-pending{
            color:orange;
            font-weight:600;
        }

        .status-approved{
            color:green;
            font-weight:600;
        }

        .status-rejected{
            color:red;
            font-weight:600;
        }

        /* ===================================
           CANCEL BUTTON
        =================================== */

        .cancel-btn{
            display:block;
            text-align:center;

            background:transparent;

            color:var(--ecclesia-charcoal);

            border:1px solid rgba(92,98,102,0.3);

            padding:12px;

            border-radius:6px;

            text-decoration:none;

            font-family:var(--font-serif);

            font-size:12px;

            text-transform:uppercase;

            letter-spacing:1.5px;

            font-weight:bold;

            transition:0.25s ease;
        }

        .cancel-btn:hover{
            background:var(--ecclesia-charcoal);
            color:white;
            border-color:var(--ecclesia-charcoal);
        }

        /* ===================================
           EMPTY STATE
        =================================== */

        .empty-ledger{
            grid-column:1/-1;

            background:rgba(252,251,249,0.8);

            border:1px dashed rgba(214,175,55,0.3);

            padding:60px 40px;

            text-align:center;

            border-radius:12px;
        }

        .empty-ledger h3{
            font-family:var(--font-serif);
            color:var(--cathedral-blue);
            font-size:22px;
            margin-bottom:10px;
        }

        .empty-ledger p{
            color:var(--muted-slate);
            font-size:14px;
        }

        /* ===================================
           FOOTER
        =================================== */

        .footer-nav{
            text-align:center;
            margin-top:50px;
        }

        .footer-nav a{
            font-family:var(--font-serif);
            color:var(--cathedral-blue);
            text-decoration:none;
            font-size:13px;
            letter-spacing:1px;
            font-weight:bold;
            text-transform:uppercase;
            transition:0.2s ease;
        }

        .footer-nav a:hover{
            color:var(--liturgical-gold);
        }

    </style>
</head>
<body>

<!-- Animated Background -->
<div class="ambient-background">
    <div class="ember ember-1"></div>
    <div class="ember ember-2"></div>
</div>

<div class="container">

    <!-- Header -->
    <div class="title-section">
        <h1>My Registered Events</h1>
        <p>Your current active reservations in the parish registry</p>
    </div>

    <!-- Cards Grid -->
    <div class="events-grid">

        <?php if($query_result->num_rows > 0): ?>

            <?php while($row = $query_result->fetch_assoc()): ?>

                <div class="card">

                    <div>

                        <h2>
                            <?php echo htmlspecialchars($row['title']); ?>
                        </h2>

                        <p class="description">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </p>

                    </div>

                    <div>

                        <div class="meta-list">

                            <!-- DATE -->
                            <div class="meta-item">

                                <svg viewBox="0 0 24 24">
                                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .89-2 2v14c0 1.11.89 2 2 2h14c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2z"/>
                                </svg>

                                <strong>Date:</strong>

                                <?php echo date("F d, Y", strtotime($row['event_date'])); ?>

                            </div>

                            <!-- TIME -->
                            <div class="meta-item">

                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                </svg>

                                <strong>Time:</strong>

                                <?php echo date("g:i A", strtotime($row['event_time'])); ?>

                            </div>

                            <!-- VENUE -->
                            <div class="meta-item">

                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                </svg>

                                <strong>Venue:</strong>

                                <?php echo htmlspecialchars($row['location']); ?>

                            </div>

                            <!-- STATUS -->
                            <div class="meta-item">

                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                                </svg>

                                <strong>Status:</strong>

                                <?php
                                if($row['status'] == "Pending"){
                                    echo "<span class='status-pending'>Pending Approval</span>";
                                }
                                elseif($row['status'] == "Approved"){
                                    echo "<span class='status-approved'>Approved</span>";
                                }
                                else{
                                    echo "<span class='status-rejected'>Rejected</span>";
                                }
                                ?>

                            </div>

                        </div>

                        <!-- CANCEL BUTTON -->
                        <a href="cancel_registration.php?id=<?php echo $row['reg_id']; ?>"
                           class="cancel-btn"
                           onclick="return confirm('Are you sure you want to withdraw from this event?')">

                            Withdraw Place

                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <!-- EMPTY STATE -->
            <div class="empty-ledger">

                <h3>No Active Registrations</h3>

                <p>
                    Your calendar is empty. Explore parish events and register for upcoming gatherings.
                </p>

            </div>

        <?php endif; ?>

    </div>

    <!-- Footer -->
    <div class="footer-nav">

        <a href="dashboard.php">
            ← Return to Dashboard
        </a>

    </div>

</div>

</body>
</html>