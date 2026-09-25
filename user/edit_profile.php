<?php
include("../config.php");
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

$user = $_SESSION['user'];
$message = "";

/* UPDATE PROFILE */
if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $id = $user['id'];

    $update = mysqli_query($conn,"
    UPDATE users
    SET
    name='$name',
    email='$email'
    WHERE id='$id'
    ");

    if($update){
        /* UPDATE SESSION */
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $user = $_SESSION['user'];

        $message = "Profile Updated Successfully";
    }else{
        $message = "Failed To Update Profile";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | Sacred Heart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Premium Typography Selection -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --cathedral-blue: #0A192F;      /* Deep reverent navy */
            --liturgical-gold: #D4AF37;    /* Classic rich gold */
            --soft-gold: #F4E8C1;          /* Warm parchment accent */
            --holy-white: #FCFBF9;         /* Premium paper element tone */
            --font-serif: 'Cinzel', Georgia, serif;
            --font-sans: 'Inter', Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: 
                linear-gradient(rgba(10, 25, 47, 0.85), rgba(10, 25, 47, 0.85)),
                url('../images/church_background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: var(--font-sans);
            padding: 40px 20px;
        }

        /* --- PROFILE WINDOW CONTAINER --- */
        .profile-box {
            width: 100%;
            max-width: 520px;
            background: rgba(253, 251, 249, 0.06);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(214, 175, 55, 0.25);
            border-radius: 20px;
            padding: 40px;
            color: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            position: relative;
            overflow: hidden;
        }

        /* --- SCRIPTURAL ACCENT BANNER --- */
        .verse-banner {
            text-align: center;
            background: rgba(10, 25, 47, 0.6);
            border: 1px solid rgba(214, 175, 55, 0.3);
            border-radius: 12px;
            padding: 20px 15px;
            margin-bottom: 30px;
        }

        /* Pure CSS Styled Sacred Cross Symbol */
        .sacred-cross {
            width: 12px;
            height: 28px;
            background: var(--liturgical-gold);
            margin: 0 auto 12px auto;
            position: relative;
            border-radius: 1px;
            box-shadow: 0 0 10px rgba(214, 175, 55, 0.4);
        }

        .sacred-cross::after {
            content: '';
            position: absolute;
            top: 7px;
            left: -8px;
            width: 28px;
            height: 12px;
            background: var(--liturgical-gold);
            border-radius: 1px;
        }

        .verse-text {
            font-family: var(--font-serif);
            font-size: 13px;
            font-style: italic;
            color: var(--soft-gold);
            line-height: 1.6;
            letter-spacing: 0.5px;
        }

        .verse-reference {
            font-family: var(--font-sans);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 8px;
            display: block;
        }

        /* --- HEADINGS --- */
        .profile-box h1 {
            font-family: var(--font-serif);
            text-align: center;
            margin-bottom: 30px;
            color: var(--liturgical-gold);
            font-size: 26px;
            letter-spacing: 1px;
        }

        /* --- SYSTEM APP FEEDBACK --- */
        .message {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
        }

        /* --- ENTRY LABELS & INPUTS --- */
        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--soft-gold);
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(10, 25, 47, 0.4);
            border-radius: 8px;
            outline: none;
            font-size: 15px;
            color: white;
            font-family: var(--font-sans);
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: var(--liturgical-gold);
            box-shadow: 0 0 12px rgba(214, 175, 55, 0.2);
            background: rgba(10, 25, 47, 0.6);
        }

        /* Disabled layout styling for Roles */
        .form-group input:disabled {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.4);
            border-color: rgba(255, 255, 255, 0.05);
            cursor: not-allowed;
        }

        /* --- ACTIONS BUTTONS --- */
        button {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--liturgical-gold);
            border-radius: 8px;
            background: var(--liturgical-gold);
            color: var(--cathedral-blue);
            font-family: var(--font-serif);
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background: transparent;
            color: var(--soft-gold);
            box-shadow: 0 5px 15px rgba(214, 175, 55, 0.2);
        }

        /* --- BOTTOM FOOTER PARISH BRANDING --- */
        .info {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid rgba(214, 175, 55, 0.15);
            padding-top: 20px;
        }

        .info p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.5px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--soft-gold);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .back:hover {
            color: var(--liturgical-gold);
        }

        @media(max-width: 600px){
            .profile-box { padding: 30px 20px; }
        }
    </style>
</head>
<body>

<div class="profile-box">

    <!-- LITURGICAL VERSE BANNER -->
    <div class="verse-banner">
        <div class="sacred-cross"></div>
        <p class="verse-text">"Create in me a clean heart, O God, and renew a right spirit within me."</p>
        <span class="verse-reference">Psalm 51:10</span>
    </div>

    <h1>Edit Profile</h1>

    <?php if($message != ""){ ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input 
                type="text" 
                name="name" 
                value="<?php echo htmlspecialchars($user['name']); ?>" 
                required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input 
                type="email" 
                name="email" 
                value="<?php echo htmlspecialchars($user['email']); ?>" 
                required>
        </div>

        <div class="form-group">
            <label>System Role</label>
            <input 
                type="text" 
                value="<?php echo htmlspecialchars($user['role']); ?>" 
                disabled>
        </div>

        <button type="submit" name="update">
            Save Modifications
        </button>
    </form>

    <div class="info">
        <p>Sacred Heart Catholic Church Parish Portal</p>
    </div>

    <a class="back" href="dashboard.php">
        ← Return to Dashboard
    </a>

</div>

</body>
</html>