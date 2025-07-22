<?php
include 'connect.php';

$registration_status = "";
$registration_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $password = $_POST['password']; // storing raw password

    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_status = "fail";
        $registration_error = "Invalid email format.";
    } else {
        // Use raw password directly
        $sql = "INSERT INTO admin (full_name, email, contact_number, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $full_name, $email, $contact_number, $password);
            if ($stmt->execute()) {
                $registration_status = "success";
            } else {
                $registration_status = "fail";
                $registration_error = $stmt->error;
            }
            $stmt->close();
        } else {
            $registration_status = "fail";
            $registration_error = "Failed to prepare statement.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
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
            background-color: #111;
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
            margin-top: 20px;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            color: orange;
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

        form input[type="text"],
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
            background-color: rgb(243, 135, 11);
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
        }

        .back-btn a {
            text-decoration: none;
            color: white;
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
    <button class="back-btn"><a href="index.php">← Back</a></button>
    <div class="container">
        <div class="logo-section">
            <img src="logo.png" alt="Logo">
            <div class="logo-text">DONSAL'S EXPRESS CORPORATION</div>
        </div>
        <div class="form-section">
            <h2>Create Account</h2>
            <form method="post" action="">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="contact_number" placeholder="Contact Number" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>

            <div style="margin-top: 15px; text-align: center;">
                Already have an account?
                <a href="login.php" style="color:rgb(245, 155, 20); text-decoration: none;">Login here</a>
            </div>
        </div>
    </div>

    <?php if ($registration_status === "success"): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful',
                text: 'Redirecting to admin dashboard...',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "admin_dashboard.php";
            });
        </script>
    <?php elseif ($registration_status === "fail"): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: '<?php echo addslashes($registration_error); ?>',
                confirmButtonColor: '#f59e0b'
            });
        </script>
    <?php endif; ?>
</body>
</html>
