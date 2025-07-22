<?php
session_start();
include 'connect.php';

$login_status = "";
$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // plain text comparison

    $sql = "SELECT * FROM admin WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $login_status = "success";
    } else {
        $login_status = "fail";
        $login_error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f59e0b;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .logo-section {
            width: 40%;
            background-color:rgb(17, 17, 17);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .logo-section img {
            width: 150px;
            height: auto;
        }

        .logo-text {
            color: orange;
            margin-top: 20px;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
        }

        .form-section {
            width: 60%;
            background-color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-section h2 {
            margin-bottom: 20px;
        }

        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        form button {
            width: 100%;
            padding: 12px;
            background-color: #f59e0b;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #d97706;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            text-decoration: none;
        }

        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .logo-section,
            .form-section {
                width: 100%;
            }
        }
    </style>

    <!-- jQuery & SweetAlert2 CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <a class="back-btn" href="index.php">← Back</a>
    <div class="container">
        <div class="logo-section">
            <img src="logo.png" alt="Logo">
            <div class="logo-text">DONSAL'S EXPRESS CORPORATION</div>
        </div>
        <div class="form-section">
            <h2>Login Account</h2>
        <form method="post" action="">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>

        <!-- Add this block below the form -->
        <div style="margin-top: 15px; text-align: center;">
            Don't have an account? 
            <a href="registration.php" style="color: #f59e0b; text-decoration: none;">Register here</a>
        </div>

        </div>
    </div>

    <?php if ($login_status === "success"): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Login Successful',
                text: 'Redirecting to admin dashboard...',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "admin_dashboard.php";
            });
        </script>
    <?php elseif ($login_status === "fail"): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '<?php echo $login_error; ?>',
                confirmButtonColor: '#f59e0b'
            });
        </script>
    <?php endif; ?>
</body>
</html>
