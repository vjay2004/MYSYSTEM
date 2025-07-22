<?php
session_start();
include('connect.php');

// Check if conductor is logged in
$conductor_id = $_SESSION['conductor_id'] ?? null;
$inspector_id = $_SESSION['inspector_id'] ?? null;

if (!$conductor_id && !$inspector_id) {
    header('Location: conductor_login.php');
    exit();
}

// Fare map array
$fare_map = [
    'buruun' => [
        'timoga' => 14,
        'agos' => 14,
        'fuentes' => 14,
        'nunucan' => 14,
        'suarez' => 16,
        'terminal' => 17,
        'tominobo' => 18,
        'camague' => 20,
        'tubod' => 23,
        'mayahay' => 25,
        'city' => 25
    ],
    'timoga' => [
        'buruun' => 14,
        'agos' => 14,
        'fuentes' => 14,
        'nunucan' => 14,
        'suarez' => 14,
        'terminal' => 16,
        'tominobo' => 17,
        'camague' => 18,
        'tubod' => 20,
        'mayahay' => 23,
        'city' => 25
    ],
    'agos' => [
        'buruun' => 14,
        'timoga' => 14,
        'fuentes' => 14,
        'nunucan' => 14,
        'suarez' => 14,
        'terminal' => 14,
        'tominobo' => 16,
        'camague' => 17,
        'tubod' => 18,
        'mayahay' => 20,
        'city' => 23
    ],
    'fuentes' => [
        'buruun' => 14,
        'timoga' => 14,
        'agos' => 14,
        'nunucan' => 14,
        'suarez' => 14,
        'terminal' => 14,
        'tominobo' => 14,
        'camague' => 16,
        'tubod' => 17,
        'mayahay' => 18,
        'city' => 22
    ],
    'nunucan' => [
        'buruun' => 14,
        'timoga' => 14,
        'agos' => 14,
        'fuentes' => 14,
        'suarez' => 14,
        'terminal' => 14,
        'tominobo' => 14,
        'camague' => 16,
        'tubod' => 17,
        'mayahay' => 18,
        'city' => 20
    ],
    'suarez' => [
        'buruun' => 16,
        'timoga' => 14,
        'agos' => 14,
        'fuentes' => 14,
        'nunucan' => 14,
        'terminal' => 14,
        'tominobo' => 14,
        'camague' => 14,
        'tubod' => 16,
        'mayahay' => 17,
        'city' => 18
    ],
    'terminal' => [
        'buruun' => 17,
        'timoga' => 16,
        'agos' => 14,
        'fuentes' => 14,
        'nunucan' => 14,
        'suarez' => 14,
        'tominobo' => 14,
        'camague' => 14,
        'tubod' => 14,
        'mayahay' => 16,
        'city' => 17
    ],
    'tominobo' => [
        'buruun' => 18,
        'timoga' => 17,
        'agos' => 16,
        'fuentes' => 14,
        'nunucan' => 14,
        'suarez' => 14,
        'terminal' => 14,
        'camague' => 14,
        'tubod' => 14,
        'mayahay' => 14,
        'city' => 16
    ],
    'camague' => [
        'buruun' => 20,
        'timoga' => 18,
        'agos' => 17,
        'fuentes' => 16,
        'nunucan' => 14,
        'suarez' => 14,
        'terminal' => 14,
        'tominobo' => 14,
        'tubod' => 14,
        'mayahay' => 14,
        'city' => 15
    ],
    'tubod' => [
        'buruun' => 23,
        'timoga' => 20,
        'agos' => 18,
        'fuentes' => 17,
        'nunucan' => 16,
        'suarez' => 14,
        'terminal' => 14,
        'tominobo' => 14,
        'camague' => 14,
        'mayahay' => 14,
        'city' => 14
    ],
    'mayahay' => [
        'buruun' => 25,
        'timoga' => 23,
        'agos' => 20,
        'fuentes' => 18,
        'nunucan' => 18,
        'suarez' => 17,
        'terminal' => 16,
        'tominobo' => 14,
        'camague' => 14,
        'tubod' => 14,
        'city' => 14
    ],
    'city' => [
        'buruun' => 25,
        'timoga' => 25,
        'agos' => 23,
        'fuentes' => 22,
        'nunucan' => 20,
        'suarez' => 18,
        'terminal' => 17,
        'tominobo' => 16,
        'camague' => 15,
        'tubod' => 14,
        'mayahay' => 14
    ]

];
// Generate unique passenger ID
function generatePassengerId($conn) {
    $result = $conn->query("SELECT passenger_id FROM tickets ORDER BY ticket_id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastId = intval(substr($row['passenger_id'], 1));
        return 'P' . ($lastId + 1);
    }
    return 'P1';
}

