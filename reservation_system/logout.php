<?php
session_start();
session_unset();
session_destroy();

// Redirect to login
header("Location: login_guest.php");
exit;
?>
