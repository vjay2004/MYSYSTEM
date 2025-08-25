<?php
// Include database connection
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullname'];
    $email    = $_POST['email'];
    $password = $_POST['password']; // stored as plain text (not secure)
    $contact  = $_POST['contact'];
    $address  = $_POST['address'];

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO Guest (FullName, Email, Password, ContactNumber, Address) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullName, $email, $password, $contact, $address);

    if ($stmt->execute()) {
        echo "✅ Registration successful! You can now log in.";
        // Optional: redirect to login page
        // header("Location: login.php");
        exit;
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!-- Registration Form -->
<form method="POST" action="">
    <label>Full Name:</label><br>
    <input type="text" name="fullname" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Contact Number:</label><br>
    <input type="text" name="contact"><br><br>

    <label>Address:</label><br>
    <textarea name="address"></textarea><br><br>

    <button type="submit">Register</button>
</form>