// Handle Ticket Creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_ticket'])) {
    $passenger_id = generatePassengerId($conn);
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $discount_applicable = $_POST['discount'] === 'Yes';
    $fare = floatval($fare_map[$origin][$destination]);
    $discount = $discount_applicable ? $fare * 0.2 : 0.00;
    $final_fare = $fare - $discount;

    $stmt = $conn->prepare("INSERT INTO tickets (passenger_id, conductor_id, origin, destination, fare, discount, date_issued) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sissdd", $passenger_id, $conductor_id, $origin, $destination, $final_fare, $discount);
    $stmt->execute();
    $ticket_id = $stmt->insert_id;
    $stmt->close();

    // Generate/Update daily report
    $report_date = date('Y-m-d');
    $check_stmt = $conn->prepare("SELECT report_id FROM report WHERE conductor_id = ? AND report_date = ?");
    $check_stmt->bind_param("is", $conductor_id, $report_date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        $insert_stmt = $conn->prepare("INSERT INTO report (conductor_id, total_tickets, total_earnings, report_date) VALUES (?, 1, ?, ?)");
        $insert_stmt->bind_param("ids", $conductor_id, $final_fare, $report_date);
        $insert_stmt->execute();
        $insert_stmt->close();
    } else {
        $update_stmt = $conn->prepare("UPDATE report SET total_tickets = total_tickets + 1, total_earnings = total_earnings + ? WHERE conductor_id = ? AND report_date = ?");
        $update_stmt->bind_param("dis", $final_fare, $conductor_id, $report_date);
        $update_stmt->execute();
        $update_stmt->close();
    }
    $check_stmt->close();

    // Store ticket info for receipt
    $_SESSION['ticket_receipt'] = [
        'ticket_id' => $ticket_id,
        'passenger_id' => $passenger_id,
        'origin' => ucfirst($origin),
        'destination' => ucfirst($destination),
        'fare' => number_format($fare, 2),
        'discount' => number_format($discount, 2),
        'final_fare' => number_format($final_fare, 2),
        'date' => date('Y-m-d H:i:s'),
        'conductor_name' => $_SESSION['conductor_name'] ?? 'Conductor'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Conductor Dashboard</title>
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

        form {
            margin-bottom: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        select, input[type="text"] {
            padding: 8px;
            width: 100%;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
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

        /* Receipt Modal Styles */
        #receiptModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            width: 350px;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 1000;
            border: 2px solid #f59e0b;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #f59e0b;
            padding-bottom: 15px;
        }

        .receipt-header img {
            width: 80px;
            margin-bottom: 10px;
        }

        .receipt-header h2 {
            margin: 5px 0;
            color: #f59e0b;
            font-size: 22px;
        }

        .receipt-header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }

        .receipt-body {
            margin-bottom: 20px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #eee;
        }

        .receipt-row strong {
            color: #333;
        }

        .receipt-row.total {
            font-weight: bold;
            border-top: 2px dashed #f59e0b;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 18px;
        }

        .receipt-footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
            border-top: 2px dashed #f59e0b;
            padding-top: 15px;
        }

        .receipt-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .receipt-actions button {
            width: 48%;
            padding: 10px;
        }

        .btn-print {
            background: #4CAF50;
        }

        .btn-print:hover {
            background: #3e8e41;
        }

        .btn-close {
            background: #f44336;
        }

        .btn-close:hover {
            background: #d32f2f;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #receiptModal, #receiptModal * {
                visibility: visible;
            }
            #receiptModal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                box-shadow: none;
                border: none;
            }
            .receipt-actions {
                display: none;
            }
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
    <?php if (isset($_SESSION['conductor_name'])): ?>
        <div style="text-align: left; padding: 10px 0; font-weight: bold; font-size: 30px;">
            Welcome, Conductor <?= htmlspecialchars($_SESSION['conductor_name']) ?>
        </div>
    <?php endif; ?>
    
    <h1>Dashboard</h1>
    
    <!-- Daily Summary Cards -->
    <div class="summary-card">
        <?php
        $today = date('Y-m-d');
        $today_stmt = $conn->prepare("SELECT total_tickets, total_earnings FROM report WHERE conductor_id = ? AND report_date = ?");
        $today_stmt->bind_param("is", $conductor_id, $today);
        $today_stmt->execute();
        $today_result = $today_stmt->get_result();
        $today_data = $today_result->fetch_assoc();
        $today_stmt->close();
        ?>
        <div class="card">
            <h3>Today's Tickets</h3>
            <p><?= $today_data['total_tickets'] ?? 0 ?></p>
        </div>
        <div class="card">
            <h3>Today's Earnings</h3>
            <p>₱<?= isset($today_data['total_earnings']) ? number_format($today_data['total_earnings'], 2) : '0.00' ?></p>
        </div>
        <div class="card">
            <h3>All-Time Tickets</h3>
            <p>
                <?php 
                $all_time_stmt = $conn->prepare("SELECT SUM(total_tickets) as total FROM report WHERE conductor_id = ?");
                $all_time_stmt->bind_param("i", $conductor_id);
                $all_time_stmt->execute();
                $all_time_result = $all_time_stmt->get_result();
                $all_time_data = $all_time_result->fetch_assoc();
                $all_time_stmt->close();
                echo $all_time_data['total'] ?? 0;
                ?>
            </p>
        </div>
    </div>

    <?php if ($conductor_id): ?>
    <h3>Create Ticket</h3>
    <form method="POST">
        <label for="origin">Origin:</label>
        <select name="origin" id="origin_ticket" required>
            <?php foreach (array_keys($fare_map) as $origin): ?>
                <option value="<?= $origin ?>"><?= ucfirst($origin) ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="destination">Destination:</label>
        <select name="destination" id="destination_ticket" required></select><br>

        <label for="discount">Apply Discount?</label>
        <select name="discount" id="discount_ticket" required>
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select><br>

        <label for="fare">Fare:</label>
        <input type="text" name="fare" id="fare_ticket" readonly><br>

        <button type="submit" name="create_ticket">Print Ticket</button>
    </form>
    <?php endif; ?>

    <!-- Receipt Modal -->
    <div id="receiptModal">
        <div class="receipt-header">
            <img src="logo.png" alt="Company Logo">
            <h2>DONSAL'S EXPRESS</h2>
            <p>Official Ticket Receipt</p>
        </div>
        
        <div class="receipt-body">
            <div class="receipt-row">
                <span>Ticket ID:</span>
                <strong id="r_ticket_id"></strong>
            </div>
            <div class="receipt-row">
                <span>Passenger ID:</span>
                <strong id="r_passenger"></strong>
            </div>
            <div class="receipt-row">
                <span>Conductor:</span>
                <strong id="r_conductor"></strong>
            </div>
            <div class="receipt-row">
                <span>Date:</span>
                <strong id="r_date"></strong>
            </div>
            <div class="receipt-row">
                <span>Route:</span>
                <strong id="r_route"></strong>
            </div>
            <div class="receipt-row">
                <span>Base Fare:</span>
                <strong>₱<span id="r_fare"></span></strong>
            </div>
            <div class="receipt-row">
                <span>Discount (20%):</span>
                <strong>₱<span id="r_discount"></span></strong>
            </div>
            <div class="receipt-row total">
                <span>Total Fare:</span>
                <strong>₱<span id="r_total"></span></strong>
            </div>
        </div>
        
        <div class="receipt-footer">
            <p>Thank you for riding with us!</p>
            <p>For refunds, present this receipt to conductor</p>
        </div>
        
        <div class="receipt-actions">
            <button class="btn-print" onclick="printReceipt()">Print Receipt</button>
            <button class="btn-close" onclick="closeReceipt()">Close</button>
        </div>
    </div>
