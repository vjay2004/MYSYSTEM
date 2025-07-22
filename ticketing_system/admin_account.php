<?php
session_start();
include 'connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch conductor and inspector accounts
$conductors = $conn->query("SELECT * FROM conductor WHERE admin_id = $admin_id");
$inspectors = $conn->query("SELECT * FROM inspector WHERE admin_id = $admin_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Lists</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            display: flex;
        }

        .sidebar {
            width: 220px;
            background-color: #111;
            padding: 30px 15px;
            height: 100vh;
            position: fixed;
            color: white;
            box-sizing: border-box;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
        }

        .sidebar img {
            width: 100px;
            height: auto;
            display: block;
            margin: 0 auto 20px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #f59e0b;
        }

     
        .main-content {
            margin-left: 240px;
            padding: 30px;
            width: 100%;
        }

        h4 {
            color: #f59e0b;
            margin-top: 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background-color: #fff;
            box-shadow: 0 0 8px #ccc;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f59e0b;
            color: white;
        }

        tr:hover {
            background-color: #f3f4f6;
        }

        .back-button {
            background: #111;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<div class="sidebar">
   
    <img src="logo.png" alt="Company Logo">

    <h4>Donsal's Express Corporation</h4>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_account.php">Created Accounts</a>
    <a href="admin_report.php">Admin Reports</a>
    
  
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <a href="admin_dashboard.php" class="back-button">← Back to Dashboard</a>

    <h2>Conductor Accounts</h2>
    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $conductors->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Inspector Accounts</h2>
    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Password</th>

            </tr>
        </thead>
        <tbody>
            <?php while ($row = $inspectors->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
