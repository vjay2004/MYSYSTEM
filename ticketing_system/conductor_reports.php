<?php
session_start();
include('connect.php');

// Check if conductor is logged in
$conductor_id = $_SESSION['conductor_id'] ?? null;

if (!$conductor_id) {
    header('Location: conductor_login.php');
    exit();
}

// Get conductor's name for display
$conductor_name = '';
$stmt = $conn->prepare("SELECT full_name FROM conductor WHERE conductor_id = ?");
$stmt->bind_param("i", $conductor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $conductor_name = $row['full_name'];
}
$stmt->close();

// Get daily reports for this conductor
$daily_reports = [];
$stmt = $conn->prepare("SELECT * FROM report WHERE conductor_id = ? ORDER BY report_date DESC");
$stmt->bind_param("i", $conductor_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $daily_reports[] = $row;
}
$stmt->close();

// Get all-time summary
$all_time_summary = [];
$stmt = $conn->prepare("
    SELECT 
        SUM(total_tickets) AS total_tickets,
        SUM(total_earnings) AS total_earnings
    FROM report 
    WHERE conductor_id = ?
");
$stmt->bind_param("i", $conductor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $all_time_summary = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Conductor Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
        }

        .sidebar {
            width: 220px;
            background: #000;
            color: #fff;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: #f59e0b;
        }

        .main-content {
            margin-left: 220px;
            padding: 30px;
            background: #f3f4f6;
            width: calc(100% - 220px);
        }

        h1, h2, h3 {
            color: #f59e0b;
        }

        .report-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f59e0b;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .summary-card {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 30%;
            text-align: center;
        }

        .card h3 {
            margin-top: 0;
            color: #333;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #f59e0b;
            margin-bottom: 0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 50px;
            height: auto;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="logo.png" alt="Donsal's Express Logo" style="width:100px; margin-bottom: 10px;">
        <h2>Donsal's Express Corporation</h2>
    </div>
    <a href="conductor_dashboard.php">Dashboard</a>
    <a href="ticket_records.php">Ticket History</a>
    <a href="conductor_refund.php">Refund Ticket</a>
    <a href="conductor_reports.php">Report</a>
    <a href="conductor_login.php">Logout</a>
</div>

<div class="main-content">
    <?php if ($conductor_name): ?>
        <div style="text-align: left; padding: 10px 0; font-weight: bold; font-size: 30px;">
            Welcome, Conductor <?= htmlspecialchars($conductor_name) ?>
        </div>
    <?php endif; ?>
    
    <h1>Conductor Reports</h1>
    
    <div class="summary-card">
        <div class="card">
            <h3>Today's Tickets</h3>
            <p><?= isset($daily_reports[0]['total_tickets']) ? $daily_reports[0]['total_tickets'] : 0 ?></p>
        </div>
        <div class="card">
            <h3>Today's Earnings</h3>
            <p>₱<?= isset($daily_reports[0]['total_earnings']) ? number_format($daily_reports[0]['total_earnings'], 2) : '0.00' ?></p>
        </div>
        <div class="card">
            <h3>All-Time Earnings</h3>
            <p>₱<?= isset($all_time_summary['total_earnings']) ? number_format($all_time_summary['total_earnings'], 2) : '0.00' ?></p>
        </div>
    </div>
    
    <div class="report-section">
        <h2>Daily Reports</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Tickets Issued</th>
                    <th>Total Earnings</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daily_reports)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No reports available</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daily_reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['report_date']) ?></td>
                            <td><?= htmlspecialchars($report['total_tickets']) ?></td>
                            <td>₱<?= number_format($report['total_earnings'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>