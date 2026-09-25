<?php
include("../config.php");
session_start();

$error_msg = ""; // Variable to hold login error safely

if(isset($_POST['login'])){

    // Sanitize user inputs to prevent basic SQL Injection
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // md5 used per original code logic
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user'] = $user;

        if($user['role'] == 'admin'){
            header("Location: ../admin/dashboard.php");
            exit();
        }else{
            header("Location: ../user/dashboard.php");
            exit();
        }
    }else{
        $error_msg = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parish Login</title>
    <style>
        :root {
            --church-gold: #D4AF37;
            --deep-wood: #2c1a04;
            --charcoal: #333333;
            --soft-white: #ffffff;
            --error-red: #d9534f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            /* Replace with a beautiful warm image like candle altars or soft focus church interiors */
            background: linear-gradient(rgba(26, 15, 2, 0.7), rgba(0, 0, 0, 0.75)), 
                        url('images/church-interior.jpg') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Clean Login Card */
        .login-card {
            background: var(--soft-white);
            width: 100%;
            max-width: 420px;
            padding: 45px 35px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            border-top: 5px solid var(--church-gold);
            text-align: center;
        }

        .login-card h2 {
            color: var(--deep-wood);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-card p.subtitle {
            color: #777;
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* Error Banner */
        .error-banner {
            background: #fdf2f2;
            color: var(--error-red);
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            border-left: 4px solid var(--error-red);
            text-align: left;
        }

        /* Input Layouts */
        .input-wrapper {
            margin-bottom: 20px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #cccccc;
            border-radius: 6px;
            font-size: 15px;
            color: var(--charcoal);
            background-color: #fcfcfc;
            outline: none;
            transition: all 0.3s ease;
        }

        /* Interactive input glow */
        .input-wrapper input:focus {
            border-color: var(--church-gold);
            background-color: var(--soft-white);
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.2);
        }

        /* Gold Login Action Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--deep-wood);
            color: var(--soft-white);
            border: 1px solid var(--deep-wood);
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: var(--church-gold);
            border-color: var(--church-gold);
            color: var(--deep-wood);
        }

        /* Navigation Assistance Footer links */
        .form-footer {
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }

        .form-footer a {
            color: var(--church-gold);
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to manage your parish account</p>

        <?php if(!empty($error_msg)): ?>
            <div class="error-banner">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-wrapper">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="input-wrapper">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="btn-login">Login</button>
            
            <div class="form-footer">
                New to the parish? <a href="register.php">Create an profile</a>
            </div>
        </form>
    </div>

</body>
</html>