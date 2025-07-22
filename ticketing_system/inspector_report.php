<?php
session_start();
include('connect.php');

// Check if the inspector is logged in
$inspector_id = $_SESSION['inspector_id'] ?? null;

if (!$inspector_id) {
    header('Location: inspector_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inspector Report</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 220px;
            background: #000;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            height: auto;
            display: block;
            margin: 0 auto 15px auto;
            border-radius: 10px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: orange;
            color: black;
        }

        .main-content {
            flex: 1;
            background-color: #f4f4f4;
            padding: 30px;
            overflow-y: auto;
        }

        h2 {
            color: #f59e0b;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 6px;
            box-shadow: 0 0 5px #ccc;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: orange;
            color: white;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <img src="logo.png" alt="Logo" class="logo">
    <h2>Donsal's Express Corporation</h2>
    <a href="inspector_dashboard.php">Dashboard</a>
    <a href="inspector_report.php">Inspector Report</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Inspector Refund Decision Report</h2>
    <table>
        <thead>
            <tr>
                <th>Refund ID</th>
                <th>Ticket ID</th>
                <th>Conductor</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>Decision Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("SELECT r.refund_id, r.ticket_id, r.reason, r.status, r.request_date, r.decision_date, c.full_name 
                                    FROM refund_request r
                                    JOIN conductor c ON r.conductor_id = c.conductor_id
                                    WHERE r.status IN ('Approved', 'Rejected')
                                    ORDER BY r.decision_date DESC");
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['refund_id']) ?></td>
                    <td><?= htmlspecialchars($row['ticket_id']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['request_date']) ?></td>
                    <td><?= htmlspecialchars($row['decision_date']) ?></td>
                </tr>
            <?php endwhile; ?>
            <?php $stmt->close(); ?>
        </tbody>
    </table>
</div>

</body>
</html>
