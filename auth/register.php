<?php
include("../config.php");

if(isset($_POST['register'])){

    // SANITIZE INPUTS
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone    = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $password = md5($_POST['password']);

    /*
    ========================================
    VALIDATION SECTION
    ========================================
    */

    // Validate Full Name (letters and spaces only)
    if(!preg_match("/^[A-Za-z\s]+$/", $name)){
        $error = "Name should contain letters only.";
    }

    // Validate Email
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email address.";
    }

    // Validate Phone Number (numbers only)
    elseif(!preg_match("/^[0-9]{10,13}$/", $phone)){
        $error = "Phone number must contain 10-13 digits only.";
    }

    // Validate Password Length
    elseif(strlen($_POST['password']) < 6){
        $error = "Password must be at least 6 characters.";
    }

    else{

        /*
        ========================================
        CHECK IF EMAIL EXISTS
        ========================================
        */

        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if(mysqli_num_rows($check) > 0){

            $error = "Email already registered.";

        } else {

            /*
            ========================================
            INSERT USER
            ========================================
            */

            $sql = "INSERT INTO users(name,email,password,phone)
                    VALUES('$name','$email','$password','$phone')";

            if(mysqli_query($conn, $sql)){

                header("Location: login.php");
                exit();

            } else {

                $error = "Registration failed.";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parish Registration</title>

    <style>
        :root {
            --gold-accent: #e5c158;
            --dark-navy: #0a192f;
            --white-pure: #ffffff;
            --input-bg: rgba(255, 255, 255, 0.9);
            --error-red: #ff4d4d;
            --success-green: #28a745;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body{
            background:
            linear-gradient(rgba(10,25,47,0.55), rgba(10,25,47,0.75)),
            url('images/church-interior.jpg') no-repeat center center/cover;

            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
            background-attachment:fixed;
        }

        .form-card{
            background:rgba(255,255,255,0.12);
            width:100%;
            max-width:480px;
            padding:45px 40px;
            border-radius:16px;
            backdrop-filter:blur(12px);
            -webkit-backdrop-filter:blur(12px);
            border:1px solid rgba(255,255,255,0.25);
            box-shadow:0 20px 40px rgba(0,0,0,0.3);
            text-align:center;
        }

        .form-card h2{
            color:var(--white-pure);
            font-size:30px;
            font-weight:600;
            margin-bottom:6px;
        }

        .tagline{
            color:rgba(255,255,255,0.75);
            font-size:14px;
            margin-bottom:35px;
        }

        .form-group{
            margin-bottom:20px;
            text-align:left;
        }

        .form-group input{
            width:100%;
            padding:14px 18px;
            background:var(--input-bg);
            border:2px solid transparent;
            border-radius:8px;
            font-size:15px;
            color:var(--dark-navy);
            outline:none;
            transition:all 0.3s ease;
        }

        .form-group input:focus{
            border-color:var(--gold-accent);
            background:var(--white-pure);
            box-shadow:0 0 15px rgba(229,193,88,0.3);
        }

        .btn-submit{
            width:100%;
            padding:15px;
            background:var(--gold-accent);
            color:var(--dark-navy);
            border:none;
            border-radius:8px;
            font-size:16px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:1px;
            cursor:pointer;
            transition:0.3s;
            margin-top:10px;
        }

        .btn-submit:hover{
            background:#f0d276;
            transform:translateY(-1px);
        }

        .redirect-box{
            margin-top:25px;
            font-size:14px;
            color:rgba(255,255,255,0.7);
        }

        .redirect-box a{
            color:var(--gold-accent);
            text-decoration:none;
            font-weight:600;
        }

        .redirect-box a:hover{
            text-decoration:underline;
        }

        .error-box{
            background:rgba(255,77,77,0.15);
            color:#fff;
            padding:12px;
            border-radius:8px;
            margin-bottom:20px;
            border-left:4px solid var(--error-red);
            text-align:left;
            font-size:14px;
        }

        @media(max-width:480px){

            .form-card{
                padding:35px 20px;
            }

            .form-card h2{
                font-size:26px;
            }
        }

    </style>
</head>
<body>

<div class="form-card">

    <h2>User Registration</h2>

    <p class="tagline">
        Create an account to access the parish portal
    </p>

    <!-- ERROR DISPLAY -->
    <?php if(isset($error)): ?>
        <div class="error-box">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <!-- FULL NAME -->
        <div class="form-group">
            <input
                type="text"
                name="name"
                placeholder="Full Name"
                pattern="[A-Za-z\s]+"
                title="Name should contain letters only"
                required
            >
        </div>

        <!-- EMAIL -->
        <div class="form-group">
            <input
                type="email"
                name="email"
                placeholder="Email Address"
                required
            >
        </div>

        <!-- PASSWORD -->
        <div class="form-group">
            <input
                type="password"
                name="password"
                placeholder="Password"
                minlength="6"
                required
            >
        </div>

        <!-- PHONE -->
        <div class="form-group">
            <input
                type="tel"
                name="phone"
                placeholder="Enter Phone Number"
                pattern="[0-9]{10,13}"
                maxlength="13"
                title="Phone number must contain numbers only"
                required
            >
        </div>

        <!-- SUBMIT BUTTON -->
        <button type="submit" name="register" class="btn-submit">
            Register
        </button>

        <!-- LOGIN REDIRECT -->
        <div class="redirect-box">
            Already have a parish profile?
            <a href="login.php">Login here</a>
        </div>

    </form>

</div>

</body>
</html>