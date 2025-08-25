<?php
session_start();
include 'connect.php';

// Check if guest is logged in
if (!isset($_SESSION['guest_id'])) {
    header("Location: login.php");
    exit;
}

$guest_id = $_SESSION['guest_id'];

// --- Fetch Guest Info ---
$stmt = $conn->prepare("SELECT FullName, Email, ContactNumber, Address 
                        FROM Guest WHERE GuestID = ?");
$stmt->bind_param("i", $guest_id);
$stmt->execute();
$result = $stmt->get_result();
$guest = $result->fetch_assoc();
$stmt->close();

// --- Fetch Room Reservations ---
$sqlRoom = "SELECT r.ReservationID, rm.RoomNumber, r.CheckInDate, r.CheckOutDate, r.TotalPrice, r.Status
            FROM RoomReservation r
            JOIN Room rm ON r.RoomID = rm.RoomID
            WHERE r.GuestID = ?";
$stmtRoom = $conn->prepare($sqlRoom);
$stmtRoom->bind_param("i", $guest_id);
$stmtRoom->execute();
$roomReservations = $stmtRoom->get_result();
$stmtRoom->close();

// --- Fetch Event Reservations ---
$sqlEvent = "SELECT e.EventID, h.HallName, e.EventDate, e.StartTime, e.EndTime, e.TotalPrice, e.Status
             FROM EventReservation e
             JOIN FunctionHall h ON e.HallID = h.HallID
             WHERE e.GuestID = ?";
$stmtEvent = $conn->prepare($sqlEvent);
$stmtEvent->bind_param("i", $guest_id);
$stmtEvent->execute();
$eventReservations = $stmtEvent->get_result();
$stmtEvent->close();

// --- Fetch Payments ---
$sqlPayment = "SELECT PaymentID, ReservationType, ReservationRefID, AmountPaid, PaymentDate, PaymentMethod, Status
               FROM Payment 
               WHERE ReservationRefID IN (
                    SELECT ReservationID FROM RoomReservation WHERE GuestID = ?
                    UNION
                    SELECT EventID FROM EventReservation WHERE GuestID = ?
               )";
$stmtPayment = $conn->prepare($sqlPayment);
$stmtPayment->bind_param("ii", $guest_id, $guest_id);
$stmtPayment->execute();
$payments = $stmtPayment->get_result();
$stmtPayment->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Guest Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            padding: 20px;
        }
        .dashboard {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #2c3e50;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }
        p {
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background: #34495e;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            background: #e74c3c;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }
        .logout a:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Welcome, <?php echo htmlspecialchars($guest['FullName']); ?>!</h2>

        <p><strong>Email:</strong> <?php echo htmlspecialchars($guest['Email']); ?></p>
        <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($guest['ContactNumber']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($guest['Address']); ?></p>

        <h3>Room Reservations</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Room</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Total Price</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $roomReservations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['ReservationID']; ?></td>
                    <td><?php echo $row['RoomNumber']; ?></td>
                    <td><?php echo $row['CheckInDate']; ?></td>
                    <td><?php echo $row['CheckOutDate']; ?></td>
                    <td><?php echo $row['TotalPrice']; ?></td>
                    <td><?php echo $row['Status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3>Event Reservations</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Hall</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Total Price</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $eventReservations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['EventID']; ?></td>
                    <td><?php echo $row['HallName']; ?></td>
                    <td><?php echo $row['EventDate']; ?></td>
                    <td><?php echo $row['StartTime']; ?></td>
                    <td><?php echo $row['EndTime']; ?></td>
                    <td><?php echo $row['TotalPrice']; ?></td>
                    <td><?php echo $row['Status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3>Payments</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Method</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $payments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['PaymentID']; ?></td>
                    <td><?php echo $row['ReservationType']; ?></td>
                    <td><?php echo $row['ReservationRefID']; ?></td>
                    <td><?php echo $row['AmountPaid']; ?></td>
                    <td><?php echo $row['PaymentDate']; ?></td>
                    <td><?php echo $row['PaymentMethod']; ?></td>
                    <td><?php echo $row['Status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
