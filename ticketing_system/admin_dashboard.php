<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

$message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $password = $_POST['password'];

    if ($role === 'conductor') {
        $sql = "INSERT INTO conductor (admin_id, full_name, email, contact_number, password) VALUES (?, ?, ?, ?, ?)";
    } elseif ($role === 'inspector') {
        $sql = "INSERT INTO inspector (admin_id, full_name, email, contact_number, password) VALUES (?, ?, ?, ?, ?)";
    }

    if (isset($sql)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $admin_id, $full_name, $email, $contact_number, $password);
        if ($stmt->execute()) {
            $message = ucfirst($role) . " account created successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #333;
        }

        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: 220px;
            background: #111;
            color: #fff;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .sidebar img {
            width: 100px;
            margin: 0 auto 10px;
            display: block;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: orange;
            color: #000;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }

        h2, h3 {
            color: #f59e0b;
        }

        form {
            background: #fff;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ddd;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background: #f59e0b;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #d97706;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <img src="logo.png" alt="Donsal's Express Logo">
        <h2>Donsal's Express Corporation</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_account.php">Created Accounts</a>
        <a href="admin_report.php">Admin Reports</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="main-content">
        <?php if ($message): ?>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                $(document).ready(function () {
                    Swal.fire({
                        icon: '<?php echo (strpos($message, "successfully") !== false) ? 'success' : 'error'; ?>',
                        title: '<?php echo (strpos($message, "successfully") !== false) ? 'Success' : 'Error'; ?>',
                        text: "<?php echo $message; ?>"
                    });
                });
            </script>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin_name'])): ?>
            <div style="text-align: left; padding: 10px 0; font-weight: bold; font-size: 30px;">
                Welcome, Admin <?= htmlspecialchars($_SESSION['admin_name']) ?>
            </div>
        <?php endif; ?>

        <h3>Create Conductor Account</h3>
        <form method="post">
            <input type="hidden" name="role" value="conductor">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="contact_number" placeholder="Contact Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Conductor</button>
        </form>

        <h3>Create Inspector Account</h3>
        <form method="post">
            <input type="hidden" name="role" value="inspector">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="contact_number" placeholder="Contact Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Inspector</button>
        </form>
    </div>
</div>

</body>
</html>
