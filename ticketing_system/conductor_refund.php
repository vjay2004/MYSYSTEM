<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('connect.php');

// Check if the conductor is logged in
$conductor_id = $_SESSION['conductor_id'] ?? null;
if (!$conductor_id) {
    header('Location: conductor_login.php');
    exit();
}

// Handle Refund Request Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_refund'])) {
    $ticket_id = $_POST['ticket_id'];
    $reason = $_POST['reason'];
    $request_date = date('Y-m-d H:i:s');
    $status = "Pending";

    $stmt = $conn->prepare("INSERT INTO refund_request (conductor_id, ticket_id, reason, status, request_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $conductor_id, $ticket_id, $reason, $status, $request_date);
    $stmt->execute();
    $stmt->close();

    $conn->query("UPDATE tickets SET refund_request='Yes' WHERE ticket_id=$ticket_id");

    $message = "Refund request submitted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Refund Requests</title>

    <!-- JS libraries added -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        h2, h3 {
            color: orange;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #f59e0b;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #e68900;
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
            background-color: #f59e0b;
            color: #fff;
        }

        tr:last-child td {
            border-bottom: none;
        }

        p {
            color: green;
            font-weight: bold;
        }

        a.back-link {
            display: inline-block;
            margin-top: 20px;
            color: #f59e0b;
            text-decoration: none;
        }

        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="text-align: center;">
        <img src="logo.png" alt="Donsal's Express Logo" style="width: 100px; margin-bottom: 10px;">
    </div>
    <h2>Donsal's Express Corporation</h2>
    <a href="conductor_dashboard.php">Dashboard</a>
    <a href="ticket_records.php">Ticket History</a>
    <a href="conductor_refund.php">Refund Ticket</a>
    <a href="conductor_reports.php">Report</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Refund Request</h2>

   <?php if (isset($message)): ?>
<script>
    $(document).ready(function() {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: <?= json_encode($message) ?>,
            confirmButtonColor: '#f59e0b'
        });
    });
</script>
<?php endif; ?>

    <form method="POST">
        <label for="ticket_id">Ticket ID:</label>
        <input type="number" name="ticket_id" id="ticket_id" required>

        <label for="reason">Reason:</label>
        <textarea name="reason" id="reason" rows="4" required></textarea>

        <button type="submit" name="request_refund">Submit Refund Request</button>
    </form>

    <h3>My Refund Requests</h3>
    <table>
        <thead>
            <tr>
                <th>Refund ID</th>
                <th>Ticket ID</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>Decision Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM refund_request WHERE conductor_id = $conductor_id ORDER BY request_date DESC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['refund_id']) ?></td>
                    <td><?= htmlspecialchars($row['ticket_id']) ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['request_date']) ?></td>
                    <td><?= htmlspecialchars($row['decision_date'] ?? 'Pending') ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6">No refund requests found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="conductor_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