</div>

<script>
    // Fare calculation setup
    const fareMap = <?= json_encode($fare_map) ?>;

    function setupFareCalculator(originId, destinationId, fareId, discountId) {
        const originSelect = document.getElementById(originId);
        const destinationSelect = document.getElementById(destinationId);
        const fareInput = document.getElementById(fareId);
        const discountSelect = document.getElementById(discountId);

        originSelect.addEventListener('change', updateDestinations);
        destinationSelect.addEventListener('change', updateFare);
        discountSelect.addEventListener('change', updateFare);

        function updateDestinations() {
            const origin = originSelect.value;
            destinationSelect.innerHTML = '';
            for (let dest in fareMap[origin]) {
                const opt = document.createElement('option');
                opt.value = dest;
                opt.textContent = dest.charAt(0).toUpperCase() + dest.slice(1);
                destinationSelect.appendChild(opt);
            }
            updateFare();
        }

        function updateFare() {
            const origin = originSelect.value;
            const destination = destinationSelect.value;
            const discount = discountSelect.value;
            if (fareMap[origin] && fareMap[origin][destination]) {
                let fare = fareMap[origin][destination];
                if (discount === 'Yes') {
                    fare *= 0.8;
                }
                fareInput.value = fare.toFixed(2);
            } else {
                fareInput.value = '';
            }
        }

        updateDestinations();
    }

    setupFareCalculator('origin_ticket', 'destination_ticket', 'fare_ticket', 'discount_ticket');

    // Receipt functions
    function printReceipt() {
        window.print();
    }

    function closeReceipt() {
        document.getElementById('receiptModal').style.display = 'none';
    }

    <?php if (isset($_SESSION['ticket_receipt'])): ?>
    // Show receipt with ticket data
    const receipt = <?= json_encode($_SESSION['ticket_receipt']) ?>;
    document.getElementById('r_ticket_id').textContent = receipt.ticket_id;
    document.getElementById('r_passenger').textContent = receipt.passenger_id;
    document.getElementById('r_conductor').textContent = receipt.conductor_name;
    document.getElementById('r_date').textContent = receipt.date;
    document.getElementById('r_route').textContent = `${receipt.origin} to ${receipt.destination}`;
    document.getElementById('r_fare').textContent = receipt.fare;
    document.getElementById('r_discount').textContent = receipt.discount;
    document.getElementById('r_total').textContent = receipt.final_fare;
    
    document.getElementById('receiptModal').style.display = 'block';
    <?php unset($_SESSION['ticket_receipt']); ?>
    <?php endif; ?>
</script>
</body>
</html>