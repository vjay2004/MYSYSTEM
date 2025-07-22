<?php
session_start();
include('connect.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch conductor reports
$query = "
    SELECT 
        r.report_id,
        c.full_name AS conductor_name,
        r.total_tickets,
        r.total_earnings,
        r.report_date
    FROM report r
    JOIN conductor c ON r.conductor_id = c.conductor_id
    ORDER BY r.report_date DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Conductor Reports</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
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

        .sidebar img {
            display: block;
            margin: 0 auto 20px;
            width: 100px;
            height: auto;
        }

        .sidebar h2 {
            color: #f59e0b;
            margin-bottom: 30px;
            text-align: center;
            font-size: 30px;
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

        h2 {
            color: orange;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f59e0b;
            color: white;
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
    <h2>Donsal's Express</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_account.php">Created Accounts</a>
    <a href="admin_report.php">Admin Reports</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <a href="admin_dashboard.php" class="back-button">← Back to Dashboard</a>
    <h2>Conductor Daily Reports</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Conductor Name</th>
                    <th>Total Tickets</th>
                    <th>Total Earnings</th>
                    <th>Report Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['report_id']) ?></td>
                        <td><?= htmlspecialchars($row['conductor_name']) ?></td>
                        <td><?= htmlspecialchars($row['total_tickets']) ?></td>
                        <td>₱<?= number_format($row['total_earnings'], 2) ?></td>
                        <td><?= htmlspecialchars($row['report_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No reports found.</p>
    <?php endif; ?>
</div>

</body>
</html>
