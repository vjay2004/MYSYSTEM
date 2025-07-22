<?php
session_start();
include 'connect.php';

$loginSuccess = null; // Track login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM inspector WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['inspector_id'] = $user['inspector_id'];
        $_SESSION['inspector_name'] = $user['full_name'];
        $loginSuccess = true;
    } else {
        $loginSuccess = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inspector Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 40%;
            background-color: #111;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .logo {
            width: 150px;
            height: auto;
        }

        h3 {
            color: orange;
            margin-top: 20px;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        .main {
            flex: 1;
            padding: 50px;
            background-color: #f4f4f4;
        }

        h2 {
            margin-top: 0;
        }

        form {
            margin-top: 20px;
            max-width: 400px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: orange;
            color: white;
            border: none;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: darkorange;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <button class="back-button" onclick="goBack()">← Back</button>
    <img src="logo.png" alt="Logo" class="logo">
    <h3>Donsal's Express Corporation</h3>
</div>

<div class="main">
    <h2>Inspector Login</h2>
    <form method="post" id="loginForm">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</div>

<!-- jQuery and SweetAlert2 -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
     function goBack() {
        window.location.href = "index.php"; // Replace with the correct page
    }

    <?php if ($loginSuccess !== null): ?>
        $(document).ready(function () {
            <?php if ($loginSuccess): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "inspector_dashboard.php";
                });
            <?php else: ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: 'Invalid email or password.'
                });
            <?php endif; ?>
        });
    <?php endif; ?>
</script>

</body>
</html>
