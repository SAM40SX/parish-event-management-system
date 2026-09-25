<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sacred Heart Parish | Event Management System</title>
    
    <!-- Google Fonts for High-End Liturgical & Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Premium Monoline Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Premium Liturgical Color Palette & Variables */
        :root {
            --primary-color: #0b1a30;     /* Royal Cathedral Navy */
            --primary-dark: #050d1a;      /* Deep Midnight Shadow */
            --accent-gold: #cfac62;       /* Warm Burnished/Chalice Gold */
            --accent-gold-light: #f4ebd0; /* Soft Incense Cream */
            --text-dark: #222222;         /* Charcoal Black */
            --text-light: #ffffff;        /* Pure White */
            --bg-light: #fbfbf9;          /* Marble Off-White */
            --card-shadow: 0 15px 35px rgba(11, 26, 48, 0.08);
            --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Montserrat', sans-serif;
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Delicate Gold Border */
        body::before {
            content: '';
            display: block;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-gold), var(--primary-color));
            width: 100%;
        }

        /* Navigation Bar */
        nav {
            background: rgba(11, 26, 30, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(207, 172, 98, 0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        nav .logo {
            color: var(--text-light);
            font-family: 'Cinzel', serif;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        nav .logo i {
            color: var(--accent-gold);
            font-size: 1.3rem;
        }

        nav .logo span {
            color: var(--accent-gold);
            font-weight: 400;
        }

        /* Premium Cinematic Hero Background */
        header {
            width: 100%;
            height: 85vh;
            
            background: 
                radial-gradient(circle, rgba(11, 26, 48, 0.45) 0%, rgba(5, 13, 26, 0.85) 100%),
                url('images/church.jpg');
            background-size: cover;
            background-position: center 35%;
            background-repeat: no-repeat;
            background-attachment: fixed;

            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--text-light);
            padding: 0 20px;
        }

        .welcome-box {
            background: rgba(5, 13, 26, 0.45);
            padding: 60px 50px;
            border-radius: 4px; 
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(207, 172, 98, 0.25);
            max-width: 850px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.45);
            position: relative;
        }

        .welcome-box::before {
            content: "\f2a6"; 
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--accent-gold);
            font-size: 24px;
            display: block;
            margin-bottom: 15px;
            opacity: 0.85;
        }

        header h1 {
            font-family: 'Cinzel', serif;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 2px;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
            color: #ffffff;
        }

        header p {
            font-size: 16px;
            margin-bottom: 40px;
            font-weight: 400;
            color: var(--accent-gold-light);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Action Buttons */
        .buttons {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .buttons a {
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 2px;
            font-size: 13px;
            font-weight: 600;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .btn-login {
            background: linear-gradient(135deg, #dfbc73, var(--accent-gold));
            color: var(--primary-dark);
            border: 1px solid var(--accent-gold);
            box-shadow: 0 4px 15px rgba(207, 172, 98, 0.3);
        }

        .btn-login:hover {
            background: transparent;
            color: var(--text-light);
            border-color: var(--text-light);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.15);
        }

        .btn-register {
            background: transparent;
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .btn-register:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-gold);
            color: var(--accent-gold);
            transform: translateY(-3px);
        }

        /* Informational Card Section */
        .info-section {
            max-width: 1200px;
            margin: -60px auto 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            position: relative;
            z-index: 10;
        }

        .card {
            background: var(--text-light);
            padding: 40px 35px;
            border-radius: 4px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(207, 172, 98, 0.15);
            border-top: 5px solid var(--accent-gold);
            text-align: center;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(11, 26, 48, 0.12);
            border-top-color: var(--primary-color);
        }

        .card-icon {
            font-size: 32px;
            color: var(--accent-gold);
            margin-bottom: 20px;
        }

        .card h3 {
            font-family: 'Cinzel', serif;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        .card p {
            color: #555555;
            font-size: 15px;
            font-weight: 400;
        }

        /* Premium Framed Inline Content Image Styling */
        .media-container {
            max-width: 800px;
            margin: 40px auto 80px auto;
            padding: 0 20px;
            text-align: center;
        }

        .parish-image {
            width: 100%;
            max-width: 700px;
            height: auto;
            border-radius: 4px;
            border: 1px solid rgba(207, 172, 98, 0.35);
            box-shadow: 0 20px 45px rgba(11, 26, 48, 0.12);
            display: block;
            margin: 0 auto 15px auto;
            transition: var(--transition);
        }

        .parish-image:hover {
            border-color: var(--accent-gold);
            transform: scale(1.01);
        }

        .image-caption {
            font-size: 13px;
            color: #777777;
            font-style: italic;
            letter-spacing: 0.5px;
        }

        /* Footer Section */
        footer {
            background: var(--primary-dark);
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            padding: 35px 20px;
            font-size: 13px;
            border-top: 1px solid rgba(207, 172, 98, 0.3);
            letter-spacing: 0.5px;
        }
        
        footer span {
            color: var(--accent-gold);
            font-family: 'Cinzel', serif;
        }

        /* Clean Adaptability Layouts */
        @media (max-width: 768px) {
            header h1 { font-size: 28px; }
            header p { font-size: 13px; }
            .welcome-box { padding: 40px 25px; }
            header { height: auto; padding: 100px 20px; background-attachment: scroll; }
            .info-section { margin-top: 40px; }
            .media-container { margin-bottom: 50px; }
        }
    </style>
</head>
<body>

    <!-- Premium Header Navbar -->
    <nav>
        <div class="logo">
            <i class="fa-solid fa-church"></i> Sacred Heart <span>Parish</span>
        </div>
    </nav>

    <!-- Sanctuary Styled Hero Banner -->
    <header>
        <div class="welcome-box">
            <h1>Sacred Heart of Jesus Catholic Church</h1>
            <p>Parish Event Management System</p>
            <div class="buttons">
                <a href="auth/login.php" class="btn-login"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                <a href="auth/register.php" class="btn-register"><i class="fa-solid fa-user-plus"></i> Register</a>
            </div>
        </div>
    </header>

    <!-- Dynamic Information Cards -->
    <section class="info-section">
        <div class="card">
            <div class="card-icon"><i class="fa-solid fa-calendar-days"></i></div>
            <h3>Parish Events</h3>
            <p>Stay up to date with our community liturgical feast days, parish volunteer initiatives, and special ministry schedules.</p>
        </div>
        <div class="card">
            <div class="card-icon"><i class="fa-solid fa-dove"></i></div>
            <h3>Reservations</h3>
            <p>Registered parishioners can request sanctuary hall bookings, coordinate Sacraments, and view active schedules effortlessly.</p>
        </div>
    </section>

    <!-- Premium Inline Image Implementation -->
    <section class="media-container">
        <!-- Change 'images/sanctuary.jpg' to any inside showcase photo you wish to view -->
        <img src="images/church.jpg" alt="Sacred Heart Parish Community Sanctuary" class="parish-image">
        <p class="image-caption">Inside the Sacred Heart Sanctuary — Built for Community Prayer and Fellowship</p>
    </section>

    <!-- Sacred-Styled Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> <span>Sacred Heart of Jesus Catholic Church</span> | Developed by Samuel Ng'ang'a | All Rights Reserved</p>
    </footer>

</body>
</html>