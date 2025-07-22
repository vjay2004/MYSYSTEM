<?php
session_start();
include('connect.php');

// Check if conductor is logged in
$conductor_id = $_SESSION['conductor_id'] ?? null;

if (!$conductor_id) {
    header('Location: conductor_login.php');
    exit();
}

$stmt = $conn->prepare("SELECT ticket_id, passenger_id, origin, destination, fare, discount FROM tickets WHERE conductor_id = ? ORDER BY ticket_id DESC");
$stmt->bind_param("i", $conductor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket History</title>
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
            background: orange;
        }

        .main-content {
            margin-left: 220px;
            padding: 30px;
            background: #f3f4f6;
            width: calc(100% - 220px);
        }

        h2 {
            color: orange;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f59e0b;
            color: #fff;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: orange;
            text-decoration: none;
        }

        .back-link:hover {
            background: white;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="text-align:center;">
        <img src="logo.png" alt="Donsal's Express Logo" style="width:100px; margin-bottom: 10px;">
    </div>
    <h2>Donsal's Express Corporation</h2>
    <a href="conductor_dashboard.php">Dashboard</a>
    <a href="ticket_records.php">Ticket History</a>
    <a href="conductor_refund.php">Refund Ticket</a>
    <a href="conductor_reports.php">Report</a>
    <a href="logout.php">Logout</a>
</div>


<div class="main-content">
    <h2>Ticket History</h2>
    <table>
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Passenger ID</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Fare</th>
                <th>Discount</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['ticket_id']) ?></td>
                <td><?= htmlspecialchars($row['passenger_id']) ?></td>
                <td><?= ucfirst(htmlspecialchars($row['origin'])) ?></td>
                <td><?= ucfirst(htmlspecialchars($row['destination'])) ?></td>
                <td>₱<?= number_format($row['fare'], 2) ?></td>
                <td><?= number_format($row['discount'], 2) ?>%</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="conductor_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
