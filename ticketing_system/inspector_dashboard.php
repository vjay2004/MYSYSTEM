<?php
session_start();
include('connect.php');

// Check if the inspector is logged in
$inspector_id = $_SESSION['inspector_id'] ?? null;

if (!$inspector_id) {
    header('Location: inspector_login.php');
    exit();
}

// Handle refund decision
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inspect_refund'])) {
    $refund_id = $_POST['refund_id'];
    $status = $_POST['status'];
    $decision_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE refund_request SET status=?, decision_date=?, inspector_id=? WHERE refund_id=?");
    $stmt->bind_param("ssii", $status, $decision_date, $inspector_id, $refund_id);
    $stmt->execute();
    $stmt->close();

    // Set session flag for SweetAlert
    $_SESSION['decision_success'] = true;

    // Redirect to avoid resubmission on refresh
    header("Location: inspector_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inspector Dashboard</title>

    <!-- ✅ SweetAlert2 & jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* [Your existing CSS here] */
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
        .welcome {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        h2 {
            color: #f59e0b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: orange;
            color: white;
        }
        input[type="number"], select {
            padding: 8px;
            margin-top: 5px;
            width: 200px;
        }
        button {
            padding: 10px 18px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        form {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <img src="logo.png" alt="Logo" class="logo">
    <h2>Donsal's Express Corporation</h2>
    <a href="inspector_dashboard.php">Dashboard</a>
    <a href="inspector_report.php">Inspector Report</a>
    <a href="inspector_login.php">Logout</a>
</div>

<div class="main-content">
    <?php if (isset($_SESSION['inspector_name'])): ?>
        <div class="welcome">Welcome, Inspector <?= htmlspecialchars($_SESSION['inspector_name']) ?></div>
    <?php endif; ?>

    <h3>Review Refund Request</h3>
    <form method="POST">
        <label>Refund ID:</label><br>
        <input type="number" name="refund_id" required><br>
        <label>Status:</label><br>
        <select name="status" required>
            <option value="Approved">Approve</option>
            <option value="Rejected">Reject</option>
        </select><br>
        <button type="submit" name="inspect_refund">Submit Decision</button>
    </form>

    <h3>Pending Refund Requests</h3>
    <table>
        <tr>
            <th>Refund ID</th>
            <th>Ticket ID</th>
            <th>Conductor</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Request Date</th>
            <th>Action</th>
        </tr>
        <?php
        $result = $conn->query("SELECT r.refund_id, r.ticket_id, r.reason, r.status, r.request_date, c.full_name as conductor_name 
                                FROM refund_request r 
                                JOIN conductor c ON r.conductor_id = c.conductor_id
                                WHERE r.status = 'Pending'");

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['refund_id']}</td>
                    <td>{$row['ticket_id']}</td>
                    <td>{$row['conductor_name']}</td>
                    <td>{$row['reason']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['request_date']}</td>
                    <td>
                        <form method='POST'>
                            <input type='hidden' name='refund_id' value='{$row['refund_id']}'>
                            <select name='status' required>
                                <option value='Approved'>Approve</option>
                                <option value='Rejected'>Reject</option>
                            </select>
                            <button type='submit' name='inspect_refund'>Submit</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
    </table>
</div>

<!-- ✅ SweetAlert success popup -->
<?php if (isset($_SESSION['decision_success'])): ?>
    <script>
        $(document).ready(function () {
            Swal.fire({
                title: 'Success!',
                text: 'Refund decision submitted successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php unset($_SESSION['decision_success']); ?>
<?php endif; ?>

</body>
</html>
