<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Northern Highlands Resort: Reservation System</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('resort-bg.jpg') no-repeat center center/cover;
            color: #fff;
            text-align: center;
        }
        .overlay {
            background: rgba(0,0,0,0.6);
            min-height: 100vh;
            padding: 50px 20px;
        }
        h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }
        p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        .buttons {
            margin-top: 30px;
        }
        .buttons a {
            display: inline-block;
            margin: 10px;
            padding: 12px 25px;
            background: #27ae60;
            color: #fff;
            font-size: 16px;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }
        .buttons a:hover {
            background: #219150;
        }
        footer {
            margin-top: 50px;
            font-size: 14px;
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <h1>Welcome to Northern Highlands Resort</h1>
        <p>Experience comfort and luxury. Book rooms and events with ease using our Reservation System.</p>

        <div class="buttons">
            <a href="guest_register.php">Register</a>
            <a href="login.php">Login</a>
            <a href="contact.php">Contact Us</a>
        </div>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Northern Highlands Resort. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
