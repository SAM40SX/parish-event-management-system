<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Sacred Heart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Fonts for Cohesive Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --cathedral-blue: #0A192F;      /* Deep, reverent navy */
            --liturgical-gold: #D4AF37;    /* Authentic metallic gold tone */
            --soft-gold: #F4E8C1;          /* Creamy, warm accent */
            --holy-white: #FCFBF9;         /* Off-white parchment style */
            --marble-gray: #F0F2F5;        /* Background color */
            --text-dark: #2C3E50;          /* Main text */
            --font-serif: 'Cinzel', Georgia, serif;
            --font-sans: 'Inter', Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background: var(--marble-gray);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .profile-container {
            width: 100%;
            max-width: 550px;
            perspective: 1000px; /* Slight depth effect */
        }

        .profile-box {
            background: var(--holy-white);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(10, 25, 47, 0.08);
            border: 1px solid rgba(214, 175, 55, 0.2);
            position: relative;
        }

        /* Elegant gold accent top bar */
        .profile-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--cathedral-blue), var(--liturgical-gold), var(--cathedral-blue));
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        h1 {
            font-family: var(--font-serif);
            color: var(--cathedral-blue);
            text-align: center;
            margin-bottom: 35px;
            font-size: 28px;
            letter-spacing: 1px;
            border-bottom: 2px dashed var(--soft-gold);
            padding-bottom: 15px;
        }

        /* Profile details container */
        .profile-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Row structure for a cleaner, modern look */
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        strong {
            font-family: var(--font-serif);
            color: var(--cathedral-blue);
            font-size: 15px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .value {
            font-size: 16px;
            color: var(--text-dark);
            font-weight: 500;
        }

        /* Premium badge styling for User Role */
        .role-badge {
            background: rgba(212, 175, 55, 0.15);
            color: #997300;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        /* Action Buttons */
        .actions {
            margin-top: 15px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background: var(--cathedral-blue);
            color: var(--soft-gold);
            border: 1px solid var(--cathedral-blue);
        }

        .btn-primary:hover {
            background: var(--liturgical-gold);
            color: var(--cathedral-blue);
            border-color: var(--liturgical-gold);
            box-shadow: 0 4px 12px rgba(214, 175, 55, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #7F8C8D;
            border: 1px solid #BDC3C7;
        }

        .btn-secondary:hover {
            background: #EAECEE;
            color: var(--text-dark);
        }

    </style>
</head>
<body>

<div class="profile-container">

    <div class="profile-box">

        <h1>Parishioner Profile</h1>

        <div class="profile-details">
            <div class="info-row">
                <strong>Full Name</strong>
                <span class="value"><?php echo htmlspecialchars($user['name']); ?></span>
            </div>

            <div class="info-row">
                <strong>Email Address</strong>
                <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>

            <div class="info-row">
                <strong>Parish Role</strong>
                <div>
                    <span class="role-badge"><?php echo htmlspecialchars($user['role']); ?></span>
                </div>
            </div>
        </div>

        <!-- BUTTONS: Adding standard luxury app controls -->
        <div class="actions">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
        </div>

    </div>

</div>

</body>
</html>