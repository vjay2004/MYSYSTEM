<?php
// Include your database connection file
include 'connect.php';

// Start session to store login info
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to check guest by email + password
    $stmt = $conn->prepare("SELECT GuestID, FullName, Email FROM Guest WHERE Email = ? AND Password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Store session variables
        $_SESSION['guest_id']   = $row['GuestID'];
        $_SESSION['guest_name'] = $row['FullName'];
        $_SESSION['guest_email'] = $row['Email'];

        echo "✅ Login successful! Welcome, " . $row['FullName'];
        // Redirect to dashboard (optional)
        header("Location: dashboard_guest.php");
        exit;
    } else {
        echo "❌ Invalid email or password.";
    }

    $stmt->close();
}
$conn->close();
?>

<!-- Login Form -->
<form method="POST" action="">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>
