<?php
include("../config.php");

/* COUNT TOTAL EVENTS */
$events = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events");
$total_events = mysqli_fetch_assoc($events)['total'];

/* COUNT TOTAL USERS */
$users = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$total_users = mysqli_fetch_assoc($users)['total'];

/* GET RECENT EVENTS */
$recent_events = mysqli_query($conn,"
    SELECT *
    FROM events
    ORDER BY event_date DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parish Event Management System Report</title>
<style>
    /* Google Fonts Import for clean, professional typography */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #002147;     /* Deep Navy Blue */
        --secondary-color: #d4af37;   /* Liturgical Gold */
        --text-dark: #2c3e50;
        --text-light: #64748b;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-light);
        color: var(--text-dark);
        line-height: 1.6;
        padding: 40px 20px;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        background: #ffffff;
        padding: 50px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
    }

    /* Action Buttons Area */
    .button-group {
        display: flex;
        gap: 15px;
        margin-bottom: 35px;
        flex-wrap: wrap;
    }

    .print-btn,
    .download-btn,
    .dashboard-btn {
        text-decoration: none;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .print-btn {
        background: var(--secondary-color);
        color: var(--primary-color);
    }

    .print-btn:hover {
        background: #c89f1f;
        transform: translateY(-1px);
    }

    .download-btn {
        background: var(--primary-color);
        color: #ffffff;
    }

    .download-btn:hover {
        background: #003a70;
        transform: translateY(-1px);
    }

    .dashboard-btn {
        background: #22c55e;
        color: #ffffff;
    }

    .dashboard-btn:hover {
        background: #16a34a;
        transform: translateY(-1px);
    }

    /* Document Header */
    .header {
        border-bottom: 3px solid var(--primary-color);
        padding-bottom: 20px;
        margin-bottom: 30px;
        text-align: center;
    }

    .header h1 {
        color: var(--primary-color);
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .header h3 {
        color: var(--secondary-color);
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* Academic Meta Info Block */
    .project-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        background: #f1f5f9;
        padding: 20px 25px;
        border-radius: 8px;
        margin-bottom: 40px;
        border-left: 5px solid var(--secondary-color);
    }

    .project-info p {
        font-size: 14px;
        color: var(--text-dark);
    }

    .project-info strong {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Section Headings */
    .section-title {
        color: var(--primary-color);
        font-size: 18px;
        font-weight: 700;
        margin: 35px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    p {
        font-size: 15px;
        color: #334155;
        margin-bottom: 15px;
        text-align: justify;
    }

    /* Objectives Bullet List */
    ul {
        list-style: none;
        margin-bottom: 25px;
    }

    ul li {
        font-size: 15px;
        color: #334155;
        position: relative;
        padding-left: 25px;
        margin-bottom: 10px;
    }

    ul li::before {
        content: "✓";
        position: absolute;
        left: 0;
        color: var(--secondary-color);
        font-weight: bold;
    }

    /* KPI Counter Grid */
    .stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin: 25px 0;
    }

    .card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-top: 4px solid var(--primary-color);
        padding: 20px;
        text-align: center;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .card h2 {
        font-size: 32px;
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 5px;
    }

    .card p {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        text-align: center;
    }

    /* Professional Data Tables */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0 35px 0;
        font-size: 14px;
    }

    th {
        background-color: var(--primary-color);
        color: #ffffff;
        text-align: left;
        padding: 12px 16px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        color: #334155;
    }

    tr:nth-child(even) {
        background-color: #f8fafc;
    }

    /* Blockquote / Scripture Block */
    .scripture {
        background: #fafaf9;
        border: 1px dashed #d6d3d1;
        border-left: 4px solid var(--secondary-color);
        padding: 20px;
        text-align: center;
        margin: 40px 0;
        border-radius: 4px;
    }

    .scripture em {
        font-size: 16px;
        color: var(--primary-color);
        display: block;
        margin-bottom: 8px;
        font-family: Georgia, serif;
    }

    .scripture strong {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-light);
    }

    /* Dynamic Sign-off Section */
    .signature-container {
        display: flex;
        justify-content: space-between;
        margin-top: 60px;
        padding-top: 20px;
    }

    .sig-box {
        width: 40%;
    }

    .sig-line {
        border-top: 1px solid #94a3b8;
        margin-bottom: 10px;
        padding-top: 5px;
    }

    .sig-box strong {
        color: var(--primary-color);
        font-size: 14px;
        display: block;
    }

    .sig-box span {
        color: var(--text-light);
        font-size: 12px;
    }

    /* Print View Media Optimization */
    @media print {
        .button-group {
            display: none;
        }

        body {
            background: white;
            padding: 0;
        }

        .container {
            box-shadow: none;
            padding: 0;
            border: none;
        }
        
        .card {
            border: 1px solid #cbd5e1 !important;
            border-top: 4px solid #002147 !important;
        }
    }
</style>
</head>
<body>

<div class="container">

    <!-- Action Bar (Hidden during Print) -->
    <div class="button-group">
        <button class="print-btn" onclick="window.print()">
            🖨 Print Report
        </button>

        <a href="download_report.php" class="download-btn">
            📥 Download PDF
        </a>

        <a href="dashboard.php" class="dashboard-btn">
            ⬅ Back to Dashboard
        </a>
    </div>

    <!-- Header Section -->
    <div class="header">
        <h1>Parish Event Management System</h1>
        <h3>Church System Development Report</h3>
    </div>

    <!-- Academic & System Context Meta -->
    <div class="project-info">
        <p><strong>Student Name:</strong> Samuel Ng'ang'a Mwihaki</p>
        <p><strong>Admission Number:</strong> 115950</p>
        <p><strong>Project Supervisor:</strong> David Mwangi</p>
        <p><strong>Project Title:</strong> Parish Event Management System</p>
        <p><strong>Date Generated:</strong> <?php echo date("F d, Y h:i A"); ?></p>
    </div>

    <h2 class="section-title">Project Introduction</h2>
    <p>
        The Parish Event Management System is a web-based church administration
        platform developed to assist church leaders in managing parish events,
        attendance, announcements, member registration, and communication.
        The system improves efficiency, record management, and event coordination
        within the church environment.
    </p>

    <h2 class="section-title">Project Objectives</h2>
    <ul>
        <li>Automate church event management.</li>
        <li>Digitize member registration.</li>
        <li>Improve communication through announcements.</li>
        <li>Track attendance records.</li>
        <li>Generate reports for church administration.</li>
    </ul>

    <h2 class="section-title">System Statistics</h2>
    <div class="stats">
        <div class="card">
            <h2><?php echo $total_events; ?></h2>
            <p>Total Events</p>
        </div>

        <div class="card">
            <h2><?php echo $total_users; ?></h2>
            <p>Registered Members</p>
        </div>

        <div class="card">
            <h2><?php echo date("Y"); ?></h2>
            <p>Academic Year</p>
        </div>
    </div>

    <h2 class="section-title">Recent Parish Events</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($recent_events)){ ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                <td><?php echo htmlspecialchars($row['event_time']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2 class="section-title">System Features</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Module</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>User Registration</strong></td>
                <td>Allows church members to register and access the system.</td>
            </tr>
            <tr>
                <td><strong>Event Management</strong></td>
                <td>Create and manage church events.</td>
            </tr>
            <tr>
                <td><strong>Attendance Tracking</strong></td>
                <td>Monitor attendance for church activities.</td>
            </tr>
            <tr>
                <td><strong>Announcements</strong></td>
                <td>Publish important church notices.</td>
            </tr>
            <tr>
                <td><strong>Reports</strong></td>
                <td>Generate church management reports.</td>
            </tr>
        </tbody>
    </table>

    <!-- Scripture/Thematic Block -->
    <div class="scripture">
        <p><em>"Everything should be done decently and in good order."</em></p>
        <strong>1 Corinthians 14:40</strong>
    </div>

    <!-- Formal Academic Sign-off Row -->
    <div class="signature-container">
        <div class="sig-box">
            <div class="sig-line"></div>
            <strong>Samuel Ng'ang'a Mwihaki</strong>
            <span>Student Developer</span>
        </div>
        
        <div class="sig-box">
            <div class="sig-line"></div>
            <strong>David Mwangi</strong>
            <span>Project Supervisor</span>
        </div>
    </div>

</div>

</body>
</html>