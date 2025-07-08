<?php
session_start();
session_destroy();
header('Location: publico/login.php');
exit;
?>
