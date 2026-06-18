<?php
/**
 * Admin Logout - Past Times Clothing Store
 */
session_start();
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
header("Location: login.php");
exit();
?>
